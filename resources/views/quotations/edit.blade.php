<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Edit Quotation') }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('Update quotation details before sharing with customers.') }}
                </p>
            </div>
            <a href="{{ route('quotations.show', $quotation) }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                {{ __('View quotation') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('quotations._form', [
            'quotation' => $quotation,
            'customers' => $customers,
            'products' => $products,
        ])
    </div>
</x-app-layout>





