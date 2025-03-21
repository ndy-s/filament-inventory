<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation.products_inventory');
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.product.plural');
    }

    public static function getLabel(): string
    {
        return __('filament.resources.product.singular');
    }

    public static function form(Form $form): Form
    {
        return $form->schema(Product::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.resources.product.fields.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('baseUnit.unit_name')
                    ->label(__('filament.resources.product.fields.base_unit'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('other_units')
                    ->label(__('filament.resources.product.fields.other_units'))
                    ->getStateUsing(function ($record) {
                        $conversions = $record->units()
                            ->when($record->baseUnit, function ($query) use ($record) {
                                return $query->where('unit_name', '!=', $record->baseUnit->unit_name);
                            })
                            ->orderBy('conversion_factor', 'asc')
                            ->get();

                        return $conversions->map(function ($conversion) {
                            return $conversion->unit_name;
                        })->implode(', ');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label(__('filament.resources.product.fields.notes'))
                    ->html()
                    ->limit(50)
                    ->searchable(),
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
            ->defaultSort('name', 'asc');
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
