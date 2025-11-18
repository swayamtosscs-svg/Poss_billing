<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ $bankAccount->account_name }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('Managed under :bank', ['bank' => $bankAccount->bank_name]) }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('bank-accounts.edit', $bankAccount) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-500">
                    {{ __('Edit') }}
                </a>
                <form action="{{ route('bank-accounts.destroy', $bankAccount) }}"
                      method="POST"
                      onsubmit="return confirm('{{ __('Delete this account?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md shadow hover:bg-red-500">
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Opening Balance') }}</div>
                <div class="mt-2 text-2xl font-semibold text-gray-900">{{ currency_format($bankAccount->opening_balance) }}</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Current Balance') }}</div>
                <div class="mt-2 text-2xl font-semibold text-gray-900">{{ currency_format($bankAccount->current_balance) }}</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4 flex flex-col justify-between">
                <div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Status') }}</div>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $bankAccount->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-600' }}">
                            {{ $bankAccount->is_active ? __('Active') : __('Inactive') }}
                        </span>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    {{ __('Account #:number', ['number' => $bankAccount->account_number]) }}
                    @if($bankAccount->ifsc_code)
                        <div>{{ __('IFSC / Routing: :code', ['code' => $bankAccount->ifsc_code]) }}</div>
                    @endif
                    @if($bankAccount->branch)
                        <div>{{ __('Branch: :branch', ['branch' => $bankAccount->branch]) }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="border-b border-gray-200 px-4 py-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Recent Payments') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Payments linked to this account.') }}</p>
                    </div>
                    <a href="{{ route('payments.index', ['bank_account_id' => $bankAccount->id]) }}"
                       class="text-xs text-blue-600 hover:text-blue-500">
                        {{ __('View all') }}
                    </a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentPayments as $payment)
                        <div class="px-4 py-3 text-sm text-gray-700 flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-900">
                                    {{ __('Payment #:number', ['number' => $payment->payment_number ?? $payment->id]) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ optional($payment->payment_date)->format('d M Y') ?? __('Not dated') }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold {{ $payment->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ currency_format($payment->amount) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $payment->payment_method ? ucfirst($payment->payment_method) : __('Not set') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-gray-500">
                            {{ __('No payments recorded yet.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="border-b border-gray-200 px-4 py-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Recent Expenses') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Expenses paid using this account.') }}</p>
                    </div>
                    <a href="{{ route('expenses.index', ['bank_account_id' => $bankAccount->id]) }}"
                       class="text-xs text-blue-600 hover:text-blue-500">
                        {{ __('View all') }}
                    </a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentExpenses as $expense)
                        <div class="px-4 py-3 text-sm text-gray-700 flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-900">
                                    {{ __('Expense #:number', ['number' => $expense->expense_number ?? $expense->id]) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ optional($expense->expense_date)->format('d M Y') ?? __('Not dated') }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-red-600">
                                    {{ currency_format($expense->amount) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $expense->payment_method ? ucfirst($expense->payment_method) : __('Not set') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-gray-500">
                            {{ __('No expenses recorded yet.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


