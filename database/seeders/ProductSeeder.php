<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = Category::query()->pluck('id')->all();
        if (empty($categoryIds)) {
            $categoryIds = Category::factory()->count(5)->create()->pluck('id')->all();
        }

        // Create 120 products
        Product::factory()
            ->count(120)
            ->state(function () use ($categoryIds) {
                return ['category_id' => fake()->randomElement($categoryIds)];
            })
            ->create();
    }
}
