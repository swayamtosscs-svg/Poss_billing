<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Expenses') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Monitor operating expenses, receipts, and payouts.') }}</p>
            </div>
            <a href="{{ route('expenses.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                {{ __('Add Expense') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Today') }}</div>
                <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ currency_format($totals['today'] ?? 0) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('This Month') }}</div>
                <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ currency_format($totals['month'] ?? 0) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Overall Spend') }}</div>
                <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ currency_format($totals['overall'] ?? 0) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Search') }}</label>
                    <input type="text"
                           name="search"
                           value="{{ $filters['search'] ?? '' }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="{{ __('Expense number, description or vendor') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Category') }}</label>
                    <select name="category"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('All categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category'] ?? '') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Payment Method') }}</label>
                    <select name="payment_method"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('All methods') }}</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method }}" @selected(($filters['payment_method'] ?? '') === $method)>
                                {{ ucfirst($method) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('From date') }}</label>
                    <input type="date"
                           name="from"
                           value="{{ $filters['from'] ?? '' }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('To date') }}</label>
                    <input type="date"
                           name="to"
                           value="{{ $filters['to'] ?? '' }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div class="md:col-span-6 flex items-end gap-3">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('expenses.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Expense #') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Category') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Party') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Payment') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Amount') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($expenses as $expense)
                    <tr>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $expense->expense_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $expense->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $expense->party?->name ?? __('General') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $expense->expense_date?->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            <div>{{ $expense->payment_method ? ucfirst($expense->payment_method) : __('Not set') }}</div>
                            @if($expense->bankAccount)
                                <div class="text-xs text-gray-500 dark:text-gray-500">{{ $expense->bankAccount->account_name }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-white">{{ currency_format($expense->amount) }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium">
                            <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 mr-3">{{ __('View') }}</a>
                            <a href="{{ route('expenses.edit', $expense) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 mr-3">{{ __('Edit') }}</a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline"
                                  onsubmit="return confirm('{{ __('Delete this expense?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No expenses found for the selected filters.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 bg-gray-100 dark:bg-gray-700">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
</x-app-layout>



