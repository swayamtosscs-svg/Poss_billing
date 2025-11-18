<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Edit Stock Adjustment') }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('Update details or correct a previous adjustment entry.') }}</p>
            </div>
            <a href="{{ route('stock-adjustments.show', $stockAdjustment) }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                {{ __('View adjustment') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        @include('stock-adjustments._form', [
            'stockAdjustment' => $stockAdjustment,
            'products' => $products,
            'reasonOptions' => $reasonOptions,
        ])
    </div>
</x-app-layout>





