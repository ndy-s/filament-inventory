<?php

namespace App\Models;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
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
        'latitude',
        'longitude',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected static function booted(): void
    {
        static::deleting(function ($customer) {
            if ($customer->sales()->exists()) {
                throw new ModelNotFoundException(__('Customer cannot be deleted because it has associated sales.'));
            }
        });
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sales::class);
    }

    public static function getForm(): array
    {
        return [
            Grid::make(1)
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament.resources.customer.fields.name'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('contact_person')
                        ->label(__('filament.resources.customer.fields.contact_person'))
                        ->helperText(__('filament.resources.customer.fields.contact_person_helper'))
                        ->maxLength(255)
                        ->default(null),
                ]),

            Grid::make(2)
                ->schema([
                    TextInput::make('phone')
                        ->label(__('filament.resources.customer.fields.phone'))
                        ->tel()
                        ->maxLength(255)
                        ->default(null),
                    TextInput::make('email')
                        ->label(__('filament.resources.customer.fields.email'))
                        ->email()
                        ->maxLength(255)
                        ->default(null),
                ]),

            Grid::make(1)
                ->schema([
                    TextInput::make('address')
                        ->label(__('filament.resources.customer.fields.address'))
                        ->maxLength(255)
                        ->default(null),
                ]),

            Grid::make(2)
                ->schema([
                    TextInput::make('latitude')
                        ->label(__('filament.resources.customer.fields.latitude'))
                        ->helperText(__('filament.resources.customer.fields.latitude_helper'))
                        ->numeric()
                        ->default(null),
                    TextInput::make('longitude')
                        ->label(__('filament.resources.customer.fields.longitude'))
                        ->helperText(__('filament.resources.customer.fields.longitude_helper'))
                        ->numeric()
                        ->default(null),
                ]),
        ];
    }
}
