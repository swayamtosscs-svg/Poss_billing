<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Record Payment') }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('Capture received or paid amounts and link them to sales or purchases.') }}</p>
            </div>
            <a href="{{ route('payments.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                {{ __('Back to payments') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">
        @include('payments._form', [
            'parties' => $parties,
            'bankAccounts' => $bankAccounts,
            'purchases' => $purchases,
            'sales' => $sales,
            'paymentNumber' => $paymentNumber,
        ])
    </div>
</x-app-layout>





