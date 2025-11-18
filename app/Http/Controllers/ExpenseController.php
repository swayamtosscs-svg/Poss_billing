<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Party;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'party', 'bankAccount'])
            ->latest('expense_date')
            ->latest();

        if ($request->filled('category')) {
            $query->where('expense_category_id', $request->integer('category'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->string('payment_method'));
        }

        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', $request->date('to'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('party', fn ($partyQuery) => $partyQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $expenses = $query->paginate(15)->withQueryString();

        $categories = ExpenseCategory::orderBy('name')->get();
        $paymentMethods = Expense::query()
            ->select('payment_method')
            ->whereNotNull('payment_method')
            ->distinct()
            ->pluck('payment_method')
            ->filter()
            ->values();

        $totals = [
            'month' => Expense::whereBetween('expense_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])->sum('amount'),
            'today' => Expense::whereDate('expense_date', Carbon::today())->sum('amount'),
            'overall' => Expense::sum('amount'),
        ];

        $filters = $request->only(['search', 'category', 'payment_method', 'from', 'to']);

        return view('expenses.index', compact('expenses', 'categories', 'paymentMethods', 'totals', 'filters'));
    }

    public function create()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $bankAccounts = BankAccount::orderBy('account_name')->get();

        $lastExpense = Expense::latest('id')->first();
        $expenseNumber = 'EXP-' . str_pad(($lastExpense->id ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        return view('expenses.create', compact('categories', 'parties', 'bankAccounts', 'expenseNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'party_id' => 'nullable|exists:parties,id',
            'expense_number' => 'required|string|unique:expenses,expense_number',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $expense = Expense::create([
            'expense_category_id' => $validated['expense_category_id'],
            'party_id' => $validated['party_id'] ?? null,
            'expense_number' => $validated['expense_number'],
            'expense_date' => $validated['expense_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'] ?? null,
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'receipt_path' => $receiptPath,
        ]);

        return redirect()->route('expenses.show', $expense)->with('success', __('Expense recorded successfully.'));
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'party', 'bankAccount']);

        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $expense->load(['category', 'party', 'bankAccount']);

        $categories = ExpenseCategory::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $bankAccounts = BankAccount::orderBy('account_name')->get();

        return view('expenses.edit', compact('expense', 'categories', 'parties', 'bankAccounts'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'party_id' => 'nullable|exists:parties,id',
            'expense_number' => 'required|string|unique:expenses,expense_number,' . $expense->id,
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($request->hasFile('receipt')) {
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $expense->receipt_path = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->update([
            'expense_category_id' => $validated['expense_category_id'],
            'party_id' => $validated['party_id'] ?? null,
            'expense_number' => $validated['expense_number'],
            'expense_date' => $validated['expense_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'] ?? null,
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'receipt_path' => $expense->receipt_path,
        ]);

        return redirect()->route('expenses.show', $expense)->with('success', __('Expense updated successfully.'));
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', __('Expense deleted successfully.'));
    }
}
