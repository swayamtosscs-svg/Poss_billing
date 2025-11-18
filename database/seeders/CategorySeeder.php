<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $names = ['Beverages', 'Snacks', 'Groceries', 'Personal Care', 'Electronics', 'Stationery', 'Dairy', 'Bakery', 'Frozen', 'Produce'];
            foreach ($names as $name) {
                Category::query()->firstOrCreate(['name' => $name], ['description' => fake()->sentence()]);
            }
    }
}
