<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;

class PurchaseItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PurchaseItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'purchase_id' => Purchase::factory(),
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(-10000, 10000),
            'unit' => $this->faker->word(),
            'price_per_unit' => $this->faker->randomFloat(2, 0, 999999.99),
            'discount' => $this->faker->randomFloat(2, 0, 999999.99),
        ];
    }
}
