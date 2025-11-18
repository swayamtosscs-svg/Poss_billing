<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Record Expense') }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('Log a new business expense and attach supporting receipts.') }}</p>
            </div>
            <a href="{{ route('expenses.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                {{ __('Back to expenses') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">
        @include('expenses._form', [
            'categories' => $categories,
            'parties' => $parties,
            'bankAccounts' => $bankAccounts,
            'expenseNumber' => $expenseNumber,
        ])
    </div>
</x-app-layout>





