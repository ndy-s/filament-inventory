<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesResource\Pages;
use App\Models\Sales;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SalesResource extends Resource
{
    protected static ?string $model = Sales::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation.sales');
    }

    public static function getNavigationSort(): ?int
    {
        return 8;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.sales.plural');
    }

    public static function getLabel(): string
    {
        return __('filament.resources.sales.singular');
    }

    public static function form(Form $form): Form
    {
        return $form->schema(Sales::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('filament.resources.sales.fields.code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__('filament.resources.sales.fields.customer'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('filament.resources.sales.fields.date'))
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('sales_items')
                    ->label(__('filament.resources.sales.fields.total'))
                    ->getStateUsing(function ($record) {
                        $total = $record->salesItems->sum(function ($item) {
                            return $item->quantity * $item->price_per_unit;
                        });

                        $totalDiscount = $record->salesItems->sum(function ($item) {
                            return $item->discount ?? 0;
                        });

                        $totalAfterDiscount = $total - $totalDiscount;

                        return 'IDR ' . number_format($totalAfterDiscount, 2, ',', '.');
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_locked')
                    ->label(__('filament.resources.sales.fields.lock_status'))
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-lock-open',
                        '1' => 'heroicon-o-lock-closed',
                    })
                    ->boolean()
                    ->alignCenter()
                    ->sortable()
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
                Tables\Actions\Action::make('toggleLock')
                    ->label(__('filament.resources.sales.actions.toggle_lock'))
                    ->icon('heroicon-o-lock-closed')
                    ->action(function ($record) {
                        $record->update([
                            'is_locked' => !$record->is_locked,
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('filament.resources.sales.actions.confirm_lock_unlock'))
                    ->modalDescription(__('filament.resources.sales.actions.lock_unlock_description')),
                Tables\Actions\Action::make('details')
                    ->label(__('filament.general.fields.details'))
                    ->icon('heroicon-o-eye')
                    ->modalHeading(__('filament.resources.sales.fields.details_heading'))
                    ->modalWidth('6xl')
                    ->modalSubmitAction(false)
                    ->modalContent(fn ($record) => view('filament.resources.sales.sales-details', [
                        'sales' => $record,
                        'salesItems' => $record->salesItems,
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(fn ($query) => $query->orderBy('date', 'desc')->orderBy('created_at', 'desc'));
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSales::route('/create'),
            'edit' => Pages\EditSales::route('/{record}/edit'),
        ];
    }
}
