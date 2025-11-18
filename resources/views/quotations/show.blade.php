<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ $quotation->quotation_number }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('Quotation for :customer', ['customer' => $quotation->customer?->name ?? __('Unknown customer')]) }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                    @class([
                        'bg-gray-100 text-gray-700' => $quotation->status === 'draft',
                        'bg-blue-100 text-blue-700' => $quotation->status === 'sent',
                        'bg-emerald-100 text-emerald-700' => $quotation->status === 'accepted',
                        'bg-red-100 text-red-700' => $quotation->status === 'rejected',
                        'bg-amber-100 text-amber-700' => $quotation->status === 'expired',
                    ])">
                    {{ ucfirst($quotation->status) }}
                </span>
                <a href="{{ route('quotations.edit', $quotation) }}"
                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-500">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('quotations.index') }}"
                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-500">
                    {{ __('Back to list') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Quotation Info') }}</h3>
                <dl class="mt-4 space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between">
                        <dt>{{ __('Quotation No.') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $quotation->quotation_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Quotation Date') }}</dt>
                        <dd>{{ $quotation->quotation_date?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Valid Until') }}</dt>
                        <dd>{{ $quotation->valid_until?->format('d M Y') ?? __('No expiry') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Customer Details') }}</h3>
                <dl class="mt-4 space-y-2 text-sm text-gray-700">
                    <div>
                        <dt class="text-xs uppercase text-gray-400">{{ __('Name') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $quotation->customer?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-gray-400">{{ __('Email') }}</dt>
                        <dd>{{ $quotation->customer?->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-gray-400">{{ __('Phone') }}</dt>
                        <dd>{{ $quotation->customer?->phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-gray-400">{{ __('Address') }}</dt>
                        <dd>{{ $quotation->customer?->address ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Totals') }}</h3>
                <dl class="mt-4 space-y-3 text-sm text-gray-700">
                    <div class="flex justify-between">
                        <dt>{{ __('Subtotal') }}</dt>
                        <dd>{{ currency_format($quotation->subtotal ?? 0) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Discount') }}</dt>
                        <dd>{{ currency_format($quotation->discount ?? 0) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Tax') }}</dt>
                        <dd>{{ currency_format($quotation->tax ?? 0) }}</dd>
                    </div>
                    <div class="border-t border-dashed border-gray-300 pt-3 mt-3 flex justify-between text-base font-semibold text-gray-900">
                        <dt>{{ __('Total Amount') }}</dt>
                        <dd>{{ currency_format($quotation->total_amount ?? 0) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Items') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3 text-left">{{ __('Product') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Quantity') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Unit Price') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Discount') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Tax %') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Tax Amount') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Line Total') }}</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($quotation->items as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $item->product?->name ?? __('Product #:id', ['id' => $item->product_id]) }}</div>
                                <div class="text-xs text-gray-500">{{ __('SKU: :sku', ['sku' => $item->product?->sku ?? '—']) }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $item->quantity }}</td>
                            <td class="px-4 py-3">{{ currency_format($item->unit_price) }}</td>
                            <td class="px-4 py-3">{{ currency_format($item->discount ?? 0) }}</td>
                            <td class="px-4 py-3">{{ number_format($item->tax_rate ?? 0, 2) }}%</td>
                            <td class="px-4 py-3 text-right">{{ currency_format($item->tax_amount ?? 0) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ currency_format($item->total ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                                {{ __('No items found for this quotation.') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($quotation->terms_conditions)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('Terms & Conditions') }}</h3>
                <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $quotation->terms_conditions }}</p>
            </div>
        @endif

        @if($quotation->notes)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('Notes') }}</h3>
                <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $quotation->notes }}</p>
            </div>
        @endif
    </div>
</x-app-layout>





