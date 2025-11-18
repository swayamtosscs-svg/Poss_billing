<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query()
            ->withSum('sales as total_spent', 'total_amount')
            ->withCount('sales');

        if ($search = $request->input('search')) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($minPoints = $request->input('min_points')) {
            $query->where('points', '>=', (int) $minPoints);
        }

        $sort = $request->input('sort', 'recent');

        $query->when($sort === 'top_spenders', fn ($q) => $q->orderByDesc('total_spent'))
            ->when($sort === 'loyal', fn ($q) => $q->orderByDesc('points'))
            ->when($sort === 'recent', fn ($q) => $q->orderByDesc('updated_at'));

        $customers = $query->paginate(15)->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'min_points', 'sort']),
        ]);
    }

    public function create(): View
    {
        return view('customers.create', [
            'customer' => new Customer(),
        ]);
    }

    public function store(CustomerRequest $request): RedirectResponse
    {
        Customer::create($request->validated());

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['sales' => function ($query) {
            $query->latest()->with('items.product')->limit(20);
        }]);

        $stats = [
            'total_spent' => $customer->sales()->sum('total_amount'),
            'orders_count' => $customer->sales()->count(),
            'last_purchase' => $customer->sales()->latest('date')->first()?->date,
        ];

        return view('customers.show', compact('customer', 'stats'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer removed.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_ids' => ['required', 'array', 'min:1'],
            'customer_ids.*' => ['required', 'integer', 'exists:customers,id'],
        ]);

        $customerIds = $request->input('customer_ids', []);
        
        $deletedCount = Customer::whereIn('id', $customerIds)->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', __(':count customer(s) deleted successfully.', ['count' => $deletedCount]));
    }

    public function search(Request $request): JsonResponse
    {
        $term = $request->input('q');

        $customers = Customer::query()
            ->select(['id', 'name', 'email', 'phone', 'points'])
            ->when($term, function ($query, $term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $customers,
        ]);
    }
}

