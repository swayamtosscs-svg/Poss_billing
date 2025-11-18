<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('New Purchase') }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('Record a new supplier invoice or purchase order.') }}</p>
            </div>
            <a href="{{ route('purchases.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                {{ __('Back to purchases') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('purchases._form', [
            'parties' => $parties,
            'products' => $products,
            'invoiceNo' => $invoiceNo,
        ])
    </div>
</x-app-layout>

