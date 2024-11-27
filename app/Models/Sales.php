<?php

namespace App\Models;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sales extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'customer_id',
        'date',
        'is_locked',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function ($sales) {
            $sales->code = 'SAL/' . now()->format('my') . '/' . str_pad($sales->customer_id, 2, '0', STR_PAD_LEFT) . '/' . strtoupper(Str::random(3));
        });
    }


    public function salesItems(): HasMany
    {
        return $this->hasMany(SalesItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('code')
                ->label(__('filament.resources.sales.fields.code'))
                ->maxLength(255)
                ->disabled()
                ->default(''),
            Select::make('customer_id')
                ->label(__('filament.resources.sales.fields.customer'))
                ->relationship('customer', 'name')
                ->createOptionForm(Customer::getForm())
                ->editOptionForm(Customer::getForm())
                ->searchable()
                ->preload()
                ->default(null),
            DatePicker::make('date')
                ->label(__('filament.resources.sales.fields.date'))
                ->default(null),
        ];
    }
}
