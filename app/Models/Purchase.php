<?php

namespace App\Models;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Purchase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'supplier_id',
        'date',
        'invoice_image',
        'is_locked',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'supplier_id' => 'integer',
        'date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function ($purchase) {
            $purchase->code = 'PUR/' . now()->format('my') . '/' . str_pad($purchase->supplier_id, 2, '0', STR_PAD_LEFT) . '/' . strtoupper(Str::random(3));
        });
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('code')
                ->label(__('filament.resources.purchase.fields.code'))
                ->maxLength(255)
                ->disabled()
                ->default(''),
            Select::make('supplier_id')
                ->label(__('filament.resources.purchase.fields.supplier'))
                ->relationship('supplier', 'name')
                ->createOptionForm(Supplier::getForm())
                ->editOptionForm(Supplier::getForm())
                ->searchable()
                ->preload()
                ->default(null),
            DatePicker::make('date')
                ->label(__('filament.resources.purchase.fields.date'))
                ->default(null),
            FileUpload::make('invoice_image')
                ->label(__('filament.resources.purchase.fields.invoice_image'))
                ->disk('public')
                ->directory('purchase')
                ->image()
                ->openable()
                ->default(null),
        ];
    }
}
