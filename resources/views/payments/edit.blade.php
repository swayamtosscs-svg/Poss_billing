<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Edit Payment') }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('Update payment details or adjust linked records.') }}</p>
            </div>
            <a href="{{ route('payments.show', $payment) }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                {{ __('View payment') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">
        @include('payments._form', [
            'payment' => $payment,
            'parties' => $parties,
            'bankAccounts' => $bankAccounts,
            'purchases' => $purchases,
            'sales' => $sales,
        ])
    </div>
</x-app-layout>





