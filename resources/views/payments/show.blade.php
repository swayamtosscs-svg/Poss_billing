<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ $payment->payment_number }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('Payment recorded on :date', ['date' => $payment->payment_date?->format('d M Y')]) }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('payments.edit', $payment) }}"
                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-500">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('payments.index') }}"
                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-500">
                    {{ __('Back to list') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Payment Type') }}</span>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $payment->type === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $payment->type === 'in' ? __('Payment In (Received)') : __('Payment Out (Paid)') }}
                        </span>
                    </div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Amount') }}</span>
                    <div class="mt-1 text-sm font-semibold {{ $payment->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ currency_format($payment->amount) }}
                    </div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Party') }}</span>
                    <div class="mt-1 text-sm text-gray-900">{{ $payment->party?->name ?? __('N/A') }}</div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Method') }}</span>
                    <div class="mt-1 text-sm text-gray-900">{{ ucfirst($payment->payment_method) }}</div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Reference Number') }}</span>
                    <div class="mt-1 text-sm text-gray-900">{{ $payment->reference_number ?? __('Not provided') }}</div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Bank Account') }}</span>
                    <div class="mt-1 text-sm text-gray-900">{{ $payment->bankAccount?->account_name ?? __('Not linked') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('Linked Record') }}</h3>
            <div class="mt-3 text-sm text-gray-700">
                @if($payment->paymentable instanceof \App\Models\Purchase)
                    {{ __('Purchase #:id', ['id' => $payment->paymentable->id]) }} —
                    {{ currency_format($payment->paymentable->total_amount) }}
                @elseif($payment->paymentable instanceof \App\Models\Sale)
                    {{ __('Sale #:id', ['id' => $payment->paymentable->id]) }} —
                    {{ currency_format($payment->paymentable->total_amount ?? 0) }}
                @else
                    {{ __('No record linked') }}
                @endif
            </div>
        </div>

        @if($payment->notes)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('Notes') }}</h3>
                <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $payment->notes }}</p>
            </div>
        @endif
    </div>
</x-app-layout>





