<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Edit Purchase') }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('Update supplier invoice details or payment status.') }}</p>
            </div>
            <a href="{{ route('purchases.show', $purchase) }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                {{ __('View purchase') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('purchases._form', [
            'parties' => $parties,
            'products' => $products,
            'purchase' => $purchase,
        ])
    </div>
</x-app-layout>





