<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ $party->name }}
                </h2>
                <p class="text-sm text-gray-500">{{ __(':type party since :date', ['type' => ucfirst($party->type), 'date' => $party->created_at?->format('d M Y')]) }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-semibold">
                    {{ __('Balance: :amount', ['amount' => currency_format($party->getCurrentBalance())]) }}
                </span>
                <a href="{{ route('parties.edit', $party) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-sm font-medium text-gray-500 uppercase">{{ __('Opening Balance') }}</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ currency_format($party->opening_balance ?? 0) }}</p>
                <p class="mt-1 text-xs text-gray-500 capitalize">{{ __('Balance type') }}: {{ __($party->balance_type) }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-sm font-medium text-gray-500 uppercase">{{ __('Credit Limit') }}</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900">
                    {{ $party->credit_limit ? currency_format($party->credit_limit) : __('No limit set') }}
                </p>
                <p class="mt-1 text-xs text-gray-500">{{ __('Credit days: :days', ['days' => $party->credit_days ?? '—']) }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-sm font-medium text-gray-500 uppercase">{{ __('Total Purchases') }}</h3>
                @php
                    $totalPurchases = $party->purchases->sum('total_amount');
                    $totalPayments = $party->payments->sum('amount');
                @endphp
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ currency_format($totalPurchases) }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ __('Payments made: :amount', ['amount' => currency_format($totalPayments)]) }}</p>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 space-y-6">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Contact Information') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600">
                <div>
                    <span class="block text-xs font-semibold text-gray-500 uppercase">{{ __('Party Type') }}</span>
                    <span class="mt-1 block text-gray-900 capitalize">{{ __($party->type) }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-500 uppercase">{{ __('GSTIN') }}</span>
                    <span class="mt-1 block text-gray-900">{{ $party->gstin ?? '—' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-500 uppercase">{{ __('Phone') }}</span>
                    <span class="mt-1 block text-gray-900">{{ $party->phone ?? '—' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-500 uppercase">{{ __('Email') }}</span>
                    <span class="mt-1 block text-gray-900">{{ $party->email ?? '—' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-500 uppercase">{{ __('Billing Address') }}</span>
                    <span class="mt-1 block text-gray-900 whitespace-pre-line">{{ $party->billing_address ?? '—' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-500 uppercase">{{ __('Shipping Address') }}</span>
                    <span class="mt-1 block text-gray-900 whitespace-pre-line">{{ $party->shipping_address ?? '—' }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Recent Purchases') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Invoice') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($party->purchases->sortByDesc('date')->take(5) as $purchase)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $purchase->reference ?? __('Purchase #:id', ['id' => $purchase->id]) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $purchase->date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-900 font-semibold">{{ currency_format($purchase->total_amount) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                                    {{ __('No purchases recorded yet.') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Recent Payments') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Reference') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($party->payments->sortByDesc('date')->take(5) as $payment)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $payment->reference ?? __('Payment #:id', ['id' => $payment->id]) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $payment->date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-900 font-semibold">{{ currency_format($payment->amount) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                                    {{ __('No payments recorded yet.') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


