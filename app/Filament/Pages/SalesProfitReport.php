<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesProfitReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Laporan Penjualan';
    protected static ?string $title = 'Analisis Profit Penjualan';
    protected static string $view = 'filament.pages.sales-profit-details';

    public array $revenueData = [];

    public function mount(): void
    {
        $this->loadRevenueData();
    }

    protected function loadRevenueData(): void
    {
        $sales = $this->fetchSalesData();
        $purchases = $this->fetchPurchaseData();
        $this->revenueData = $this->aggregateRevenueData($sales, $purchases);
    }

    protected function fetchSalesData(): Collection
    {
        return DB::table('sales')
            ->join('sales_items', 'sales.id', '=', 'sales_items.sales_id')
            ->join('products', 'sales_items.product_id', '=', 'products.id')
            ->join('units as sales_units', 'sales_items.unit_id', '=', 'sales_units.id')
            ->select(
                DB::raw('DATE_FORMAT(sales.date, "%d %M %Y") as formatted_date'),
                'products.name as product_name',
                DB::raw('SUM(sales_items.quantity * sales_units.conversion_factor) as total_quantity_in_base_unit'),
                DB::raw('SUM((sales_items.quantity * sales_items.price_per_unit) - sales_items.discount) as revenue')
            )
            ->groupBy('formatted_date', 'products.name')
            ->orderBy('sales.date', 'desc')
            ->get();
    }

    protected function fetchPurchaseData(): Collection
    {
        return DB::table('purchases')
            ->join('purchase_items', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->join('units as purchase_units', 'purchase_items.unit_id', '=', 'purchase_units.id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(purchase_items.quantity * purchase_units.conversion_factor) as total_quantity_in_base_unit'),
                DB::raw('SUM((purchase_items.quantity * purchase_items.price_per_unit) - purchase_items.discount) / NULLIF(SUM(purchase_items.quantity * purchase_units.conversion_factor), 0) as average_price_in_base_unit')
            )
            ->groupBy('products.name')
            ->get();
    }

    protected function aggregateRevenueData($sales, $purchases): array
    {
        $purchaseData = $purchases->keyBy('product_name')
            ->map(fn($purchase) => [
                'average_price_in_base_unit' => $purchase->average_price_in_base_unit ?? 0,
            ])
            ->toArray();

        $aggregatedData = [];

        foreach ($sales as $sale) {
            $date = $sale->formatted_date;
            $productName = $sale->product_name;
            $quantitySold = $sale->total_quantity_in_base_unit;
            $revenue = $sale->revenue;

            $averagePrice = $purchaseData[$productName]['average_price_in_base_unit'] ?? 0;
            $cogs = $quantitySold * $averagePrice;
            $profit = $revenue - $cogs;

            // Aggregate summary data
            $aggregatedData[$date]['summary']['revenue'] =
                ($aggregatedData[$date]['summary']['revenue'] ?? 0) + $revenue;
            $aggregatedData[$date]['summary']['cogs'] =
                ($aggregatedData[$date]['summary']['cogs'] ?? 0) + $cogs;
            $aggregatedData[$date]['summary']['profit'] =
                ($aggregatedData[$date]['summary']['profit'] ?? 0) + $profit;

            // Collect detailed product information
            $aggregatedData[$date]['details'][] = [
                'product_name' => $productName,
                'quantity_sold_in_base_unit' => $quantitySold,
                'revenue' => $revenue,
                'cogs' => $cogs,
                'profit' => $profit,
            ];
        }

        return $aggregatedData;
    }
}
