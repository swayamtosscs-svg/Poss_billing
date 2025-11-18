<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::query()->inRandomOrder()->limit(30)->get();
        $products = Product::query()->inRandomOrder()->limit(100)->get();

        if ($customers->isEmpty()) {
            $customers = Customer::factory()->count(10)->create();
        }
        if ($products->isEmpty()) {
            $products = Product::factory()->count(50)->create();
        }

        // Create 200 sales with 1-5 items each
        for ($i = 0; $i < 200; $i++) {
            $sale = Sale::create([
                'customer_id' => $customers->random()->id,
                'total_amount' => 0,
                'payment_type' => fake()->randomElement(['cash', 'card', 'upi']),
                'discount' => fake()->randomFloat(2, 0, 50),
                'tax' => 0,
                'date' => fake()->dateTimeBetween('-60 days', 'now'),
            ]);

            $numItems = rand(1, 5);
            $subtotal = 0;
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $qty = rand(1, 3);
                $price = $product->price;
                $lineTotal = $qty * $price;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $lineTotal,
                ]);
                $subtotal += $lineTotal;
            }
            $tax = round($subtotal * 0.18, 2);
            $total = max(0, $subtotal - $sale->discount) + $tax;
            $sale->update(['tax' => $tax, 'total_amount' => $total]);
        }
    }
}
