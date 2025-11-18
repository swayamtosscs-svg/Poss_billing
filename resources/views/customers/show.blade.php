<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $customer->name }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Customer since :date', ['date' => $customer->created_at?->format('d M Y')]) }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-sm font-semibold">
                    {{ __('Points: :points', ['points' => $customer->points]) }}
                </span>
                <a href="{{ route('customers.edit', $customer) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Total Spent') }}</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ currency_format($stats['total_spent']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Orders') }}</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['orders_count'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Last Purchase') }}</h3>
                <p class="mt-2 text-xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ $stats['last_purchase'] ? $stats['last_purchase']->format('d M Y H:i') : __('No purchases yet') }}
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Contact Information') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-300">
                <div><span class="font-semibold">{{ __('Email') }}:</span> {{ $customer->email ?? '—' }}</div>
                <div><span class="font-semibold">{{ __('Phone') }}:</span> {{ $customer->phone ?? '—' }}</div>
                <div class="md:col-span-2"><span class="font-semibold">{{ __('Address') }}:</span> {{ $customer->address ?? '—' }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Recent Purchases') }}</h3>
                <a href="{{ route('sales.index', ['customer' => $customer->id]) }}" class="text-blue-600 hover:text-blue-500 text-sm">{{ __('View all') }}</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Items') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Discount') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Payment') }}</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($customer->sales as $sale)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $sale->date?->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                <ul class="space-y-1">
                                    @foreach($sale->items as $item)
                                        <li>{{ $item->product?->name }} × {{ $item->quantity }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-200">{{ currency_format($sale->total_amount) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-200">{{ currency_format($sale->discount) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-200">{{ ucfirst($sale->payment_type) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('No purchases yet.') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

