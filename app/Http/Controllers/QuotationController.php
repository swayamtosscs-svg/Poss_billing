<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with('customer')->latest();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($customerQuery) => $customerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $quotations = $query->paginate(15)->withQueryString();

        $statusCounts = Quotation::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalValue = Quotation::sum('total_amount');
        $filters = $request->only(['search', 'status']);

        return view('quotations.index', compact('quotations', 'statusCounts', 'totalValue', 'filters'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        $lastQuotation = Quotation::latest('id')->first();
        $quotationNumber = 'QUO-' . str_pad(($lastQuotation->id ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        return view('quotations.create', compact('customers', 'products', 'quotationNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quotation_number' => 'required|string|unique:quotations,quotation_number',
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:quotation_date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,sent,accepted,rejected,expired',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        $quotation = DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $itemsData = [];
            $totalTaxFromItems = 0;

            foreach ($validated['items'] as $item) {
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $taxRate = (float) ($item['tax_rate'] ?? 0);

                $rowSubtotal = max(($unitPrice * $quantity) - $discount, 0);
                $rowTax = ($rowSubtotal * $taxRate) / 100;
                $rowTotal = $rowSubtotal + $rowTax;

                $subtotal += $rowSubtotal;
                $totalTaxFromItems += $rowTax;

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $rowTax,
                    'total' => $rowTotal,
                ];
            }

            $discountValue = (float) ($validated['discount'] ?? 0);
            $taxInput = $validated['tax'] ?? null;
            $taxValue = $taxInput !== null ? (float) $taxInput : $totalTaxFromItems;
            $totalAmount = max($subtotal - $discountValue + $taxValue, 0);

            $quotation = Quotation::create([
                'quotation_number' => $validated['quotation_number'],
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discountValue,
                'tax' => $taxValue,
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $quotation->items()->createMany($itemsData);

            return $quotation;
        });

        return redirect()->route('quotations.show', $quotation)->with('success', __('Quotation created successfully.'));
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'items.product']);

        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $quotation->load(['items.product']);
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('quotations.edit', compact('quotation', 'customers', 'products'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'quotation_number' => 'required|string|unique:quotations,quotation_number,' . $quotation->id,
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:quotation_date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,sent,accepted,rejected,expired',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $quotation) {
            $subtotal = 0;
            $itemsData = [];
            $totalTaxFromItems = 0;

            foreach ($validated['items'] as $item) {
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $taxRate = (float) ($item['tax_rate'] ?? 0);

                $rowSubtotal = max(($unitPrice * $quantity) - $discount, 0);
                $rowTax = ($rowSubtotal * $taxRate) / 100;
                $rowTotal = $rowSubtotal + $rowTax;

                $subtotal += $rowSubtotal;
                $totalTaxFromItems += $rowTax;

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $rowTax,
                    'total' => $rowTotal,
                ];
            }

            $discountValue = (float) ($validated['discount'] ?? 0);
            $taxInput = $validated['tax'] ?? null;
            $taxValue = $taxInput !== null ? (float) $taxInput : $totalTaxFromItems;
            $totalAmount = max($subtotal - $discountValue + $taxValue, 0);

            $quotation->update([
                'quotation_number' => $validated['quotation_number'],
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discountValue,
                'tax' => $taxValue,
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $quotation->items()->delete();
            $quotation->items()->createMany($itemsData);
        });

        return redirect()->route('quotations.show', $quotation)->with('success', __('Quotation updated successfully.'));
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', __('Quotation deleted successfully.'));
    }
}
