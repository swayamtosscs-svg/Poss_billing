@php
    $editing = isset($quotation) && $quotation->exists;
    $statusOptions = [
        'draft' => __('Draft'),
        'sent' => __('Sent'),
        'accepted' => __('Accepted'),
        'rejected' => __('Rejected'),
        'expired' => __('Expired'),
    ];

    $defaultItems = [[
        'product_id' => null,
        'quantity' => 1,
        'unit_price' => 0,
        'discount' => 0,
        'tax_rate' => 0,
    ]];

    $items = old('items', $editing
        ? $quotation->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'discount' => $item->discount,
            'tax_rate' => $item->tax_rate,
        ])->toArray()
        : $defaultItems
    );

    $currencyCode = currency_code();
    $currencySymbol = currency_symbol($currencyCode);
@endphp

<form method="POST"
      action="{{ $editing ? route('quotations.update', $quotation) : route('quotations.store') }}"
      class="space-y-8"
      x-data="quotationForm({
          initialItems: @js($items),
          products: @js($products->map(fn($product) => [
              'id' => $product->id,
              'name' => $product->name,
              'sku' => $product->sku,
              'price' => $product->price,
          ])),
          defaultUnitPrice: {{ json_encode($products->pluck('price', 'id')) }},
      })"
      x-init="init()">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Quotation Number') }}</label>
                    <input name="quotation_number"
                           value="{{ old('quotation_number', $editing ? $quotation->quotation_number : ($quotationNumber ?? '')) }}"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                    <x-input-error :messages="$errors->get('quotation_number')" class="mt-2"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Customer') }}</label>
                    <select name="customer_id"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                        <option value="">{{ __('Select customer') }}</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                @selected((int) old('customer_id', $editing ? $quotation->customer_id : '') === $customer->id)>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('customer_id')" class="mt-2"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Quotation Date') }}</label>
                    <input type="date"
                           name="quotation_date"
                           required
                           value="{{ old('quotation_date', optional($editing ? $quotation->quotation_date : now())->format('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                    <x-input-error :messages="$errors->get('quotation_date')" class="mt-2"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Valid Until') }}</label>
                    <input type="date"
                           name="valid_until"
                           value="{{ old('valid_until', optional($editing ? $quotation->valid_until : null)?->format('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                    <x-input-error :messages="$errors->get('valid_until')" class="mt-2"/>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Line Items') }}</h3>
                    <button type="button"
                            class="inline-flex items-center px-3 py-1.5 rounded-md bg-indigo-50 text-indigo-600 text-sm font-medium hover:bg-indigo-100"
                            @click="addItem">
                        {{ __('Add Item') }}
                    </button>
                </div>

                @if($errors->has('items') || $errors->has('items.*'))
                    <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-600">
                        {{ __('Please review the highlighted item rows.') }}
                    </div>
                @endif

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                        <thead class="bg-gray-50">
                        <tr class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            <th class="px-4 py-3 text-left">{{ __('Product') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Quantity') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Unit Price') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Discount') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Tax %') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Line Total') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" x-ref="itemsBody">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="text-sm text-gray-700">
                                <td class="px-4 py-3 w-64">
                                    <select class="w-full rounded-md border-gray-300 bg-white text-gray-900"
                                            :name="`items[${index}][product_id]`"
                                            x-model="item.product_id"
                                            @change="prefillPrice(index)"
                                            required>
                                        <option value="">{{ __('Select product') }}</option>
                                        <template x-for="product in products" :key="`product-${product.id}`">
                                            <option :value="product.id" x-text="product.name"></option>
                                        </template>
                                    </select>
                                    <template x-if="productSku(item.product_id)">
                                        <p class="mt-1 text-xs text-gray-500" x-text="productSku(item.product_id)"></p>
                                    </template>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" min="1"
                                           class="w-24 rounded-md border-gray-300 bg-white text-gray-900"
                                           :name="`items[${index}][quantity]`"
                                           x-model.number="item.quantity"
                                           @input="refreshTotals"
                                           required>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">{{ $currencySymbol }}</span>
                                        <input type="number" min="0" step="0.01"
                                               class="w-32 rounded-md border-gray-300 bg-white text-gray-900 pl-7"
                                               :name="`items[${index}][unit_price]`"
                                               x-model.number="item.unit_price"
                                               @input="refreshTotals"
                                               required>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" min="0" step="0.01"
                                           class="w-24 rounded-md border-gray-300 bg-white text-gray-900"
                                           :name="`items[${index}][discount]`"
                                           x-model.number="item.discount"
                                           @input="refreshTotals">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" min="0" step="0.01"
                                           class="w-20 rounded-md border-gray-300 bg-white text-gray-900"
                                           :name="`items[${index}][tax_rate]`"
                                           x-model.number="item.tax_rate"
                                           @input="refreshTotals">
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    <span x-text="formatCurrency(item.total)"></span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button"
                                            class="text-sm text-red-500 hover:text-red-600"
                                            @click="removeItem(index)"
                                            x-show="items.length > 1">
                                        {{ __('Remove') }}
                                    </button>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-6">
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('Quotation Settings') }}</h3>

                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                    <select name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $editing ? $quotation->status : 'draft') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2"/>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Discount') }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">{{ $currencySymbol }}</span>
                            <input type="number" min="0" step="0.01"
                                   name="discount"
                                   x-model.number="summary.discount"
                                   @input="refreshTotals"
                                   value="{{ old('discount', $editing ? $quotation->discount : 0) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 pl-7">
                        </div>
                        <x-input-error :messages="$errors->get('discount')" class="mt-2"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Tax') }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">{{ $currencySymbol }}</span>
                            <input type="number" min="0" step="0.01"
                                   name="tax"
                                   x-model.number="summary.tax"
                                   @input="refreshTotals"
                                   value="{{ old('tax', $editing ? $quotation->tax : null) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 pl-7">
                        </div>
                        <x-input-error :messages="$errors->get('tax')" class="mt-2"/>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Terms & Conditions') }}</label>
                    <textarea name="terms_conditions"
                              rows="4"
                              class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">{{ old('terms_conditions', $editing ? $quotation->terms_conditions : '') }}</textarea>
                    <x-input-error :messages="$errors->get('terms_conditions')" class="mt-2"/>
                </div>

                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Notes') }}</label>
                    <textarea name="notes"
                              rows="4"
                              class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">{{ old('notes', $editing ? $quotation->notes : '') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2"/>
                </div>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 space-y-3" x-init="refreshTotals">
                <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">{{ __('Summary') }}</h4>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>{{ __('Subtotal') }}</span>
                    <span x-text="formatCurrency(summary.subtotal)"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>{{ __('Discount') }}</span>
                    <span x-text="formatCurrency(summary.discount ?? 0)"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>{{ __('Tax') }}</span>
                    <span x-text="formatCurrency(summary.tax ?? 0)"></span>
                </div>
                <div class="border-t border-dashed border-gray-300 pt-3 mt-3 flex justify-between text-base font-semibold text-gray-900">
                    <span>{{ __('Total Amount') }}</span>
                    <span x-text="formatCurrency(summary.total)"></span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('quotations.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                    {{ $editing ? __('Update Quotation') : __('Create Quotation') }}
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('quotationForm', (config) => ({
            products: config.products ?? [],
            defaultUnitPrice: config.defaultUnitPrice ?? {},
            items: [],
            summary: {
                subtotal: Number(@json(old('subtotal', $editing ? $quotation->subtotal : 0))),
                discount: Number(@json(old('discount', $editing ? $quotation->discount : 0))),
                tax: Number(@json(old('tax', $editing ? $quotation->tax : null))),
                total: 0,
            },

            init() {
                this.items = (config.initialItems && config.initialItems.length)
                    ? config.initialItems.map(item => ({
                        product_id: item.product_id,
                        quantity: Number(item.quantity ?? 1),
                        unit_price: Number(item.unit_price ?? 0),
                        discount: Number(item.discount ?? 0),
                        tax_rate: Number(item.tax_rate ?? 0),
                        total: 0,
                    }))
                    : [{
                        product_id: null,
                        quantity: 1,
                        unit_price: 0,
                        discount: 0,
                        tax_rate: 0,
                        total: 0,
                    }];

                this.refreshTotals();
            },

            addItem() {
                this.items.push({
                    product_id: null,
                    quantity: 1,
                    unit_price: 0,
                    discount: 0,
                    tax_rate: 0,
                    total: 0,
                });
                this.$nextTick(this.refreshTotals);
            },

            removeItem(index) {
                if (this.items.length === 1) {
                    return;
                }
                this.items.splice(index, 1);
                this.$nextTick(this.refreshTotals);
            },

            prefillPrice(index) {
                const productId = this.items[index]?.product_id;
                if (productId && this.defaultUnitPrice[productId] !== undefined) {
                    this.items[index].unit_price = Number(this.defaultUnitPrice[productId]);
                    this.refreshTotals();
                }
            },

            productSku(productId) {
                const product = this.products.find((p) => Number(p.id) === Number(productId));
                if (!product || !product.sku) {
                    return null;
                }

                return `SKU: ${product.sku}`;
            },

            refreshTotals() {
                let subtotal = 0;
                let taxTotal = 0;

                this.items = this.items.map(item => {
                    const quantity = Number(item.quantity) || 0;
                    const unitPrice = Number(item.unit_price) || 0;
                    const discount = Number(item.discount) || 0;
                    const taxRate = Number(item.tax_rate) || 0;

                    const lineSubtotal = Math.max((unitPrice * quantity) - discount, 0);
                    const lineTax = lineSubtotal * taxRate / 100;
                    const lineTotal = lineSubtotal + lineTax;

                    subtotal += lineSubtotal;
                    taxTotal += lineTax;

                    return {
                        ...item,
                        quantity,
                        unit_price: unitPrice,
                        discount,
                        tax_rate: taxRate,
                        total: lineTotal,
                    };
                });

                this.summary.subtotal = Number(subtotal.toFixed(2));

                if (Number.isNaN(this.summary.discount) || this.summary.discount === null) {
                    this.summary.discount = 0;
                }
                if (Number.isNaN(this.summary.tax) || this.summary.tax === null) {
                    this.summary.tax = Number(taxTotal.toFixed(2));
                }

                const total = this.summary.subtotal - (Number(this.summary.discount) || 0) + (Number(this.summary.tax) || 0);
                this.summary.total = Number(Math.max(total, 0).toFixed(2));
            },

            formatCurrency(amount) {
                const value = Number(amount) || 0;
                return new Intl.NumberFormat(undefined, {
                    style: 'currency',
                    currency: '{{ $currencyCode }}',
                    currencyDisplay: 'symbol',
                    maximumFractionDigits: 2,
                }).format(value);
            },
        }));
    });
</script>





