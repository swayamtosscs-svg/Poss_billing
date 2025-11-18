<?php

namespace App\Services;

use App\Events\SaleRecorded;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    /**
     * @param array{
     *     customer_id?: int|null,
     *     payment_type: string,
     *     discount?: float,
     *     tax?: float,
     *     notes?: string|null,
     *     items: array<int, array{product_id:int, quantity:int, price:float, discount?:float}>
     * } $data
     */
    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $itemsData = $data['items'] ?? [];

            if (empty($itemsData)) {
                throw ValidationException::withMessages([
                    'items' => 'At least one item is required.',
                ]);
            }

            $subtotal = 0;
            $itemsPrepared = [];

            foreach ($itemsData as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $quantity = max(1, (int) $item['quantity']);

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for {$product->name}.",
                    ]);
                }

                $price = round((float) $item['price'], 2);
                $lineDiscount = round((float) ($item['discount'] ?? 0), 2);
                $lineTotal = max(0, ($price * $quantity) - $lineDiscount);

                $subtotal += $lineTotal;

                $itemsPrepared[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $lineTotal,
                ];
            }

            $discount = round((float) Arr::get($data, 'discount', 0), 2);
            $tax = round((float) Arr::get($data, 'tax', 0), 2);

            $total = max(0, $subtotal - $discount) + $tax;

            $sale = Sale::create([
                'customer_id' => Arr::get($data, 'customer_id'),
                'total_amount' => $total,
                'payment_type' => Arr::get($data, 'payment_type', 'cash'),
                'discount' => $discount,
                'tax' => $tax,
                'date' => now(),
            ]);

            foreach ($itemsPrepared as $item) {
                $sale->items()->create($item);
            }

            $sale->load(['items.product', 'customer']);

            SaleRecorded::dispatch($sale);

            return $sale;
        });
    }
}

