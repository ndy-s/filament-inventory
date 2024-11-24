<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\PurchaseItem;

class InventoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Inventory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'purchase_item_id' => PurchaseItem::factory(),
            'quantity' => $this->faker->numberBetween(-10000, 10000),
            'unit' => $this->faker->word(),
            'price_per_unit' => $this->faker->randomFloat(2, 0, 999999.99),
        ];
    }
}
