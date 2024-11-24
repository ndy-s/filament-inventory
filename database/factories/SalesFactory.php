<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\Sales;

class SalesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sales::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->word(),
            'customer_id' => Customer::factory(),
            'date' => $this->faker->date(),
        ];
    }
}
