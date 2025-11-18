<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $discount = fake()->randomFloat(2, 0, 50);
        $tax = fake()->randomFloat(2, 0, 50);
        $total = fake()->randomFloat(2, 100, 5000);
        return [
            'customer_id' => \App\Models\Customer::factory(),
            'total_amount' => $total,
            'payment_type' => fake()->randomElement(['cash', 'card', 'upi']),
            'discount' => $discount,
            'tax' => $tax,
            'date' => fake()->dateTimeBetween('-60 days', 'now'),
        ];
    }
}
