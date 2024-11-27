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

            // Fetch the original record if it exists (for edit scenario)
            $originalItem = $salesItem->getOriginal();

            $originalConvertedQuantity = 0;
            if ($originalItem) {
                $originalProductId = $originalItem['product_id'];
                $originalUnit = Unit::query()->find($originalItem['unit_id']);

                if ($originalUnit) {
                    $originalConvertedQuantity = $originalItem['quantity'] * $originalUnit->conversion_factor;
                }

                // If the product is changed, adjust inventory for the original product
                if ($originalProductId != $salesItem->product_id) {
                    // Handle inventory addition for the original product
                    $originalInventory = Inventory::query()->where('product_id', $originalProductId)->first();
                    if ($originalInventory) {
                        $originalInventory->quantity += $originalConvertedQuantity;
                        $originalInventory->save();
                    }

                    // Handle inventory subtraction for the new product
                    $newConvertedQuantity = $salesItem->quantity * $unit->conversion_factor;
                    $newInventory = Inventory::query()->firstOrNew([
                        'product_id' => $product->id,
                    ]);

                    $newInventory->quantity = ($newInventory->quantity ?? 0) - $newConvertedQuantity;

                    if ($newInventory->quantity < 0) {
                        throw new \Exception('Not enough inventory to complete the sale.');
                    }

                    $newInventory->save();

                    return; // Exit since product has changed
                }
            }

            // If product is not changed, calculate the difference
            $newConvertedQuantity = $salesItem->quantity * $unit->conversion_factor;
            $quantityDifference = $newConvertedQuantity - $originalConvertedQuantity;

            // Update inventory for the same product
            $inventory = Inventory::query()->firstOrNew([
                'product_id' => $product->id,
            ]);

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
