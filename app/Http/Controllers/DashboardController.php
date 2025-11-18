<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $todayTotal = Sale::whereDate('date', now()->toDateString())->sum('total_amount');
        $weekTotal = Sale::whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
        $monthTotal = Sale::whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount');

        $topProducts = \DB::table('sale_items')
            ->select('product_id', \DB::raw('SUM(quantity) as qty'))
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $product = Product::find($row->product_id);
                return ['name' => $product?->name ?? 'Unknown', 'qty' => $row->qty];
            });

        $lowStock = Product::orderBy('stock')->limit(5)->get(['name', 'stock']);

        return view('dashboard', [
            'todayTotal' => $todayTotal,
            'weekTotal' => $weekTotal,
            'monthTotal' => $monthTotal,
            'topProducts' => $topProducts,
            'lowStock' => $lowStock,
            'customerCount' => Customer::count(),
            'productCount' => Product::count(),
            'saleCount' => Sale::count(),
        ]);
    }
}
