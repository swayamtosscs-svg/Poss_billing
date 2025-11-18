<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Party;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['party', 'bankAccount', 'paymentable'])->latest('payment_date');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->string('payment_method'));
        }

        if ($request->filled('party_id')) {
            $query->where('party_id', $request->integer('party_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('payment_date', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('payment_date', '<=', $request->date('to'));
        }

        if ($request->filled('linked_to')) {
            $class = $this->paymentableMap()[$request->string('linked_to')] ?? null;
            if ($class) {
                $query->where('paymentable_type', $class);
            }
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('party', fn ($partyQuery) => $partyQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        $parties = Party::orderBy('name')->get(['id', 'name']);
        $paymentMethods = Payment::select('payment_method')->distinct()->pluck('payment_method');

        $stats = [
            'in' => Payment::where('type', 'in')->sum('amount'),
            'out' => Payment::where('type', 'out')->sum('amount'),
            'net' => Payment::selectRaw("
                SUM(CASE WHEN type = 'in' THEN amount ELSE -amount END) as balance
            ")->value('balance') ?? 0,
        ];

        $filters = $request->only(['search', 'type', 'payment_method', 'party_id', 'from', 'to', 'linked_to']);
        $linkedOptions = $this->linkedFilterOptions();

        return view('payments.index', compact('payments', 'parties', 'paymentMethods', 'stats', 'filters', 'linkedOptions'));
    }

    public function create()
    {
        $parties = Party::orderBy('name')->get();
        $bankAccounts = BankAccount::orderBy('account_name')->get();
        $purchases = Purchase::latest('id')->take(50)->get();
        $sales = Sale::latest('id')->take(50)->get();

        $lastPayment = Payment::latest('id')->first();
        $paymentNumber = 'PAY-' . str_pad(($lastPayment->id ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        return view('payments.create', [
            'parties' => $parties,
            'bankAccounts' => $bankAccounts,
            'purchases' => $purchases,
            'sales' => $sales,
            'paymentNumber' => $paymentNumber,
            'linkedOptions' => $this->linkedFilterOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);

        DB::transaction(function () use ($validated) {
            $paymentable = $this->resolvePaymentable($validated);

            $payment = Payment::create([
                'payment_number' => $validated['payment_number'],
                'type' => $validated['type'],
                'party_id' => $validated['party_id'],
                'paymentable_type' => get_class($paymentable),
                'paymentable_id' => $paymentable->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'bank_account_id' => $validated['bank_account_id'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->applyPaymentEffects($payment, 1);
        });

        return redirect()->route('payments.index')->with('success', __('Payment recorded successfully.'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['party', 'bankAccount', 'paymentable']);
        $linkedOptions = $this->linkedFilterOptions();

        return view('payments.show', compact('payment', 'linkedOptions'));
    }

    public function edit(Payment $payment)
    {
        $payment->load(['party', 'bankAccount', 'paymentable']);

        $parties = Party::orderBy('name')->get();
        $bankAccounts = BankAccount::orderBy('account_name')->get();
        $purchases = Purchase::latest('id')->take(50)->get();
        $sales = Sale::latest('id')->take(50)->get();

        return view('payments.edit', [
            'payment' => $payment,
            'parties' => $parties,
            'bankAccounts' => $bankAccounts,
            'purchases' => $purchases,
            'sales' => $sales,
            'linkedOptions' => $this->linkedFilterOptions(),
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $this->validatedData($request, $payment->id);

        DB::transaction(function () use ($validated, $payment) {
            $this->applyPaymentEffects($payment, -1);

            $paymentable = $this->resolvePaymentable($validated);

            $payment->update([
                'payment_number' => $validated['payment_number'],
                'type' => $validated['type'],
                'party_id' => $validated['party_id'],
                'paymentable_type' => get_class($paymentable),
                'paymentable_id' => $paymentable->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'bank_account_id' => $validated['bank_account_id'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $payment->load(['paymentable', 'bankAccount']);

            $this->applyPaymentEffects($payment, 1);
        });

        return redirect()->route('payments.show', $payment)->with('success', __('Payment updated successfully.'));
    }

    public function destroy(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            $this->applyPaymentEffects($payment, -1);
            $payment->delete();
        });

        return redirect()->route('payments.index')->with('success', __('Payment deleted successfully.'));
    }

    protected function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'payment_number' => ['required', 'string', Rule::unique('payments', 'payment_number')->ignore($ignoreId)],
            'type' => ['required', 'in:in,out'],
            'party_id' => ['nullable', 'exists:parties,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'max:100'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'purchase_id' => ['nullable', 'exists:purchases,id'],
            'sale_id' => ['nullable', 'exists:sales,id'],
        ]);

        if (empty($data['purchase_id']) && empty($data['sale_id'])) {
            throw ValidationException::withMessages([
                'purchase_id' => __('Select at least one linked record (purchase or sale).'),
            ]);
        }

        if (!empty($data['purchase_id']) && !empty($data['sale_id'])) {
            throw ValidationException::withMessages([
                'purchase_id' => __('Select either a purchase or a sale, not both.'),
            ]);
        }

        return $data;
    }

    protected function resolvePaymentable(array $validated)
    {
        if (!empty($validated['purchase_id'])) {
            return Purchase::findOrFail($validated['purchase_id']);
        }

        if (!empty($validated['sale_id'])) {
            return Sale::findOrFail($validated['sale_id']);
        }

        throw ValidationException::withMessages([
            'purchase_id' => __('Unable to determine the linked record for this payment.'),
        ]);
    }

    protected function applyPaymentEffects(Payment $payment, int $multiplier): void
    {
        if ($payment->paymentable instanceof Purchase) {
            $purchase = Purchase::lockForUpdate()->find($payment->paymentable_id);
            if ($purchase) {
                $newPaidAmount = max(0, $purchase->paid_amount + ($payment->amount * $multiplier));
                $purchase->update(['paid_amount' => $newPaidAmount]);
            }
        }

        if ($payment->bank_account_id) {
            $bankAccount = BankAccount::lockForUpdate()->find($payment->bank_account_id);
            if ($bankAccount) {
                $delta = $payment->type === 'in'
                    ? $payment->amount * $multiplier
                    : -$payment->amount * $multiplier;

                $newBalance = $bankAccount->current_balance + $delta;
                $bankAccount->update(['current_balance' => $newBalance]);
            }
        }
    }

    protected function linkedFilterOptions(): array
    {
        return [
            'purchase' => __('Purchase'),
            'sale' => __('Sale'),
        ];
    }

    protected function paymentableMap(): array
    {
        return [
            'purchase' => Purchase::class,
            'sale' => Sale::class,
        ];
    }
}
