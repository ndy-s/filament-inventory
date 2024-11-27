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
            $product = $purchaseItem->product;
            $unit = $purchaseItem->unit;

            if (!$product || !$unit) {
                throw new \Exception('Product or unit not found.');
            }

            // Fetch the original record if it exists (for edit scenario)
            $originalItem = $purchaseItem->getOriginal();

            $originalConvertedQuantity = 0;
            if ($originalItem) {
                $originalProductId = $originalItem['product_id'];
                $originalUnit = Unit::query()->find($originalItem['unit_id']);

                if ($originalUnit) {
                    $originalConvertedQuantity = $originalItem['quantity'] * $originalUnit->conversion_factor;
                }

                // If the product is changed, adjust inventory for the original product
                if ($originalProductId != $purchaseItem->product_id) {
                    // Handle inventory reduction for the original product
                    $originalInventory = Inventory::query()->where('product_id', $originalProductId)->first();
                    if ($originalInventory) {
                        $originalInventory->quantity -= $originalConvertedQuantity;

                        // Delete if inventory is zero or less
                        if ($originalInventory->quantity <= 0) {
                            $originalInventory->delete();
                        } else {
                            $originalInventory->save();
                        }
                    }

                    // Handle inventory addition for the new product
                    $newConvertedQuantity = $purchaseItem->quantity * $unit->conversion_factor;
                    $inventory = Inventory::query()->firstOrNew([
                        'product_id' => $product->id,
                    ]);

                    $inventory->quantity = ($inventory->quantity ?? 0) + $newConvertedQuantity;

                    // Prevent negative inventory
                    if ($inventory->quantity < 0) {
                        throw new \Exception('Inventory cannot have a negative quantity.');
                    }

                    $inventory->save();

                    return; // Exit since product has changed
                }
            }

            // If product is not changed, calculate the difference
            $newConvertedQuantity = $purchaseItem->quantity * $unit->conversion_factor;
            $quantityDifference = $newConvertedQuantity - $originalConvertedQuantity;

            // Update inventory for the same product
            $inventory = Inventory::query()->firstOrNew([
                'product_id' => $product->id,
            ]);

            $inventory->quantity = ($inventory->quantity ?? 0) + $quantityDifference;

            // Prevent negative inventory
            if ($inventory->quantity < 0) {
                throw new \Exception('Inventory cannot have a negative quantity.');
            }

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
