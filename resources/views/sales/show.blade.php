<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Invoice #:number', ['number' => str_pad($sale->id, 5, '0', STR_PAD_LEFT)]) }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $sale->date?->format('d M Y H:i') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('sales.index') }}" class="text-sm text-blue-600 hover:text-blue-500">{{ __('Back to Sales') }}</a>
                <a href="{{ route('sales.invoice', $sale) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md shadow hover:bg-emerald-500">
                    {{ __('Download PDF') }}
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                    {{ __('Print Invoice') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-6 print:shadow-none print:border print:border-gray-300">
            <div class="flex justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ config('app.name', 'BillingPOS') }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Invoice Summary') }}</p>
                </div>
                <div class="text-right text-sm text-gray-600 dark:text-gray-300">
                    <p>{{ __('Payment Method') }}: <span class="font-semibold">{{ ucfirst($sale->payment_type) }}</span></p>
                    <p>{{ __('Total Amount') }}: <span class="font-semibold">{{ currency_format($sale->total_amount) }}</span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Billed To') }}</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $sale->customer?->name ?? __('Walk-in Customer') }}</p>
                    @if($sale->customer)
                        <p class="text-gray-500 dark:text-gray-400">{{ $sale->customer->email }}</p>
                        <p class="text-gray-500 dark:text-gray-400">{{ $sale->customer->phone }}</p>
                        <p class="text-gray-500 dark:text-gray-400">{{ $sale->customer->address }}</p>
                    @endif
                </div>
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Invoice Details') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Invoice Date') }}: {{ $sale->date?->format('d M Y H:i') }}</p>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Subtotal') }}: {{ currency_format($sale->items->sum('total')) }}</p>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Discount') }}: {{ currency_format($sale->discount) }}</p>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Tax') }}: {{ currency_format($sale->tax) }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('Item') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">{{ __('Qty') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">{{ __('Price') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">{{ __('Total') }}</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($sale->items as $item)
                        <tr>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $item->product?->name ?? __('Product') }}</td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ currency_format($item->price) }}</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100 font-semibold">{{ currency_format($item->total) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end">
                <div class="w-full md:w-1/2">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                        <span>{{ __('Subtotal') }}</span>
                        <span>{{ currency_format($sale->items->sum('total')) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                        <span>{{ __('Discount') }}</span>
                        <span>{{ currency_format($sale->discount) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                        <span>{{ __('Tax') }}</span>
                        <span>{{ currency_format($sale->tax) }}</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-4 pt-4 flex justify-between text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <span>{{ __('Total Due') }}</span>
                        <span>{{ currency_format($sale->total_amount) }}</span>
                    </div>
                </div>
            </div>

            <p class="text-center text-xs text-gray-500 dark:text-gray-400 pt-6 border-t border-dashed border-gray-300 dark:border-gray-700">
                {{ __('Thank you for shopping with us! This invoice was generated electronically and is valid without a signature.') }}
            </p>
        </div>
    </div>
</x-app-layout>

