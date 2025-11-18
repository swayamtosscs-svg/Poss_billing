<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Customers') }}
            </h2>
            <a href="{{ route('customers.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                {{ __('Add Customer') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6"
         x-data="{
             selectedCustomers: [],
             allCustomerIds: [{{ $customers->map(fn($c) => '"' . $c->id . '"')->join(',') }}],
             get selectAll() {
                 return this.allCustomerIds.length > 0 && this.allCustomerIds.every(id => this.selectedCustomers.includes(id));
             },
             set selectAll(value) {
                 this.selectedCustomers = value ? [...this.allCustomerIds] : [];
             }
         }">
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Search') }}</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                           placeholder="{{ __('Name, email or phone') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Minimum Points') }}</label>
                    <input type="number" name="min_points" min="0" value="{{ $filters['min_points'] ?? '' }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sort By') }}</label>
                    <select name="sort"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="recent" @selected(($filters['sort'] ?? 'recent') === 'recent')>{{ __('Recent activity') }}</option>
                        <option value="top_spenders" @selected(($filters['sort'] ?? '') === 'top_spenders')>{{ __('Top spenders') }}</option>
                        <option value="loyal" @selected(($filters['sort'] ?? '') === 'loyal')>{{ __('Most loyalty points') }}</option>
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('customers.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div x-show="selectedCustomers.length > 0" x-cloak
             class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center justify-between">
            <span class="text-sm font-medium text-red-800">
                <span x-text="selectedCustomers.length"></span> {{ __('customer(s) selected') }}
            </span>
            <form method="POST" action="{{ route('customers.bulk-destroy') }}" class="inline"
                  x-ref="bulkDeleteForm"
                  @submit.prevent="if(confirm('{{ __('Are you sure you want to delete the selected customers? This action cannot be undone.') }}')) { $refs.bulkDeleteForm.submit(); }">
                @csrf
                <template x-for="customerId in selectedCustomers" :key="customerId">
                    <input type="hidden" name="customer_ids[]" :value="customerId">
                </template>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md shadow hover:bg-red-500">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M6 7h12M9 7V4h6v3m-8 4h10l-1 9H8l-1-9z"/>
                    </svg>
                    {{ __('Delete Selected') }}
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left w-12">
                        <input type="checkbox" x-model="selectAll"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Customer') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Contact') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Orders') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Spent') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Loyalty Points') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($customers as $customer)
                    <tr>
                        <td class="px-4 py-3">
                            <input type="checkbox" value="{{ $customer->id }}" x-model="selectedCustomers"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $customer->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->created_at?->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            <div>{{ $customer->email ?? '—' }}</div>
                            <div>{{ $customer->phone ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-200">{{ $customer->sales_count }}</td>
                        <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-200">{{ currency_format($customer->total_spent ?? 0) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                {{ $customer->points }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-medium">
                            <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-500 mr-3">{{ __('View') }}</a>
                            <a href="{{ route('customers.edit', $customer) }}" class="text-indigo-600 hover:text-indigo-500 mr-3">{{ __('Edit') }}</a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline"
                                  onsubmit="return confirm('{{ __('Delete this customer?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-500">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No customers found.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

