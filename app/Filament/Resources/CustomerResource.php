<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation.sales');
    }

    public static function getNavigationSort(): ?int
    {
        return 7;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.customer.plural');
    }

    public static function getLabel(): string
    {
        return __('filament.resources.customer.singular');
    }

    public static function form(Form $form): Form
    {
        return $form->schema(Customer::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.resources.customer.fields.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->label(__('filament.resources.customer.fields.contact_person'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('filament.resources.customer.fields.phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament.resources.customer.fields.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label(__('filament.resources.customer.fields.address'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label(__('filament.resources.customer.fields.latitude'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->label(__('filament.resources.customer.fields.longitude'))
                    ->numeric()
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
