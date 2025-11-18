<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['product', 'adjustedBy'])->latest('adjustment_date');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('reason')) {
            $query->where('reason', $request->string('reason'));
        }

        if ($request->filled('product')) {
            $query->where('product_id', $request->integer('product'));
        }

        if ($request->filled('from')) {
            $query->whereDate('adjustment_date', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('adjustment_date', '<=', $request->date('to'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('adjustment_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('product', fn ($productQuery) => $productQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $adjustments = $query->paginate(15)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'stock']);
        $reasonOptions = $this->reasons();

        $stats = [
            'net' => StockAdjustment::sum(DB::raw("CASE WHEN type = 'add' THEN quantity ELSE -quantity END")),
            'added' => StockAdjustment::where('type', 'add')->sum('quantity'),
            'removed' => StockAdjustment::where('type', 'remove')->sum('quantity'),
        ];

        $filters = $request->only(['search', 'type', 'reason', 'product', 'from', 'to']);

        return view('stock-adjustments.index', compact('adjustments', 'products', 'reasonOptions', 'stats', 'filters'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'stock']);
        $reasonOptions = $this->reasons();

        $lastAdjustment = StockAdjustment::latest('id')->first();
        $adjustmentNumber = 'ADJ-' . str_pad(($lastAdjustment->id ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        return view('stock-adjustments.create', compact('products', 'reasonOptions', 'adjustmentNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'adjustment_number' => 'required|string|unique:stock_adjustments,adjustment_number',
            'product_id' => 'required|exists:products,id',
            'adjustment_date' => 'required|date',
            'type' => 'required|in:add,remove',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|in:' . implode(',', array_keys($this->reasons())),
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::lockForUpdate()->find($validated['product_id']);

            if ($validated['type'] === 'remove' && $product->stock < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => __('Not enough stock available to remove :qty units.', ['qty' => $validated['quantity']]),
                ]);
            }

            $delta = $validated['type'] === 'add' ? $validated['quantity'] : -$validated['quantity'];
            $product->increment('stock', $delta);

            StockAdjustment::create([
                ...$validated,
                'adjusted_by' => Auth::id(),
            ]);
        });

        return redirect()->route('stock-adjustments.index')->with('success', __('Stock adjustment recorded successfully.'));
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['product', 'adjustedBy']);

        return view('stock-adjustments.show', ['adjustment' => $stockAdjustment, 'reasonOptions' => $this->reasons()]);
    }

    public function edit(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['product', 'adjustedBy']);
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'stock']);
        $reasonOptions = $this->reasons();

        return view('stock-adjustments.edit', compact('stockAdjustment', 'products', 'reasonOptions'));
    }

    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        $validated = $request->validate([
            'adjustment_number' => 'required|string|unique:stock_adjustments,adjustment_number,' . $stockAdjustment->id,
            'product_id' => 'required|exists:products,id',
            'adjustment_date' => 'required|date',
            'type' => 'required|in:add,remove',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|in:' . implode(',', array_keys($this->reasons())),
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($stockAdjustment, $validated) {
            $oldProduct = Product::lockForUpdate()->find($stockAdjustment->product_id);
            $this->revertStockChange($stockAdjustment, $oldProduct);

            $newProduct = Product::lockForUpdate()->find($validated['product_id']);
            if ($validated['type'] === 'remove' && $newProduct->stock < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => __('Not enough stock available to remove :qty units.', ['qty' => $validated['quantity']]),
                ]);
            }

            $delta = $validated['type'] === 'add' ? $validated['quantity'] : -$validated['quantity'];
            $newProduct->increment('stock', $delta);

            $stockAdjustment->update([
                ...$validated,
                'adjusted_by' => $stockAdjustment->adjusted_by ?? Auth::id(),
            ]);
        });

        return redirect()->route('stock-adjustments.show', $stockAdjustment)->with('success', __('Stock adjustment updated.'));
    }

    public function destroy(StockAdjustment $stockAdjustment)
    {
        DB::transaction(function () use ($stockAdjustment) {
            $product = Product::lockForUpdate()->find($stockAdjustment->product_id);
            $this->revertStockChange($stockAdjustment, $product);

            $stockAdjustment->delete();
        });

        return redirect()->route('stock-adjustments.index')->with('success', __('Stock adjustment deleted.'));
    }

    protected function revertStockChange(StockAdjustment $adjustment, Product $product): void
    {
        if ($adjustment->type === 'add') {
            $product->decrement('stock', $adjustment->quantity);
        } else {
            $product->increment('stock', $adjustment->quantity);
        }
    }

    protected function reasons(): array
    {
        return [
            'damaged' => __('Damaged'),
            'expired' => __('Expired'),
            'theft' => __('Theft / Loss'),
            'found' => __('Stock Found'),
            'opening_stock' => __('Opening Stock'),
            'other' => __('Other'),
        ];
    }
}
