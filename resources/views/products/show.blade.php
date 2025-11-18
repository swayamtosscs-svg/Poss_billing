<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 space-y-4">
            <div class="flex items-center gap-4">
                @if($product->image_path)
                    <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-24 w-24 rounded object-cover">
                @endif
                <div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
                    <p class="text-gray-600 dark:text-gray-300">{{ $product->category?->name }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-200">
                <div><span class="font-semibold">{{ __('Price') }}:</span> {{ currency_format($product->price) }}</div>
                <div><span class="font-semibold">{{ __('Stock') }}:</span> {{ $product->stock }}</div>
                <div><span class="font-semibold">{{ __('Barcode') }}:</span> {{ $product->barcode ?? '—' }}</div>
                <div><span class="font-semibold">{{ __('SKU') }}:</span> {{ $product->sku ?? '—' }}</div>
                <div><span class="font-semibold">{{ __('Created') }}:</span> {{ $product->created_at?->format('d M Y H:i') }}</div>
            </div>
            <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Description') }}</h4>
                <p class="text-gray-600 dark:text-gray-300 mt-2">{{ $product->description ?: __('No description provided.') }}</p>
            </div>
            <div class="flex justify-end">
                <a href="{{ route('products.edit', $product) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                    {{ __('Edit Product') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

