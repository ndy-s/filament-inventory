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
use Filament\Support\RawJs;
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
                    ->label(__('filament.resources.purchase_item.fields.purchase'))
                    ->relationship('purchase', 'code', function ($query) {
                        $query->where('is_locked', 0)->orderBy('created_at', 'desc');
                    })
                    ->createOptionForm(Purchase::getForm())
                    ->editOptionForm(Purchase::getForm())
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('product_id')
                    ->label(__('filament.resources.purchase_item.fields.product'))
                    ->relationship('product', 'name', function ($query) {
                        $query->whereNotNull('base_unit_id')
                            ->whereHas('baseUnit', function ($unitQuery) {
                                $unitQuery->where('conversion_factor', '>', 0);
                            });
                    })
                    ->createOptionForm(Product::getForm())
                    ->editOptionForm(Product::getForm())
                    ->options(function () {
                        return Product::query()->whereNotNull('base_unit_id')
                            ->whereHas('baseUnit', function ($unitQuery) {
                                $unitQuery->where('conversion_factor', '>', 0);
                            })
                            ->get()
                            ->mapWithKeys(function ($product) {
                                $notes = $product->notes ? strip_tags($product->notes) : null;

                                $label = $notes ? $product->name . ' (' . $notes . ')' : $product->name;

                                return [$product->id => $label];
                            });
                    })
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
                    ->relationship('unit', 'unit_name', function ($query) {
                        $query->where('conversion_factor', '>', 0)->orderBy('conversion_factor', 'asc');
                    })
                    ->options(function (callable $get) {
                        $productId = $get('product_id');
                        if (!$productId) {
                            return [];
                        }

                        return Unit::query()
                            ->where('product_id', $productId)
                            ->where('conversion_factor', '>', 0)
                            ->orderBy('conversion_factor')
                            ->get()
                            ->mapWithKeys(function ($unit) {
                                return [$unit->id => $unit->unit_name . ' (' . $unit->product->name . ')'];
                            });
                    })
                    ->createOptionForm(Unit::getForm())
                    ->editOptionForm(Unit::getForm())
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('price_per_unit')
                    ->label(__('filament.resources.purchase_item.fields.price_per_unit'))
                    ->required()
                    ->numeric()
                    ->prefix('IDR')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters([',']),
                Forms\Components\TextInput::make('discount')
                    ->label(__('filament.resources.purchase_item.fields.discount'))
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->prefix('IDR')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters([',']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchase.code')
                    ->label(__('filament.resources.purchase_item.fields.purchase'))
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('filament.resources.purchase_item.fields.product'))
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('filament.resources.purchase_item.fields.quantity'))
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.unit_name')
                    ->label(__('filament.resources.purchase_item.fields.unit'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_per_unit')
                    ->label(__('filament.resources.purchase_item.fields.price_per_unit'))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 2, ',', '.'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount')
                    ->label(__('filament.resources.purchase_item.fields.discount'))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 2, ',', '.'))
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
            'index' => Pages\ListPurchaseItems::route('/'),
            'create' => Pages\CreatePurchaseItem::route('/create'),
            'edit' => Pages\EditPurchaseItem::route('/{record}/edit'),
        ];
    }
}
