<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Filament\Resources\InventoryResource\RelationManagers;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    ->label(__('filament.resources.product.fields.name'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('filament.resources.inventory.fields.quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.baseUnit.unit_name')
                    ->label(__('filament.resources.product.fields.base_unit'))
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
                //
            ])
            ->bulkActions([
                //
            ]);
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
