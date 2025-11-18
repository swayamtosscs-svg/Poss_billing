@php
    $editing = isset($purchase) && $purchase->exists;
    $statusOptions = [
        'draft' => __('Draft'),
        'ordered' => __('Ordered'),
        'received' => __('Received'),
        'cancelled' => __('Cancelled'),
    ];

    $defaultItems = [[
        'product_id' => null,
        'quantity' => 1,
        'purchase_price' => 0,
        'discount' => 0,
        'tax_rate' => 0,
    ]];

    $items = old('items', $editing
        ? $purchase->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'purchase_price' => $item->purchase_price,
            'discount' => $item->discount,
            'tax_rate' => $item->tax_rate,
        ])->toArray()
        : $defaultItems
    );
    $currencyCode = currency_code();
    $currencySymbol = currency_symbol($currencyCode);
    $currentPurchase = $editing ? $purchase : null;
@endphp

<form method="POST"
      action="{{ $editing ? route('purchases.update', $purchase) : route('purchases.store') }}"
      class="space-y-8"
      x-data="purchaseForm({
          initialItems: @js($items),
          products: @js($products->map(fn($product) => [
              'id' => $product->id,
              'name' => $product->name,
              'sku' => $product->sku,
          ])),
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
                    <label class="block text-sm font-medium text-gray-700">{{ __('Invoice Number') }}</label>
                    <input name="invoice_no"
                           value="{{ old('invoice_no', $editing ? $purchase->invoice_no : ($invoiceNo ?? '')) }}"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                    <x-input-error :messages="$errors->get('invoice_no')" class="mt-2"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Supplier') }}</label>
                    <select name="party_id"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                        <option value="">{{ __('Select supplier') }}</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}"
                                @selected((int) old('party_id', $editing ? $purchase->party_id : '') === $party->id)>
                                {{ $party->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('party_id')" class="mt-2"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Purchase Date') }}</label>
                    <input type="date"
                           name="purchase_date"
                           required
                           value="{{ old('purchase_date', optional($editing ? $purchase->purchase_date : now())->format('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                    <x-input-error :messages="$errors->get('purchase_date')" class="mt-2"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Due Date') }}</label>
                    <input type="date"
                           name="due_date"
                           value="{{ old('due_date', optional($editing ? $purchase->due_date : null)?->format('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                    <x-input-error :messages="$errors->get('due_date')" class="mt-2"/>
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
                            <th class="px-4 py-3 text-left">{{ __('Cost') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Discount') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Tax %') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Row Total') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100"
                               x-ref="itemsBody">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="text-sm text-gray-700">
                                <td class="px-4 py-3 w-64">
                                    <select class="w-full rounded-md border-gray-300 bg-white text-gray-900"
                                            :name="`items[${index}][product_id]`"
                                            x-model="item.product_id"
                                            required>
                                        <option value="">{{ __('Select product') }}</option>
                                        <template x-for="product in products" :key="`product-${product.id}`">
                                            <option :value="product.id" x-text="product.name"></option>
                                        </template>
                                    </select>
                                    <template x-if="errors[`items.${index}.product_id`]">
                                        <p class="mt-1 text-xs text-red-500" x-text="errors[`items.${index}.product_id`]"></p>
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
                                    <input type="number" min="0" step="0.01"
                                           class="w-28 rounded-md border-gray-300 bg-white text-gray-900"
                                           :name="`items[${index}][purchase_price]`"
                                           x-model.number="item.purchase_price"
                                           @input="refreshTotals"
                                           required>
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
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('Payment & Status') }}</h3>

                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                    <select name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $editing ? $purchase->status : 'draft') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2"/>
                </div>

                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Payment Method') }}</label>
                    <input name="payment_method"
                           value="{{ old('payment_method', $editing ? $purchase->payment_method : '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900"
                           placeholder="{{ __('e.g. Cash, UPI, Card') }}">
                    <x-input-error :messages="$errors->get('payment_method')" class="mt-2"/>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Discount') }}</label>
                        <input type="number" min="0" step="0.01"
                               name="discount"
                               x-model.number="summary.discount"
                               @input="refreshTotals"
                               value="{{ old('discount', $editing ? $purchase->discount : 0) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                        <x-input-error :messages="$errors->get('discount')" class="mt-2"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Tax') }}</label>
                        <input type="number" min="0" step="0.01"
                               name="tax"
                               x-model.number="summary.tax"
                               @input="refreshTotals"
                               value="{{ old('tax', $editing ? $purchase->tax : 0) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                        <x-input-error :messages="$errors->get('tax')" class="mt-2"/>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Paid Amount') }}</label>
                    <input type="number" min="0" step="0.01"
                           name="paid_amount"
                           x-model.number="summary.paid_amount"
                           @input="refreshTotals"
                           value="{{ old('paid_amount', $editing ? $purchase->paid_amount : 0) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                    <x-input-error :messages="$errors->get('paid_amount')" class="mt-2"/>
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">{{ __('Notes') }}</label>
                <textarea name="notes"
                          rows="4"
                          class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">{{ old('notes', $editing ? $purchase->notes : '') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2"/>
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
                <div class="flex justify-between text-sm text-gray-600">
                    <span>{{ __('Paid') }}</span>
                    <span x-text="formatCurrency(summary.paid_amount ?? 0)"></span>
                </div>
                <div class="flex justify-between text-sm font-semibold text-gray-900">
                    <span>{{ __('Balance Due') }}</span>
                    <span x-text="formatCurrency(summary.balance)"></span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('purchases.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                    {{ $editing ? __('Update Purchase') : __('Create Purchase') }}
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('purchaseForm', (config) => ({
                products: config.products ?? [],
                items: [],
                errors: {},
                summary: {
                    subtotal: Number(@json(old('subtotal', $editing ? $purchase->subtotal : 0))),
                    discount: Number(@json(old('discount', $editing ? $purchase->discount : 0))),
                    tax: Number(@json(old('tax', $editing ? $purchase->tax : 0))),
                    paid_amount: Number(@json(old('paid_amount', $editing ? $purchase->paid_amount : 0))),
                    total: 0,
                    balance: 0,
                },

                init() {
                    this.items = (config.initialItems && config.initialItems.length)
                        ? config.initialItems.map(item => ({
                            product_id: item.product_id,
                            quantity: Number(item.quantity ?? 1),
                            purchase_price: Number(item.purchase_price ?? 0),
                            discount: Number(item.discount ?? 0),
                            tax_rate: Number(item.tax_rate ?? 0),
                            total: 0,
                        }))
                        : [{
                            product_id: null,
                            quantity: 1,
                            purchase_price: 0,
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
                        purchase_price: 0,
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

                refreshTotals() {
                    let subtotal = 0;

                    this.items = this.items.map(item => {
                        const qty = Number(item.quantity) || 0;
                        const price = Number(item.purchase_price) || 0;
                        const discount = Number(item.discount) || 0;
                        const taxRate = Number(item.tax_rate) || 0;

                        const rowSubtotal = Math.max(price * qty - discount, 0);
                        const taxAmount = rowSubtotal * taxRate / 100;
                        const total = rowSubtotal + taxAmount;

                        subtotal += rowSubtotal;

                        return {
                            ...item,
                            quantity: qty,
                            purchase_price: price,
                            discount,
                            tax_rate: taxRate,
                            total,
                        };
                    });

                    this.summary.subtotal = Number(subtotal.toFixed(2));
                    this.summary.discount = Number((Number(this.summary.discount) || 0).toFixed(2));
                    this.summary.tax = Number((Number(this.summary.tax) || 0).toFixed(2));
                    this.summary.paid_amount = Number((Number(this.summary.paid_amount) || 0).toFixed(2));

                    const total = this.summary.subtotal - this.summary.discount + this.summary.tax;
                    this.summary.total = Number(total.toFixed(2));
                    const balance = this.summary.total - this.summary.paid_amount;
                    this.summary.balance = Number(balance.toFixed(2));
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

