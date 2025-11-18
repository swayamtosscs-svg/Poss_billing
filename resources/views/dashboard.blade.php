<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php($currencySymbol = currency_symbol())

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('invoices.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 hover:bg-orange-400 text-white font-semibold rounded-lg shadow transition">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ __('Create Invoice') }}
            </a>
            <a href="{{ route('sales.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-400 text-white font-semibold rounded-lg shadow transition">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                {{ __('POS Sale') }}
            </a>
            <a href="{{ route('products.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-semibold rounded-lg shadow transition">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('Add Product') }}
            </a>
            <a href="{{ route('customers.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-purple-500 hover:bg-purple-400 text-white font-semibold rounded-lg shadow transition">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                {{ __('Add Customer') }}
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <div class="text-gray-500 dark:text-gray-400 text-sm">{{ __('Today Sales') }}</div>
                <div class="text-2xl font-semibold mt-2 text-gray-900 dark:text-white">{{ currency_format($todayTotal) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <div class="text-gray-500 dark:text-gray-400 text-sm">{{ __('This Week') }}</div>
                <div class="text-2xl font-semibold mt-2 text-gray-900 dark:text-white">{{ currency_format($weekTotal) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <div class="text-gray-500 dark:text-gray-400 text-sm">{{ __('This Month') }}</div>
                <div class="text-2xl font-semibold mt-2 text-gray-900 dark:text-white">{{ currency_format($monthTotal) }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Sales Overview') }}</h3>
                </div>
                <canvas id="salesChart" height="120"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Top Products') }}</h3>
                <ul class="space-y-2">
                    @foreach($topProducts as $p)
                        <li class="flex items-center justify-between">
                            <span class="text-gray-700 dark:text-gray-300">{{ $p['name'] }}</span>
                            <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $p['qty'] }}</span>
                        </li>
                    @endforeach
                </ul>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mt-6 mb-2">{{ __('Low Stock') }}</h3>
                <ul class="space-y-2">
                    @foreach($lowStock as $p)
                        <li class="flex items-center justify-between">
                            <span class="text-gray-700 dark:text-gray-300">{{ $p->name }}</span>
                            <span class="text-red-600 dark:text-red-400 text-sm font-medium">{{ $p->stock }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            // no-op
        });
        window.addEventListener('load', () => {
            const ctx = document.getElementById('salesChart');
            if (ctx && window.Chart) {
                const labels = Array.from({length: 12}, (_, i) => new Date(new Date().getFullYear(), i, 1).toLocaleString('default', { month: 'short' }));
                const data = labels.map(() => Math.floor(Math.random() * 50000) + 5000);
                
                const isDarkMode = document.documentElement.classList.contains('dark');
                const textColor = isDarkMode ? '#e5e7eb' : '#374151';
                const gridColor = isDarkMode ? '#374151' : '#e5e7eb';
                
                new window.Chart(ctx, {
                    type: 'line',
                    data: { labels, datasets: [{ label: '{{ __('Sales') }}', data, borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.2)', tension: 0.35, fill: true }]},
                    options: {
                        plugins: {
                            legend: { 
                                display: true,
                                labels: {
                                    color: textColor
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return '{{ $currencySymbol }}' + context.parsed.y.toLocaleString();
                                    }
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
            }
        });
    </script>
</x-app-layout>
