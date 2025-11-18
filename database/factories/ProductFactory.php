<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'category_id' => \App\Models\Category::factory(),
            'stock' => fake()->numberBetween(0, 500),
            'price' => fake()->randomFloat(2, 10, 2000),
            'barcode' => fake()->ean13(),
            'sku' => strtoupper(fake()->bothify('SKU-#####')),
            'image_path' => null,
            'description' => fake()->optional()->paragraph(),
        ];
    }
}
