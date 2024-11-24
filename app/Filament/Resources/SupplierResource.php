<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation.purchases');
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.supplier.plural');
    }

    public static function getLabel(): string
    {
        return __('filament.resources.supplier.singular');
    }

    public static function form(Form $form): Form
    {
        return $form->schema(Supplier::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.resources.supplier.fields.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->label(__('filament.resources.supplier.fields.contact_person'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('filament.resources.supplier.fields.phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament.resources.supplier.fields.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label(__('filament.resources.supplier.fields.address'))
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}