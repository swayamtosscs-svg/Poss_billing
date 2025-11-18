<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
            {{ __('Sales Reports & Analytics') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('From') }}</label>
                    <input type="date" name="from" value="{{ $from->format('Y-m-d') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('To') }}</label>
                    <input type="date" name="to" value="{{ $to->format('Y-m-d') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Customer') }}</label>
                    <select name="customer"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('All') }}</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(($filters['customer'] ?? null) == $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Category') }}</label>
                    <select name="category"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('All') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category'] ?? null) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Product') }}</label>
                    <select name="product"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('All') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" @selected(($filters['product'] ?? null) == $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                        {{ __('Apply Filters') }}
                    </button>
                    <a href="{{ route('reports.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Revenue') }}</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-2">{{ currency_format($totalRevenue) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Orders') }}</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-2">{{ $ordersCount }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Average Order Value') }}</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-2">{{ currency_format($averageOrder) }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Sales Trend') }}</h3>
                <div class="flex gap-3">
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}"
                       class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white rounded-md shadow hover:bg-emerald-500 text-sm">
                        {{ __('Export Excel') }}
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"
                       class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-500 text-sm">
                        {{ __('Export PDF') }}
                    </a>
                </div>
            </div>
            <div class="h-80">
                <canvas id="salesReportChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Top Products') }}</h3>
                <ul class="space-y-3">
                    @forelse($topProducts as $product)
                        <li class="flex items-center justify-between text-sm text-gray-700 dark:text-gray-300">
                            <span>{{ $product->name }}</span>
                            <span>{{ $product->qty }} {{ __('units') }} ({{ currency_format($product->revenue) }})</span>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500 dark:text-gray-400">{{ __('No data for selected filters.') }}</li>
                    @endforelse
                </ul>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Recent Sales') }}</h3>
                <div class="overflow-y-auto max-h-72">
                    <ul class="space-y-3">
                        @foreach($sales as $sale)
                            <li class="flex items-center justify-between text-sm text-gray-700 dark:text-gray-300">
                                <div>
                                    <p class="font-semibold">#{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $sale->date?->format('d M Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <p>{{ currency_format($sale->total_amount) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $sale->customer?->name ?? __('Walk-in') }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Invoice #') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Customer') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Payment') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Total') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Discount') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Tax') }}</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($sales as $sale)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white font-semibold">#{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $sale->date?->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $sale->customer?->name ?? __('Walk-in Customer') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($sale->payment_type) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900 dark:text-white font-semibold">{{ currency_format($sale->total_amount) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600 dark:text-gray-400">{{ currency_format($sale->discount) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600 dark:text-gray-400">{{ currency_format($sale->tax) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3 bg-gray-100 dark:bg-gray-700">
                {{ $sales->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('salesReportChart');
            if (!ctx || !window.Chart) {
                return;
            }
            const currencySymbol = @json(currency_symbol());
            const data = @json($chartData);
            const labels = data.map(item => item.day);
            const totals = data.map(item => item.total);

            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? '#e5e7eb' : '#374151';
            const gridColor = isDarkMode ? '#374151' : '#e5e7eb';
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                        datasets: [{
                            label: '{{ __('Revenue') }}',
                            data: totals,
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: '#3b82f6',
                            borderWidth: 1
                        }]
                },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: textColor
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => currencySymbol + context.parsed.y.toLocaleString()
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: textColor
                                },
                                grid: {
                                    color: gridColor
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: textColor
                                },
                                grid: {
                                    color: gridColor
                                }
                            }
                        }
                    }
            });
        });
    </script>
</x-app-layout>

