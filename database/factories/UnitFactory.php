<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Unit;

class UnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Unit::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'unit_name' => $this->faker->word(),
            'conversion_factor' => $this->faker->numberBetween(-10000, 10000),
        ];
    }
}
