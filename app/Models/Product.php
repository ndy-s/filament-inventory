<?php

namespace App\Models;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'notes',
        'base_unit_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'base_unit_id' => 'integer',
    ];

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class);
    }

    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sales::class);
    }

    public static function getForm(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament.resources.product.fields.name'))
                        ->required()
                        ->maxLength(255),
                    Select::make('base_unit_id')
                        ->label(__('filament.resources.product.fields.base_unit'))
                        ->helperText(__('filament.resources.product.fields.base_unit_helper'))
                        ->options(function () {
                            return Unit::all()
                                ->mapWithKeys(function ($unit) {
                                    return [$unit->id => $unit->unit_name . ' (' . $unit->product->name . ')'];
                                });
                        })
                        ->searchable()
                        ->preload()
                        ->default(null),
                ]),
            Grid::make(1)
                ->schema([
                    RichEditor::make('notes')
                        ->label(__('filament.resources.product.fields.notes'))
                        ->toolbarButtons([
                            'bold',
                            'bulletList',
                            'italic',
                            'orderedList',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ])
                        ->default(null),
                ]),
        ];
    }
}
