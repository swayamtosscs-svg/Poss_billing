<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', function () {
        $products = Product::query()->latest()->paginate(25);
        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
            'data' => $products,
        ]);
    });

    Route::post('/customers', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'address' => 'nullable|string',
        ]);
        $customer = Customer::create($validated + ['points' => 0]);
        return response()->json([
            'status' => true,
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    });

    Route::post('/sales', function (Request $request) {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'payment_type' => 'required|string',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $sale = \DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $sale = Sale::create([
                'customer_id' => $validated['customer_id'] ?? null,
                'payment_type' => $validated['payment_type'],
                'discount' => $validated['discount'] ?? 0,
                'tax' => 0,
                'total_amount' => 0,
                'date' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                $lineTotal = $item['quantity'] * $item['price'];
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $lineTotal,
                ]);
                $subtotal += $lineTotal;

                // Deduct stock
                Product::whereKey($item['product_id'])->decrement('stock', $item['quantity']);
            }

            $tax = round($subtotal * 0.18, 2);
            $sale->update([
                'tax' => $tax,
                'total_amount' => max(0, $subtotal - ($sale->discount ?? 0)) + $tax,
            ]);

            return $sale->load('items');
        });

        return response()->json([
            'status' => true,
            'message' => 'Sale recorded successfully',
            'data' => $sale,
        ], 201);
    });

    Route::get('/reports', function (Request $request) {
        $from = $request->date('from', now()->startOfMonth());
        $to = $request->date('to', now()->endOfMonth());
        $sales = Sale::whereBetween('date', [$from, $to])->get();
        return response()->json([
            'status' => true,
            'message' => 'Reports fetched successfully',
            'data' => [
                'from' => $from,
                'to' => $to,
                'sales' => $sales,
                'total' => $sales->sum('total_amount'),
            ],
        ]);
    });
});


