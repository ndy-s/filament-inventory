<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation.products_inventory');
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.inventory.plural');
    }

    public static function getLabel(): string
    {
        return __('filament.resources.inventory.singular');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('filament.resources.inventory.fields.product'))
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('filament.resources.inventory.fields.quantity'))
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.baseUnit.unit_name')
                    ->label(__('filament.resources.product.fields.base_unit'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_conversions')
                    ->label(__('filament.resources.inventory.fields.other_units'))
                    ->getStateUsing(function ($record) {
                        $conversions = $record->product->units()
                            ->orderBy('conversion_factor', 'asc')
                            ->get();

                        return $conversions->map(function ($conversion) use ($record) {
                            if ($conversion->unit_name !== $record->product->baseUnit->unit_name && $conversion->conversion_factor > 0) {
                                $convertedQuantity = $record->quantity / $conversion->conversion_factor;
                                $convertedQuantity = round($convertedQuantity, 2);

                                return $convertedQuantity . ' ' . $conversion->unit_name;
                            }

                            return '';
                        })->filter()->implode(', ');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.general.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament.general.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('viewHistory')
                    ->label('Riwayat Transaksi')
                    ->icon('heroicon-o-clock')
                    ->modalHeading(fn ($record) => __('Riwayat Transaksi Produk: ') . $record->product->name)
                    ->modalWidth('6xl')
                    ->modalSubmitAction(false)
                    ->modalContent(function ($record) {
                        $purchaseItems = $record->product->purchaseItems()
                            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                            ->select('purchase_items.*', 'purchases.date as transaction_date', 'purchases.code as code', DB::raw("'Purchase' as type"))
                            ->get();

                        $salesItems = $record->product->salesItems()
                            ->join('sales', 'sales_items.sales_id', '=', 'sales.id')
                            ->select('sales_items.*', 'sales.date as transaction_date', 'sales.code as code', DB::raw("'Sale' as type"))
                            ->get();

                        $history = collect()->concat($purchaseItems)->concat($salesItems)->sortByDesc('created_at');

                        return view('filament.resources.inventory.history-modal', [
                            'history' => $history,
                        ]);
                    })
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('product.name', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
        ];
    }
}
