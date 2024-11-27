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
                    ->label(__('filament.resources.purchase.fields.supplier'))
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('filament.resources.purchase.fields.date'))
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('purchase_items')
                    ->label(__('filament.resources.purchase.fields.total'))
                    ->getStateUsing(function ($record) {
                        $total = $record->purchaseItems->sum(function ($item) {
                            return $item->quantity * $item->price_per_unit;
                        });

                        $totalDiscount = $record->purchaseItems->sum(function ($item) {
                            return $item->discount ?? 0;
                        });

                        $totalAfterDiscount = $total - $totalDiscount;

                        return 'IDR ' . number_format($totalAfterDiscount, 2, ',', '.');
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
                Tables\Columns\IconColumn::make('is_locked')
                    ->label(__('filament.resources.purchase.fields.lock_status'))
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
                    ->label(__('filament.resources.purchase.actions.toggle_lock'))
                    ->icon('heroicon-o-lock-closed')
                    ->action(function ($record) {
                        $record->update([
                            'is_locked' => !$record->is_locked,
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('filament.resources.purchase.actions.confirm_lock_unlock'))
                    ->modalDescription(__('filament.resources.purchase.actions.lock_unlock_description')),
                Tables\Actions\Action::make('details')
                    ->label(__('filament.general.fields.details'))
                    ->icon('heroicon-o-eye')
                    ->modalHeading(__('filament.resources.purchase.fields.details_heading'))
                    ->modalWidth('6xl')
                    ->modalSubmitAction(false)
                    ->modalContent(fn ($record) => view('filament.resources.purchase.purchase-details', [
                        'purchase' => $record,
                        'purchaseItems' => $record->purchaseItems,
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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
