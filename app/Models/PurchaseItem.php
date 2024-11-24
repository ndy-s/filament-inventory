<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_id',
        'price_per_unit',
        'discount',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'purchase_id' => 'integer',
        'product_id' => 'integer',
        'unit_id' => 'integer',
        'price_per_unit' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function ($purchaseItem) {
            // Get the base unit conversion factor for the product
            $product = $purchaseItem->product;
            $unit = $purchaseItem->unit;

            if (!$product || !$unit) {
                throw new \Exception('Product or unit not found.');
            }

            // Convert quantity to the base unit
            $convertedQuantity = $purchaseItem->quantity * $unit->conversion_factor;

            // Update inventory
            $inventory = Inventory::query()->firstOrNew([
                'product_id' => $product->id,
            ]);

            // Add converted quantity to the inventory
            $inventory->quantity = ($inventory->quantity ?? 0) + $convertedQuantity;
            $inventory->save();
        });

        static::deleting(function ($purchaseItem) {
            // Get the base unit conversion factor for the product
            $product = $purchaseItem->product;
            $unit = $purchaseItem->unit;

            if (!$product || !$unit) {
                throw new \Exception('Product or unit not found.');
            }

            // Convert quantity to the base unit
            $convertedQuantity = $purchaseItem->quantity * $unit->conversion_factor;

            // Update inventory
            $inventory = Inventory::query()->where('product_id', $product->id)->first();

            if ($inventory) {
                // Subtract converted quantity from the inventory
                $inventory->quantity -= $convertedQuantity;

                // Delete the inventory if quantity is zero or less
                if ($inventory->quantity <= 0) {
                    $inventory->delete();
                } else {
                    $inventory->save();
                }
            }
        });
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
