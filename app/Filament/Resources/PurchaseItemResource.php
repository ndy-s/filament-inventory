<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseItemResource\Pages;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseItemResource extends Resource
{
    protected static ?string $model = PurchaseItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation.purchases');
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.purchase_item.plural');
    }

    public static function getLabel(): string
    {
        return __('filament.resources.purchase_item.singular');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('purchase_id')
                    ->label(__('filament.resources.purchase_item.fields.purchase_id'))
                    ->relationship('purchase', 'code')
                    ->createOptionForm(Purchase::getForm())
                    ->editOptionForm(Purchase::getForm())
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('product_id')
                    ->label(__('filament.resources.purchase_item.fields.product_id'))
                    ->relationship('product', 'name')
                    ->createOptionForm(Product::getForm())
                    ->editOptionForm(Product::getForm())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('unit_id', null);
                    }),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('filament.resources.purchase_item.fields.quantity'))
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('unit_id')
                    ->label(__('filament.resources.purchase_item.fields.unit'))
                    ->relationship('unit', 'unit_name')
                    ->options(function (callable $get) {
                        $productId = $get('product_id');
                        if (!$productId) {
                            return [];
                        }

                        return Unit::query()->where('product_id', $productId)
                            ->get()
                            ->mapWithKeys(function ($unit) {
                                return [$unit->id => $unit->unit_name . ' (' . $unit->product->name . ')'];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('price_per_unit')
                    ->label(__('filament.resources.purchase_item.fields.price_per_unit'))
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('discount')
                    ->label(__('filament.resources.purchase_item.fields.discount'))
                    ->required()
                    ->numeric()
                    ->default(0.00),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchase.code')
                    ->label(__('filament.resources.purchase.fields.code'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('filament.resources.product.fields.name'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('filament.resources.purchase_item.fields.quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.unit_name')
                    ->label(__('filament.resources.unit.fields.unit_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_per_unit')
                    ->label(__('filament.resources.purchase_item.fields.price_per_unit'))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->label(__('filament.resources.purchase_item.fields.discount'))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListPurchaseItems::route('/'),
            'create' => Pages\CreatePurchaseItem::route('/create'),
            'edit' => Pages\EditPurchaseItem::route('/{record}/edit'),
        ];
    }
}
