<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Create Quotation') }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('Draft a quotation to share with customers.') }}
                </p>
            </div>
            <a href="{{ route('quotations.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                {{ __('Back to quotations') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('quotations._form', [
            'quotationNumber' => $quotationNumber,
            'customers' => $customers,
            'products' => $products,
        ])
    </div>
</x-app-layout>





