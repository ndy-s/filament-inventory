<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_id',
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
        'sales_id' => 'integer',
        'product_id' => 'integer',
        'unit_id' => 'integer',
        'price_per_unit' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function ($salesItem) {
            // Get the product and unit associated with the sales item
            $product = $salesItem->product;
            $unit = $salesItem->unit;

            if (!$product || !$unit) {
                throw new \Exception('Product or unit not found.');
            }

            // Fetch the existing record if it exists (for edit scenario)
            $originalItem = $salesItem->getOriginal();

            $originalConvertedQuantity = 0;
            if ($originalItem && isset($originalItem['quantity']) && isset($originalItem['unit_id'])) {
                $originalUnit = Unit::query()->find($originalItem['unit_id']);
                if ($originalUnit) {
                    $originalConvertedQuantity = $originalItem['quantity'] * $originalUnit->conversion_factor;
                }
            }

            // Convert the new quantity to the base unit
            $newConvertedQuantity = $salesItem->quantity * $unit->conversion_factor;

            // Calculate the difference to update inventory
            // If it's an edit, the difference should be the difference in the converted quantity
            $quantityDifference = $newConvertedQuantity - $originalConvertedQuantity;

            // Update inventory (subtract from inventory since it's a sale)
            $inventory = Inventory::query()->firstOrNew([
                'product_id' => $product->id,
            ]);

            // Deduct the quantityDifference from inventory
            $inventory->quantity = ($inventory->quantity ?? 0) - $quantityDifference;

            // Prevent negative inventory
            if ($inventory->quantity < 0) {
                throw new \Exception('Not enough inventory to complete the sale.');
            }

            $inventory->save();
        });

        static::deleting(function ($salesItem) {
            // Get the product and unit associated with the sales item
            $product = $salesItem->product;
            $unit = $salesItem->unit;

            if (!$product || !$unit) {
                throw new \Exception('Product or unit not found.');
            }

            // Convert the quantity to the base unit for the deleted item
            $convertedQuantity = $salesItem->quantity * $unit->conversion_factor;

            // Update inventory (add back to inventory since it's a deleted sale item)
            $inventory = Inventory::query()->where('product_id', $product->id)->first();

            if ($inventory) {
                // Add the converted quantity back to inventory
                $inventory->quantity += $convertedQuantity;

                $inventory->save();
            }
        });
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class);
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
