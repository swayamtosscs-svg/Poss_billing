<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('New Stock Adjustment') }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('Correct inventory counts for damage, discrepancies, or stock found.') }}</p>
            </div>
            <a href="{{ route('stock-adjustments.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                {{ __('Back to list') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        @include('stock-adjustments._form', [
            'products' => $products,
            'reasonOptions' => $reasonOptions,
            'adjustmentNumber' => $adjustmentNumber,
        ])
    </div>
</x-app-layout>





