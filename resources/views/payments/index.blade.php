<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Payments') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Track incoming receipts and outgoing supplier payments.') }}</p>
            </div>
            <a href="{{ route('payments.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                {{ __('New Payment') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Received') }}</div>
                <div class="mt-2 text-2xl font-semibold text-emerald-600 dark:text-emerald-400">{{ currency_format($stats['in'] ?? 0) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Paid') }}</div>
                <div class="mt-2 text-2xl font-semibold text-red-600 dark:text-red-400">{{ currency_format($stats['out'] ?? 0) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Net Cash Flow') }}</div>
                <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ currency_format($stats['net'] ?? 0) }}
                </div>
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
                           placeholder="{{ __('Payment number, reference or notes') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Party') }}</label>
                    <select name="party_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('All parties') }}</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" @selected(($filters['party_id'] ?? '') == $party->id)>
                                {{ $party->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Type') }}</label>
                    <select name="type"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('All types') }}</option>
                        <option value="in" @selected(($filters['type'] ?? '') === 'in')>{{ __('Payment In') }}</option>
                        <option value="out" @selected(($filters['type'] ?? '') === 'out')>{{ __('Payment Out') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Method') }}</label>
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Linked To') }}</label>
                    <select name="linked_to"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('All records') }}</option>
                        @foreach($linkedOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['linked_to'] ?? '') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('From') }}</label>
                    <input type="date"
                           name="from"
                           value="{{ $filters['from'] ?? '' }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('To') }}</label>
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
                    <a href="{{ route('payments.index') }}"
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
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Payment #') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Party') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Type / Method') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Reference') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Linked Record') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Amount') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $payment->payment_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            <div>{{ $payment->party?->name ?? __('—') }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $payment->payment_date?->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $payment->type === 'in' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' }}">
                                {{ $payment->type === 'in' ? __('In') : __('Out') }}
                            </span>
                            <span class="ml-2">{{ ucfirst($payment->payment_method) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $payment->reference_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            @if($payment->paymentable instanceof \App\Models\Purchase)
                                {{ __('Purchase #:id', ['id' => $payment->paymentable->id]) }}
                            @elseif($payment->paymentable instanceof \App\Models\Sale)
                                {{ __('Sale #:id', ['id' => $payment->paymentable->id]) }}
                            @else
                                {{ __('—') }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-semibold {{ $payment->type === 'in' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ currency_format($payment->amount) }}
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-medium">
                            <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 mr-3">{{ __('View') }}</a>
                            <a href="{{ route('payments.edit', $payment) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 mr-3">{{ __('Edit') }}</a>
                            <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline"
                                  onsubmit="return confirm('{{ __('Delete this payment?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No payments found for the selected filters.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 bg-gray-100 dark:bg-gray-700">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</x-app-layout>



