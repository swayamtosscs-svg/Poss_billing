<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Add Bank Account') }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('Store account details for tracking balances and transactions.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        @include('bank-accounts._form')
    </div>
</x-app-layout>


