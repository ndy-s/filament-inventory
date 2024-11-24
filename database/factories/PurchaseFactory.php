<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Purchase;
use App\Models\Supplier;

class PurchaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Purchase::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->word(),
            'supplier_id' => Supplier::factory(),
            'date' => $this->faker->date(),
            'invoice_image' => $this->faker->word(),
        ];
    }
}
