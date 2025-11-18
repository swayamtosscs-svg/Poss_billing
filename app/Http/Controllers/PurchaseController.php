<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Party;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['party', 'items']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('party', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $purchases = $query->latest()->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $parties = Party::where('type', 'supplier')->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        // Generate invoice number
        $lastInvoice = Purchase::latest('id')->first();
        $invoiceNo = 'PUR-' . str_pad(($lastInvoice->id ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        return view('purchases.create', compact('parties', 'products', 'invoiceNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|string|unique:purchases',
            'party_id' => 'required|exists:parties,id',
            'purchase_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:purchase_date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'status' => 'required|in:draft,ordered,received,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Calculate totals
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $qty = $item['quantity'];
                $price = $item['purchase_price'];
                $discount = $item['discount'] ?? 0;
                $taxRate = $item['tax_rate'] ?? 0;

                $itemSubtotal = ($price * $qty) - $discount;
                $taxAmount = ($itemSubtotal * $taxRate) / 100;
                $itemTotal = $itemSubtotal + $taxAmount;

                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'purchase_price' => $price,
                    'discount' => $discount,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'total' => $itemTotal,
                ];
            }

            $totalAmount = $subtotal - ($validated['discount'] ?? 0) + ($validated['tax'] ?? 0);

            // Create purchase
            $purchase = Purchase::create([
                'invoice_no' => $validated['invoice_no'],
                'party_id' => $validated['party_id'],
                'purchase_date' => $validated['purchase_date'],
                'due_date' => $validated['due_date'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $validated['discount'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'total_amount' => $totalAmount,
                'paid_amount' => $validated['paid_amount'] ?? 0,
                'payment_method' => $validated['payment_method'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create purchase items
            foreach ($itemsData as $itemData) {
                $purchase->items()->create($itemData);

                // Update product stock if status is received
                if ($validated['status'] === 'received') {
                    $product = Product::find($itemData['product_id']);
                    $product->increment('stock', $itemData['quantity']);
                }
            }
        });

        return redirect()->route('purchases.index')->with('success', __('Purchase created successfully.'));
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['party', 'items.product']);

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $parties = Party::where('type', 'supplier')->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $purchase->load(['party', 'items.product']);

        return view('purchases.edit', compact('purchase', 'parties', 'products'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|string|unique:purchases,invoice_no,' . $purchase->id,
            'party_id' => 'required|exists:parties,id',
            'purchase_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:purchase_date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'status' => 'required|in:draft,ordered,received,cancelled',
            'notes' => 'nullable|string',
        ]);

        $purchase->update($validated);

        return redirect()->route('purchases.index')->with('success', __('Purchase updated successfully.'));
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()->route('purchases.index')->with('success', __('Purchase deleted successfully.'));
    }
}
