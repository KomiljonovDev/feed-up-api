<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name'=>fake()->name,
            'phone_number'=>fake()->numberBetween(1000, 100000),
            'status'=>fake()->randomElement(['pending', 'completed', 'canceled']),
            'created_at'=>fake()->dateTimeBetween('-8 month', 'now'),
        ];
    }
}
