<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ $purchase->invoice_no }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('Purchase from :supplier', ['supplier' => $purchase->party?->name ?? __('Unknown supplier')]) }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                    @class([
                        'bg-gray-100 text-gray-700' => $purchase->status === 'draft',
                        'bg-amber-100 text-amber-800' => $purchase->status === 'ordered',
                        'bg-emerald-100 text-emerald-700' => $purchase->status === 'received',
                        'bg-red-100 text-red-700' => $purchase->status === 'cancelled',
                    ])">
                    {{ ucfirst($purchase->status) }}
                </span>
                <a href="{{ route('purchases.edit', $purchase) }}"
                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-500">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('purchases.index') }}"
                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-500">
                    {{ __('Back to list') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Invoice Info') }}</h3>
                <dl class="mt-4 space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between">
                        <dt>{{ __('Invoice No.') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $purchase->invoice_no }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Purchase Date') }}</dt>
                        <dd>{{ $purchase->purchase_date?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Due Date') }}</dt>
                        <dd>{{ $purchase->due_date?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Payment Method') }}</dt>
                        <dd>{{ $purchase->payment_method ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Supplier Details') }}</h3>
                <dl class="mt-4 space-y-2 text-sm text-gray-700">
                    <div>
                        <dt class="text-xs uppercase text-gray-400">{{ __('Name') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $purchase->party?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-gray-400">{{ __('Phone') }}</dt>
                        <dd>{{ $purchase->party?->phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-gray-400">{{ __('Email') }}</dt>
                        <dd>{{ $purchase->party?->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-gray-400">{{ __('GSTIN') }}</dt>
                        <dd>{{ $purchase->party?->gstin ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Totals') }}</h3>
                <dl class="mt-4 space-y-3 text-sm text-gray-700">
                    <div class="flex justify-between">
                        <dt>{{ __('Subtotal') }}</dt>
                        <dd>{{ currency_format($purchase->subtotal ?? 0) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Discount') }}</dt>
                        <dd>{{ currency_format($purchase->discount ?? 0) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Tax') }}</dt>
                        <dd>{{ currency_format($purchase->tax ?? 0) }}</dd>
                    </div>
                    <div class="flex justify-between font-semibold text-gray-900">
                        <dt>{{ __('Total Amount') }}</dt>
                        <dd>{{ currency_format($purchase->total_amount ?? 0) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Paid Amount') }}</dt>
                        <dd>{{ currency_format($purchase->paid_amount ?? 0) }}</dd>
                    </div>
                    <div class="flex justify-between font-semibold text-gray-900">
                        <dt>{{ __('Balance Due') }}</dt>
                        <dd>{{ currency_format($purchase->getBalanceAmount()) }}</dd>
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
                        <th class="px-4 py-3 text-left">{{ __('Cost') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Discount') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Tax %') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Tax Amount') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Total') }}</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($purchase->items as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $item->product?->name ?? __('Product #:id', ['id' => $item->product_id]) }}</div>
                                <div class="text-xs text-gray-500">{{ __('SKU: :sku', ['sku' => $item->product?->sku ?? '—']) }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $item->quantity }}</td>
                            <td class="px-4 py-3">{{ currency_format($item->purchase_price) }}</td>
                            <td class="px-4 py-3">{{ currency_format($item->discount ?? 0) }}</td>
                            <td class="px-4 py-3">{{ number_format($item->tax_rate ?? 0, 2) }}%</td>
                            <td class="px-4 py-3 text-right">{{ currency_format($item->tax_amount ?? 0) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ currency_format($item->total ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                                {{ __('No items recorded for this purchase.') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($purchase->notes)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('Notes') }}</h3>
                <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $purchase->notes }}</p>
            </div>
        @endif
    </div>
</x-app-layout>





