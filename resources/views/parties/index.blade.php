<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900">
                {{ __('Suppliers & Parties') }}
            </h2>
            <a href="{{ route('parties.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                {{ __('Add Party') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white shadow rounded-lg p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search', '') }}"
                           placeholder="{{ __('Name, phone, email or GSTIN') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Party Type') }}</label>
                    <select name="type"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                        <option value="">{{ __('All') }}</option>
                        <option value="supplier" @selected(request('type') === 'supplier')>{{ __('Suppliers') }}</option>
                        <option value="customer" @selected(request('type') === 'customer')>{{ __('Customers') }}</option>
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('parties.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Party') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Contact') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('GSTIN') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Opening Balance') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Credit Limit') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($parties as $party)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="text-sm font-semibold text-gray-900">{{ $party->name }}</div>
                            <div class="text-xs text-gray-500 capitalize">{{ __($party->type) }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <div>{{ $party->phone ?? '—' }}</div>
                            <div>{{ $party->email ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $party->gstin ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900 font-semibold">{{ currency_format($party->opening_balance ?? 0) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ $party->credit_limit ? currency_format($party->credit_limit) : '—' }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium">
                            <a href="{{ route('parties.show', $party) }}" class="text-blue-600 hover:text-blue-500 mr-3">{{ __('View') }}</a>
                            <a href="{{ route('parties.edit', $party) }}" class="text-indigo-600 hover:text-indigo-500 mr-3">{{ __('Edit') }}</a>
                            <form action="{{ route('parties.destroy', $party) }}" method="POST" class="inline"
                                  onsubmit="return confirm('{{ __('Delete this party?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-500">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                            {{ __('No parties found.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 bg-gray-50">
                {{ $parties->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>


