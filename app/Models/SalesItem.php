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
        'unit',
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
        'price_per_unit' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
