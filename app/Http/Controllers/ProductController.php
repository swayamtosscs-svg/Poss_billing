<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Http\Requests\ProductRequest;
use App\Imports\ProductsImport;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($search = $request->input('search')) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category_id', $category);
        }

        if ($request->boolean('low_stock')) {
            $query->where('stock', '<', 10);
        }

        $products = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'filters' => $request->only(['search', 'category', 'low_stock']),
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request);
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        return view('products.show', ['product' => $product->load('category')]);
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product->load('category'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $this->deleteImage($product->image_path);
            $data['image_path'] = $this->storeImage($request);
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->saleItems()->exists()) {
            return redirect()->route('products.index')->with('error', 'Cannot delete product with associated sales.');
        }

        $this->deleteImage($product->image_path);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv,xls'],
        ]);

        Excel::import(new ProductsImport, $request->file('file'));

        return redirect()->route('products.index')->with('success', 'Products imported successfully.');
    }

    public function export(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'category']);

        return Excel::download(new ProductsExport($filters), 'products-' . now()->format('Ymd_His') . '.xlsx');
    }

    public function search(Request $request): JsonResponse
    {
        $term = $request->input('q');

        $products = Product::query()
            ->with('category:id,name')
            ->select(['id', 'name', 'price', 'stock', 'barcode', 'sku', 'category_id', 'image_path'])
            ->when($term, function ($query, $term) {
                $query->where(function ($builder) use ($term) {
                    $builder->where('name', 'like', "%{$term}%")
                        ->orWhere('barcode', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%");
                });
            })
            ->limit(24)
            ->get()
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'barcode' => $product->barcode,
                    'sku' => $product->sku,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category?->name,
                    'image_url' => $product->thumbnail_url,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $products,
        ]);
    }

    protected function storeImage(Request $request): string
    {
        if (! Storage::disk('assets')->exists('products')) {
            Storage::disk('assets')->makeDirectory('products');
        }

        $path = $request->file('image')->store('products', 'assets');

        return $path;
    }

    protected function deleteImage(?string $path): void
    {
        if (! $path) {
            return;
        }

        foreach (['assets', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
                return;
            }
        }

        $absolutePath = public_path('assets/' . ltrim($path, '/'));
        if (file_exists($absolutePath)) {
            @unlink($absolutePath);
            return;
        }

        $absolutePath = public_path($path);
        if (file_exists($absolutePath)) {
            @unlink($absolutePath);
        }
    }
}

