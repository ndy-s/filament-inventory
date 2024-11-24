<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Models\Purchase;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation.purchases');
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.purchase.plural');
    }

    public static function getLabel(): string
    {
        return __('filament.resources.purchase.singular');
    }

    public static function form(Form $form): Form
    {
        return $form->schema(Purchase::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('filament.resources.purchase.fields.code'))
                    ->searchable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label(__('filament.resources.purchase.fields.supplier_id'))
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('filament.resources.purchase.fields.date'))
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('purchase_items')
                    ->label('Total Pembelian')
                    ->getStateUsing(function ($record) {
                        $total = $record->purchaseItems->sum(function ($item) {
                            return $item->quantity * $item->price_per_unit;
                        });

                        return 'IDR ' . number_format($total, 2, ',', '.');
                    })
                    ->sortable(),
                Tables\Columns\ImageColumn::make('invoice_image')
                    ->label(__('filament.resources.purchase.fields.invoice_image'))
                    ->disk('public')
                    ->width(100)
                    ->height(100)
                    ->url(function ($record) {
                        if ($record->invoice_image) {
                            return asset('storage/' . $record->invoice_image);
                        }

                        return null;
                    })
                    ->openUrlInNewTab(function ($record) {
                        return (bool)$record->invoice_image;
                    }),
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
