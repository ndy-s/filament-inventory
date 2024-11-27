<?php

namespace App\Models;

use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'unit_name',
        'conversion_factor',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'product_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::updating(function (Unit $unit) {
            if ($unit->isDirty('conversion_factor')) {
                if ($unit->purchaseItems()->exists() || $unit->salesItems()->exists()) {
                    throw new Exception(__('The conversion factor for this unit cannot be updated because it has been used in transactions.'));
                }
            }
        });

        static::deleting(function (Unit $unit) {
            if ($unit->purchaseItems()->exists() || $unit->salesItems()->exists()) {
                throw new Exception(__('This unit is used in other transactions and cannot be deleted.'));
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function salesItems(): HasMany
    {
        return $this->hasMany(SalesItem::class);
    }


    public static function getForm(): array
    {
        return [
            Select::make('product_id')
                ->label(__('filament.resources.unit.fields.product'))
                ->relationship('product', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->disabled(function (callable $get) {
                    $unitId = $get('id');
                    if ($unitId) {
                        $unit = Unit::query()->find($unitId);
                        if ($unit && $unit->product_id) {
                            return true;
                        }
                    }
                    return false;
                })
                ->options(function () {
                    return Product::all()->mapWithKeys(function ($product) {
                        $notes = $product->notes ? strip_tags($product->notes) : null;
                        $label = $notes ? $product->name . ' (' . $notes . ')' : $product->name;

                        return [$product->id => $label];
                    });
                }),
            TextInput::make('unit_name')
                ->label(__('filament.resources.unit.fields.unit_name'))
                ->required()
                ->maxLength(255),
            TextInput::make('conversion_factor')
                ->label(__('filament.resources.unit.fields.conversion_factor'))
                ->helperText(__('filament.resources.unit.fields.conversion_factor_helper'))
                ->required()
                ->numeric()
                ->default(0),
        ];
    }
}
