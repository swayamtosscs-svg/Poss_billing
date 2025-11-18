<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Edit Expense') }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('Update expense details or replace receipts.') }}</p>
            </div>
            <a href="{{ route('expenses.show', $expense) }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                {{ __('View expense') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">
        @include('expenses._form', [
            'expense' => $expense,
            'categories' => $categories,
            'parties' => $parties,
            'bankAccounts' => $bankAccounts,
        ])
    </div>
</x-app-layout>





