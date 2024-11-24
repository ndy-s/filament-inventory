<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesItem;

class SalesItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalesItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'sales_id' => Sales::factory(),
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(-10000, 10000),
            'unit' => $this->faker->word(),
            'price_per_unit' => $this->faker->randomFloat(2, 0, 999999.99),
            'discount' => $this->faker->randomFloat(2, 0, 999999.99),
        ];
    }
}
