<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SaleController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {
    }

    public function index(Request $request): View
    {
        $sales = Sale::with(['customer'])
            ->when($request->filled('customer'), fn($q) => $q->where('customer_id', $request->input('customer')))
            ->when($request->filled('payment_type'), fn($q) => $q->where('payment_type', $request->input('payment_type')))
            ->when($request->filled('from'), fn($q) => $q->whereDate('date', '>=', $request->date('from')))
            ->when($request->filled('to'), fn($q) => $q->whereDate('date', '<=', $request->date('to')))
            ->orderByDesc('date')
            ->paginate(15)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['customer', 'payment_type', 'from', 'to']),
        ]);
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $products = Product::with('category:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'stock', 'image_path', 'category_id'])
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category?->name,
                    'image_url' => $product->thumbnail_url,
                ];
            })
            ->values();

        return view('sales.create', [
            'customers' => Customer::orderBy('name')->get(['id', 'name', 'phone']),
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function createInvoice(): View
    {
        $products = Product::with('category:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'stock', 'image_path', 'category_id'])
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category?->name,
                    'image_url' => $product->thumbnail_url,
                ];
            })
            ->values();

        return view('sales.create-invoice', [
            'products' => $products,
        ]);
    }

    public function store(SaleRequest $request): RedirectResponse
    {
        $sale = $this->saleService->create($request->validated());

        return redirect()->route('sales.show', $sale)->with('success', 'Sale completed successfully.');
    }

    public function show(Sale $sale): View
    {
        $sale->load(['items.product', 'customer']);

        return view('sales.show', compact('sale'));
    }

    public function invoice(Sale $sale): Response
    {
        $sale->load(['items.product', 'customer']);
        $pdf = Pdf::loadView('sales.invoice-pdf', ['sale' => $sale]);

        return $pdf->download('invoice-'.$sale->id.'.pdf');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        return redirect()->route('sales.index')->with('error', 'Deleting sales is not permitted.');
    }
}

