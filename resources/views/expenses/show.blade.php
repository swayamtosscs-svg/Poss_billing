<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ $expense->expense_number }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('Expense recorded on :date', ['date' => $expense->expense_date?->format('d M Y')]) }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('expenses.edit', $expense) }}"
                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-500">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('expenses.index') }}"
                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-500">
                    {{ __('Back to list') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Expense Info') }}</h3>
                <dl class="mt-4 space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between">
                        <dt>{{ __('Category') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $expense->category?->name ?? __('Not set') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Amount') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ currency_format($expense->amount) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Payment Method') }}</dt>
                        <dd>{{ $expense->payment_method ? ucfirst($expense->payment_method) : __('Not recorded') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Bank Account') }}</dt>
                        <dd>{{ $expense->bankAccount?->account_name ?? __('Not linked') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Vendor / Party') }}</h3>
                <dl class="mt-4 space-y-2 text-sm text-gray-700">
                    <div>
                        <dt class="text-xs uppercase text-gray-400">{{ __('Name') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $expense->party?->name ?? __('General expense') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-gray-400">{{ __('Phone') }}</dt>
                        <dd>{{ $expense->party?->phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-gray-400">{{ __('Email') }}</dt>
                        <dd>{{ $expense->party?->email ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Receipt') }}</h3>
                <div class="mt-4 text-sm text-gray-700">
                    @if($expense->receipt_path)
                        @php($receiptUrl = Storage::disk('public')->url($expense->receipt_path))
                        <a href="{{ $receiptUrl }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-md border border-blue-200 hover:bg-blue-100">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 5v14m7-7H5"/>
                            </svg>
                            {{ __('View attachment') }}
                        </a>
                        <p class="mt-2 text-xs text-gray-500">{{ basename($expense->receipt_path) }}</p>
                    @else
                        <p class="text-gray-500">{{ __('No receipt uploaded for this expense.') }}</p>
                    @endif
                </div>
            </div>
        </div>

        @if($expense->description)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('Description') }}</h3>
                <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $expense->description }}</p>
            </div>
        @endif
    </div>
</x-app-layout>





