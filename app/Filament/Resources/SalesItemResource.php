<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesItemResource\Pages;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\Unit;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;

class SalesItemResource extends Resource
{
    protected static ?string $model = SalesItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation.sales');
    }

    public static function getNavigationSort(): ?int
    {
        return 9;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.sales_item.plural');
    }

    public static function getLabel(): string
    {
        return __('filament.resources.sales_item.singular');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sales_id')
                    ->label(__('filament.resources.sales_item.fields.sales_id'))
                    ->relationship('sales', 'code')
                    ->createOptionForm(Sales::getForm())
                    ->editOptionForm(Sales::getForm())
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('product_id')
                    ->label(__('filament.resources.sales_item.fields.product_id'))
                    ->relationship('product', 'name', function ($query) {
                        $query->whereNotNull('base_unit_id')
                            ->whereHas('baseUnit', function ($unitQuery) {
                                $unitQuery->where('conversion_factor', '>', 0);
                            });
                    })
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
                    ->label(__('filament.resources.sales_item.fields.quantity'))
                    ->required()
                    ->numeric()
                    ->helperText(function (callable $get) {
                        $productId = $get('product_id');
                        $unitId = $get('unit_id');

                        if (!$productId || !$unitId) {
                            return 'No product or unit selected.';
                        }

                        $inventory = Inventory::query()->where('product_id', $productId)->first();
                        $unit = Unit::query()->find($unitId);

                        if ($inventory && $unit) {
                            $availableStock = $inventory->quantity / $unit->conversion_factor;

                            return 'Available stock: ' . $availableStock . ' ' . $unit->unit_name;
                        }

                        return 'No stock available.';
                    }),
                Forms\Components\Select::make('unit_id')
                    ->label(__('filament.resources.sales_item.fields.unit_id'))
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
                            ->orderBy('conversion_factor', 'asc')
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
                    ->label(__('filament.resources.sales_item.fields.price_per_unit'))
                    ->required()
                    ->numeric()
                    ->prefix('IDR')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters([',']),
                Forms\Components\TextInput::make('discount')
                    ->label(__('filament.resources.sales_item.fields.discount'))
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
                Tables\Columns\TextColumn::make('sales.code')
                    ->label(__('filament.resources.sales_item.fields.sales_id'))
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('filament.resources.sales_item.fields.product_id'))
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('filament.resources.sales_item.fields.quantity'))
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.unit_name')
                    ->label(__('filament.resources.sales_item.fields.unit_id'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_per_unit')
                    ->label(__('filament.resources.sales_item.fields.price_per_unit'))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount')
                    ->label(__('filament.resources.sales_item.fields.discount'))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
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
            'index' => Pages\ListSalesItems::route('/'),
            'create' => Pages\CreateSalesItem::route('/create'),
            'edit' => Pages\EditSalesItem::route('/{record}/edit'),
        ];
    }
}
