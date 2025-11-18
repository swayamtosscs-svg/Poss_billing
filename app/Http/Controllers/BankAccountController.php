<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankAccountRequest;
use App\Models\BankAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => $request->query('status', 'all'),
        ];

        $accountsQuery = BankAccount::query();

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $accountsQuery->where(function ($query) use ($search) {
                $query->where('account_name', 'like', "%{$search}%")
                    ->orWhere('bank_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        if (in_array($filters['status'], ['active', 'inactive'], true)) {
            $accountsQuery->where('is_active', $filters['status'] === 'active');
        }

        $statsQuery = clone $accountsQuery;

        $accounts = $accountsQuery
            ->orderBy('account_name')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'count' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->where('is_active', true)->count(),
            'inactive' => (clone $statsQuery)->where('is_active', false)->count(),
            'opening_balance' => (float) (clone $statsQuery)->sum('opening_balance'),
            'current_balance' => (float) (clone $statsQuery)->sum('current_balance'),
        ];

        return view('bank-accounts.index', [
            'accounts' => $accounts,
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('bank-accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BankAccountRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        if (! array_key_exists('current_balance', $data) || $data['current_balance'] === null) {
            $data['current_balance'] = $data['opening_balance'];
        }

        $bankAccount = BankAccount::create($data);

        return redirect()
            ->route('bank-accounts.show', $bankAccount)
            ->with('success', __('Bank account created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount): View
    {
        $recentPayments = $bankAccount->payments()
            ->latest()
            ->limit(10)
            ->get();

        $recentExpenses = $bankAccount->expenses()
            ->latest()
            ->limit(10)
            ->get();

        return view('bank-accounts.show', [
            'bankAccount' => $bankAccount,
            'recentPayments' => $recentPayments,
            'recentExpenses' => $recentExpenses,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount): View
    {
        return view('bank-accounts.edit', [
            'bankAccount' => $bankAccount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BankAccountRequest $request, BankAccount $bankAccount): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if (! array_key_exists('current_balance', $data) || $data['current_balance'] === null) {
            $data['current_balance'] = $bankAccount->current_balance;
        }

        $bankAccount->update($data);

        return redirect()
            ->route('bank-accounts.show', $bankAccount)
            ->with('success', __('Bank account updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount): RedirectResponse
    {
        $bankAccount->delete();

        return redirect()
            ->route('bank-accounts.index')
            ->with('success', __('Bank account deleted successfully.'));
    }
}

