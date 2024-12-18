<?php

namespace App\Models;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    protected static function booted(): void
    {
        static::deleting(function ($product) {
            if (
                $product->purchaseItems()->exists() ||
                $product->salesItems()->exists() ||
                $product->units()->exists()
            ) {
                throw new ModelNotFoundException(__('Product cannot be deleted because it has associated records.'));
            }
        });
    }

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

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sales::class);
    }

    public function salesItems(): HasMany
    {
        return $this->hasMany(SalesItem::class);
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
                        ->relationship('baseUnit', 'unit_name', function ($query) {
                            $query->where('conversion_factor', 1);
                        })
                        ->options(function (callable $get) {
                            $productName = $get('name');

                            return Unit::query()->whereHas('product', function ($query) use ($productName) {
                                $query->where('name', $productName);
                            })
                                ->where('conversion_factor', 1)
                                ->get()
                                ->mapWithKeys(function ($unit) {
                                    return [
                                        $unit->id => $unit->product->name . ' - ' . $unit->unit_name . ' (Factor: ' . $unit->conversion_factor . ')'
                                    ];
                                });
                        })
                        ->createOptionForm(Unit::getForm())
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
