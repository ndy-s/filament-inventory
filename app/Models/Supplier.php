<?php

namespace App\Models;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function ($supplier) {
            if ($supplier->purchases()->exists()) {
                throw new ModelNotFoundException(__('Supplier cannot be deleted because it has associated purchases.'));
            }
        });
    }


    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public static function getForm(): array
    {
        return [
            Grid::make(1)
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament.resources.supplier.fields.name'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('contact_person')
                        ->label(__('filament.resources.supplier.fields.contact_person'))
                        ->helperText(__('filament.resources.supplier.fields.contact_person_helper'))
                        ->maxLength(255)
                        ->default(null),
                ]),

            Grid::make(2)
                ->schema([
                    TextInput::make('phone')
                        ->label(__('filament.resources.supplier.fields.phone'))
                        ->tel()
                        ->maxLength(255)
                        ->default(null),
                    TextInput::make('email')
                        ->label(__('filament.resources.supplier.fields.email'))
                        ->email()
                        ->maxLength(255)
                        ->default(null),
                ]),

            Grid::make(1)
                ->schema([
                    TextInput::make('address')
                        ->label(__('filament.resources.supplier.fields.address'))
                        ->maxLength(255)
                        ->default(null),
                ]),
        ];
    }
}
