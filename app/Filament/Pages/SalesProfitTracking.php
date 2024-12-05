<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesProfitTracking extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $title = 'Sales Revenue and Profit Tracking';
    protected static string $view = 'filament.pages.sales-profit-tracking';

    public array $revenueData = [];
    public string $timeframe = 'day';
    public array $timeframes = ['day', 'week', 'month'];

    public function mount(): void
    {
        // Get timeframe from the request (default to 'day')
        $this->timeframe = request('timeframe', 'day');
        $this->revenueData = $this->getRevenueAndProfitData($this->timeframe);
    }

    public function updatedTimeframe($value): void
    {
        $this->revenueData = $this->getRevenueAndProfitData($value);
    }

    public function getRevenueAndProfitData(string $timeframe): array
    {
        // Determine date grouping based on timeframe
        $dateFormat = match($timeframe) {
            'week' => '%U %Y', // Year and week number
            'month' => '%M %Y', // Year and month
            default => '%d %M %Y', // Full date
        };

        // Fetch sales data grouped by date
        $sales = DB::table('sales')
            ->join('sales_items', 'sales.id', '=', 'sales_items.sales_id')
            ->join('products', 'sales_items.product_id', '=', 'products.id')
            ->join('units as sales_units', 'sales_items.unit_id', '=', 'sales_units.id')
            ->select(
                DB::raw('DATE_FORMAT(sales.date, "' . $dateFormat . '") as formatted_date'),
                'products.name as product_name',
                DB::raw('SUM(sales_items.quantity * sales_units.conversion_factor) as total_quantity_in_base_unit'),
                DB::raw('SUM((sales_items.quantity * sales_items.price_per_unit) - sales_items.discount) as revenue')
            )
            ->groupBy('formatted_date', 'products.name')
            ->orderBy('sales.date', 'desc')
            ->get();

        // Fetch purchase data grouped by product
        $purchases = DB::table('purchases')
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

        // Index purchase data for quick lookup
        $purchaseData = [];
        foreach ($purchases as $purchase) {
            $purchaseData[$purchase->product_name] = [
                'average_price_in_base_unit' => $purchase->average_price_in_base_unit ?? 0,
            ];
        }

        // Calculate revenue, COGS, and profit
        $aggregatedData = [];
        foreach ($sales as $sale) {
            $date = $sale->formatted_date;
            $productName = $sale->product_name;
            $quantitySold = $sale->total_quantity_in_base_unit;
            $revenue = $sale->revenue;

            // Get average purchase price
            $averagePrice = $purchaseData[$productName]['average_price_in_base_unit'] ?? 0;
            $cogs = $quantitySold * $averagePrice;
            $profit = $revenue - $cogs;

            // Aggregate by date
            $aggregatedData[$date]['summary']['revenue'] = ($aggregatedData[$date]['summary']['revenue'] ?? 0) + $revenue;
            $aggregatedData[$date]['summary']['cogs'] = ($aggregatedData[$date]['summary']['cogs'] ?? 0) + $cogs;
            $aggregatedData[$date]['summary']['profit'] = ($aggregatedData[$date]['summary']['profit'] ?? 0) + $profit;

            // Store item details
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
