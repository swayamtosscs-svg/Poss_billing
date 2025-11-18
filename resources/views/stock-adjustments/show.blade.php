<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ $adjustment->adjustment_number }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('Adjustment recorded on :date', ['date' => $adjustment->adjustment_date?->format('d M Y')]) }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('stock-adjustments.edit', $adjustment) }}"
                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-500">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('stock-adjustments.index') }}"
                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-500">
                    {{ __('Back to list') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Product') }}</span>
                    <div class="mt-1 text-sm text-gray-900 font-semibold">
                        {{ $adjustment->product?->name ?? __('Product #:id', ['id' => $adjustment->product_id]) }}
                    </div>
                    <div class="text-xs text-gray-500">{{ $adjustment->product?->sku ?? '—' }}</div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Current Stock') }}</span>
                    <div class="mt-1 text-sm text-gray-900 font-semibold">{{ $adjustment->product?->stock ?? '—' }} {{ __('units') }}</div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Type') }}</span>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $adjustment->type === 'add' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $adjustment->type === 'add' ? __('Stock In') : __('Stock Out') }}
                        </span>
                    </div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Quantity') }}</span>
                    <div class="mt-1 text-sm text-gray-900 font-semibold">
                        {{ $adjustment->type === 'add' ? '+' : '-' }}{{ $adjustment->quantity }} {{ __('units') }}
                    </div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Reason') }}</span>
                    <div class="mt-1 text-sm text-gray-900">
                        {{ $reasonOptions[$adjustment->reason] ?? ucfirst($adjustment->reason) }}
                    </div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Adjusted By') }}</span>
                    <div class="mt-1 text-sm text-gray-900">{{ $adjustment->adjustedBy?->name ?? __('N/A') }}</div>
                </div>
            </div>
        </div>

        @if($adjustment->notes)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('Notes') }}</h3>
                <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $adjustment->notes }}</p>
            </div>
        @endif
    </div>
</x-app-layout>





