<?php

namespace App\Models;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function getForm(): array
    {
        return [
            Select::make('product_id')
                ->label(__('filament.resources.unit.fields.product_id'))
                ->relationship('product', 'name')
                ->searchable()
                ->preload()
                ->required(),
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
