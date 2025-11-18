<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Quotations') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Create and track quotations shared with customers.') }}
                </p>
            </div>
            <a href="{{ route('quotations.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                {{ __('New Quotation') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $statusLabels = [
                    'draft' => __('Draft'),
                    'sent' => __('Sent'),
                    'accepted' => __('Accepted'),
                    'rejected' => __('Rejected'),
                    'expired' => __('Expired'),
                ];
            @endphp
            @foreach($statusLabels as $status => $label)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $label }}</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $statusCounts[$status] ?? 0 }}</div>
                </div>
            @endforeach
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Value') }}</div>
                <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ currency_format($totalValue ?? 0) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Search') }}</label>
                    <input type="text"
                           name="search"
                           value="{{ $filters['search'] ?? '' }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="{{ __('Quotation number, customer name or email') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                    <select name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('All statuses') }}</option>
                        @foreach($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end gap-3">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('quotations.index') }}"
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
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Quotation #') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Customer') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Quotation Date') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Valid Until') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Amount') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($quotations as $quotation)
                    <tr>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $quotation->quotation_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            <div>{{ $quotation->customer?->name ?? '—' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->customer?->email }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $quotation->quotation_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $quotation->valid_until?->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                @class([
                                    'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' => $quotation->status === 'draft',
                                    'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' => $quotation->status === 'sent',
                                    'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' => $quotation->status === 'accepted',
                                    'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' => $quotation->status === 'rejected',
                                    'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' => $quotation->status === 'expired',
                                ])">
                                {{ $statusLabels[$quotation->status] ?? ucfirst($quotation->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-white">{{ currency_format($quotation->total_amount) }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium">
                            <a href="{{ route('quotations.show', $quotation) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 mr-3">{{ __('View') }}</a>
                            <a href="{{ route('quotations.edit', $quotation) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 mr-3">{{ __('Edit') }}</a>
                            <form action="{{ route('quotations.destroy', $quotation) }}" method="POST" class="inline"
                                  onsubmit="return confirm('{{ __('Delete this quotation?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No quotations found for the selected filters.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 bg-gray-100 dark:bg-gray-700">
                {{ $quotations->links() }}
            </div>
        </div>
    </div>
</x-app-layout>



