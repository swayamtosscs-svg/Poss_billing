<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Create Invoice') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Generate professional invoices with live preview') }}
                </p>
            </div>
            <a href="{{ route('sales.index') }}" class="text-sm font-medium text-orange-500 dark:text-orange-400 hover:text-orange-400">
                {{ __('View All Invoices') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="invoiceApp({
        productsUrl: '{{ route('products.search') }}',
        customersUrl: '{{ route('customers.search') }}',
        initialProducts: @json($products ?? []),
        currencySymbol: @json(currency_symbol())
    })" x-init="init()">
        <form method="POST" action="{{ route('sales.store') }}" @submit.prevent="submitInvoice">
            @csrf
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Side: Invoice Form -->
                    <div class="space-y-6">
                        <!-- Company & Customer Details -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Invoice Details') }}</h3>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Invoice Number') }}</label>
                                        <input type="text" x-model="invoiceNumber" 
                                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Invoice Date') }}</label>
                                        <input type="date" x-model="invoiceDate" 
                                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Customer Information') }}</h4>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Search Customer') }}</label>
                                    <div class="relative">
                                        <input type="text" x-model="customerSearch"
                                               @input.debounce.400ms="searchCustomers"
                                               placeholder="{{ __('Search by name, email or phone') }}"
                                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white pl-10">
                                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 dark:text-gray-500">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </span>
                                    </div>
                                    
                                    <div x-show="customerResults.length > 0" 
                                         class="mt-2 max-h-48 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        <template x-for="customer in customerResults" :key="customer.id">
                                            <button type="button" @click="selectCustomer(customer)"
                                                    class="w-full text-left px-4 py-3 hover:bg-orange-50 dark:hover:bg-orange-900/20">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="customer.name"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="customer.phone"></p>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div x-show="selectedCustomer" class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Customer Name') }}</label>
                                        <input type="text" x-model="selectedCustomer.name" 
                                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Email') }}</label>
                                            <input type="email" x-model="selectedCustomer.email" 
                                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Phone') }}</label>
                                            <input type="text" x-model="selectedCustomer.phone" 
                                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Address') }}</label>
                                        <textarea x-model="selectedCustomer.address" rows="2"
                                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Selection -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Add Products') }}</h3>
                            
                            <div class="relative">
                                <input type="text" x-model="productSearch"
                                       @input.debounce.400ms="searchProducts"
                                       placeholder="{{ __('Search products...') }}"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white pl-10">
                                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 dark:text-gray-500">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </span>
                            </div>

                            <div class="max-h-64 overflow-y-auto space-y-2">
                                <template x-for="product in filteredProducts" :key="product.id">
                                    <button type="button" @click="addProduct(product)"
                                            class="w-full flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-orange-400 dark:hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition bg-white dark:bg-gray-800">
                                        <div class="h-12 w-12 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                            <span class="text-sm font-semibold text-orange-600 dark:text-orange-400" x-text="product.name.charAt(0)"></span>
                                        </div>
                                        <div class="flex-1 text-left">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="product.name"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                <span x-text="currencySymbol + Number(product.price).toFixed(2)"></span> · 
                                                <span x-text="'Stock: ' + product.stock"></span>
                                            </p>
                                        </div>
                                        <svg class="h-5 w-5 text-orange-500 dark:text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Invoice Items -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Invoice Items') }}</h3>
                            
                            <div class="space-y-3">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 space-y-3 bg-white dark:bg-gray-800">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="item.name"></p>
                                                <div class="grid grid-cols-3 gap-2 mt-3">
                                                    <div>
                                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Qty') }}</label>
                                                        <input type="number" x-model.number="item.quantity" min="1"
                                                               @input="calculateTotals"
                                                               class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Price') }}</label>
                                                        <input type="number" x-model.number="item.price" min="0" step="0.01"
                                                               @input="calculateTotals"
                                                               class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Discount') }}</label>
                                                        <input type="number" x-model.number="item.discount" min="0" step="0.01"
                                                               @input="calculateTotals"
                                                               class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ __('Item Total') }}: 
                                                        <span class="font-semibold text-gray-900 dark:text-white" 
                                                              x-text="currencySymbol + ((item.quantity * item.price) - (item.discount || 0)).toFixed(2)"></span>
                                                    </p>
                                                </div>
                                            </div>
                                            <button type="button" @click="removeItem(index)"
                                                    class="ml-3 text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="items.length === 0" 
                                     class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="mt-2 text-sm">{{ __('No items added yet. Search and add products above.') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Totals & Payment -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Payment Details') }}</h3>
                            
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Extra Discount') }}</label>
                                        <input type="number" x-model.number="extraDiscount" min="0" step="0.01"
                                               @input="calculateTotals()"
                                               @change="calculateTotals()"
                                               class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Tax/GST') }}</label>
                                        <input type="number" x-model.number="tax" min="0" step="0.01"
                                               @input="calculateTotals()"
                                               @change="calculateTotals()"
                                               class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Payment Method') }}</label>
                                    <select x-model="paymentMethod" class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="cash">{{ __('Cash') }}</option>
                                        <option value="card">{{ __('Card') }}</option>
                                        <option value="upi">{{ __('UPI') }}</option>
                                        <option value="wallet">{{ __('Wallet') }}</option>
                                        <option value="netbanking">{{ __('Net Banking') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="pt-4 border-t-2 border-gray-300 dark:border-gray-700 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Subtotal') }}</span>
                                    <span class="font-medium dark:text-gray-300" x-text="currencySymbol + subtotal.toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Discount') }}</span>
                                    <span class="font-medium text-red-600 dark:text-red-400" x-text="'- ' + currencySymbol + totalDiscount.toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Tax') }}</span>
                                    <span class="font-medium dark:text-gray-300" x-text="currencySymbol + tax.toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between text-xl font-bold pt-3 border-t-2 border-orange-500 dark:border-orange-600 bg-orange-50 dark:bg-orange-900/20 -mx-6 px-6 py-3 rounded-lg select-none" style="pointer-events: none; user-select: none;">
                                    <span class="text-gray-900 dark:text-white">{{ __('Grand Total') }}</span>
                                    <span class="text-orange-600 dark:text-orange-400" x-text="currencySymbol + (grandTotal || 0).toFixed(2)"></span>
                                </div>
                            </div>

                            <button type="submit" :disabled="items.length === 0 || isSubmitting"
                                    class="w-full py-3 bg-orange-500 hover:bg-orange-400 text-white font-semibold rounded-lg shadow disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isSubmitting">{{ __('Generate Invoice') }}</span>
                                <span x-show="isSubmitting">{{ __('Processing...') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Right Side: Live Invoice Preview -->
                    <div class="lg:sticky lg:top-6 lg:h-fit">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                                <h3 class="text-lg font-semibold text-white">{{ __('Live Preview') }}</h3>
                                <p class="text-sm text-orange-100">{{ __('See how your invoice looks') }}</p>
                            </div>
                            
                            <div class="p-8 space-y-6 bg-white dark:bg-gray-800" id="invoice-preview">
                                <!-- Invoice Header -->
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ config('app.name', 'BillingPOS') }}</h1>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Professional Billing Solution') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Invoice') }}</p>
                                        <p class="text-lg font-bold text-orange-500 dark:text-orange-400" x-text="invoiceNumber"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatDate(invoiceDate)"></p>
                                    </div>
                                </div>

                                <!-- Customer Details -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold mb-2">{{ __('Bill To') }}</p>
                                        <template x-if="selectedCustomer">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedCustomer.name"></p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400" x-text="selectedCustomer.email"></p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400" x-text="selectedCustomer.phone"></p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400" x-text="selectedCustomer.address"></p>
                                            </div>
                                        </template>
                                        <template x-if="!selectedCustomer">
                                            <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('No customer selected') }}</p>
                                        </template>
                                    </div>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold mb-2">{{ __('Payment') }}</p>
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            <span class="font-medium">{{ __('Method') }}:</span>
                                            <span x-text="paymentMethod.toUpperCase()" class="text-orange-500 dark:text-orange-400 font-semibold ml-1"></span>
                                        </p>
                                        <p class="text-sm text-gray-900 dark:text-white mt-1">
                                            <span class="font-medium">{{ __('Date') }}:</span>
                                            <span x-text="formatDate(invoiceDate)" class="ml-1"></span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Invoice Items Table -->
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                    <table class="w-full bg-white dark:bg-gray-800">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">#</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">{{ __('Item') }}</th>
                                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">{{ __('Qty') }}</th>
                                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">{{ __('Price') }}</th>
                                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">{{ __('Total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            <template x-for="(item, index) in items" :key="index">
                                                <tr>
                                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" x-text="index + 1"></td>
                                                    <td class="px-4 py-3">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="item.name"></p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-show="item.discount > 0">
                                                            {{ __('Disc') }}: <span x-text="currencySymbol + item.discount.toFixed(2)"></span>
                                                        </p>
                                                    </td>
                                                    <td class="px-4 py-3 text-right text-sm text-gray-900 dark:text-white" x-text="item.quantity"></td>
                                                    <td class="px-4 py-3 text-right text-sm text-gray-900 dark:text-white" x-text="currencySymbol + item.price.toFixed(2)"></td>
                                                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-white" 
                                                        x-text="currencySymbol + ((item.quantity * item.price) - (item.discount || 0)).toFixed(2)"></td>
                                                </tr>
                                            </template>
                                            <tr x-show="items.length === 0">
                                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                                    {{ __('No items added yet') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Totals -->
                                <div class="flex justify-end">
                                    <div class="w-64 space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">{{ __('Subtotal') }}</span>
                                            <span class="font-medium text-gray-900 dark:text-white" x-text="currencySymbol + subtotal.toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between text-sm" x-show="totalDiscount > 0">
                                            <span class="text-gray-600 dark:text-gray-400">{{ __('Discount') }}</span>
                                            <span class="font-medium text-red-600 dark:text-red-400" x-text="'- ' + currencySymbol + totalDiscount.toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between text-sm" x-show="tax > 0">
                                            <span class="text-gray-600 dark:text-gray-400">{{ __('Tax') }}</span>
                                            <span class="font-medium text-gray-900 dark:text-white" x-text="currencySymbol + tax.toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between text-xl font-bold pt-3 border-t-2 border-orange-500 dark:border-orange-600 bg-orange-50 dark:bg-orange-900/20 -mx-4 px-4 py-3 rounded-lg select-none" style="pointer-events: none; user-select: none;">
                                            <span class="text-gray-900 dark:text-white">{{ __('Grand Total') }}</span>
                                            <span class="text-orange-600 dark:text-orange-400" x-text="currencySymbol + (grandTotal || 0).toFixed(2)"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="pt-6 border-t border-dashed border-gray-300 dark:border-gray-700 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Thank you for your business!') }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('This is a computer-generated invoice') }}</p>
                                </div>
                            </div>

                            <!-- Print Button -->
                            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 relative z-10">
                                <button type="button" 
                                        @click="printPreview()"
                                        class="w-full py-3 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer relative z-10"
                                        style="pointer-events: auto;">
                                    <span class="flex items-center justify-center gap-2">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" 
                                                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        {{ __('Print Preview') }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden Form Fields -->
            <template x-for="(item, index) in items" :key="`hidden-${index}`">
                <div>
                    <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                    <input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity">
                    <input type="hidden" :name="`items[${index}][price]`" :value="item.price">
                    <input type="hidden" :name="`items[${index}][discount]`" :value="item.discount || 0">
                </div>
            </template>
            <input type="hidden" name="customer_id" :value="selectedCustomer?.id ?? ''">
            <input type="hidden" name="payment_type" :value="paymentMethod">
            <input type="hidden" name="discount" :value="extraDiscount">
            <input type="hidden" name="tax" :value="tax">
            <input type="hidden" name="date" :value="invoiceDate">
        </form>
    </div>

    <script>
        function invoiceApp({ productsUrl, customersUrl, initialProducts, currencySymbol }) {
            return {
                productsUrl,
                customersUrl,
                currencySymbol,
                allProducts: initialProducts,
                filteredProducts: initialProducts,
                productSearch: '',
                customerSearch: '',
                customerResults: [],
                selectedCustomer: null,
                items: [],
                invoiceNumber: '',
                invoiceDate: '',
                extraDiscount: 0,
                tax: 0,
                paymentMethod: 'cash',
                subtotal: 0,
                totalDiscount: 0,
                grandTotal: 0,
                isSubmitting: false,

                init() {
                    this.generateInvoiceNumber();
                    this.invoiceDate = new Date().toISOString().split('T')[0];
                    this.calculateTotals();
                    
                    // Watch for changes and recalculate
                    this.$watch('items', () => this.calculateTotals(), { deep: true });
                    this.$watch('extraDiscount', () => this.calculateTotals());
                    this.$watch('tax', () => this.calculateTotals());
                },

                generateInvoiceNumber() {
                    const timestamp = Date.now();
                    const random = Math.floor(Math.random() * 1000);
                    this.invoiceNumber = `INV-${timestamp.toString().slice(-6)}${random}`;
                },

                async searchProducts() {
                    if (!this.productSearch.trim()) {
                        this.filteredProducts = [...this.allProducts];
                        return;
                    }

                    try {
                        const response = await fetch(`${this.productsUrl}?q=${encodeURIComponent(this.productSearch)}`);
                        const { data } = await response.json();
                        this.filteredProducts = Array.isArray(data) ? data : [];
                    } catch (error) {
                        console.error(error);
                        window.notyf?.error('Unable to search products');
                    }
                },

                async searchCustomers() {
                    if (!this.customerSearch.trim()) {
                        this.customerResults = [];
                        return;
                    }

                    try {
                        const response = await fetch(`${this.customersUrl}?q=${encodeURIComponent(this.customerSearch)}`);
                        const { data } = await response.json();
                        this.customerResults = Array.isArray(data) ? data : [];
                    } catch (error) {
                        console.error(error);
                        window.notyf?.error('Unable to search customers');
                    }
                },

                selectCustomer(customer) {
                    this.selectedCustomer = { ...customer };
                    this.customerResults = [];
                    this.customerSearch = customer.name;
                },

                addProduct(product) {
                    const existing = this.items.find(item => item.id === product.id);
                    if (existing) {
                        existing.quantity += 1;
                    } else {
                        this.items.push({
                            id: product.id,
                            name: product.name,
                            price: Number(product.price),
                            quantity: 1,
                            discount: 0,
                        });
                    }
                    this.calculateTotals();
                    window.notyf?.success('Product added to invoice');
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotals();
                },

                calculateTotals() {
                    this.subtotal = this.items.reduce((sum, item) => {
                        const qty = Number(item.quantity) || 0;
                        const price = Number(item.price) || 0;
                        return sum + (qty * price);
                    }, 0);
                    
                    const itemDiscounts = this.items.reduce((sum, item) => {
                        return sum + (Number(item.discount) || 0);
                    }, 0);
                    
                    this.totalDiscount = itemDiscounts + (Number(this.extraDiscount) || 0);
                    const taxAmount = Number(this.tax) || 0;
                    
                    this.grandTotal = Math.max(0, this.subtotal - this.totalDiscount + taxAmount);
                    
                    // Force reactivity
                    this.$nextTick(() => {
                        this.grandTotal = this.grandTotal;
                    });
                },

                printPreview() {
                    const preview = document.getElementById('invoice-preview');
                    if (!preview) {
                        alert('Preview not available');
                        return;
                    }

                    // Clone the preview element to avoid modifying the original
                    const printContent = preview.cloneNode(true);
                    
                    // Create a new window for printing
                    const printWindow = window.open('', '_blank', 'width=900,height=1200');
                    if (!printWindow) {
                        alert('Please allow pop-ups to print the invoice.');
                        return;
                    }

                    printWindow.document.write(`
                        <!DOCTYPE html>
                        <html>
                            <head>
                                <meta charset="utf-8">
                                <title>Invoice Print - ${this.invoiceNumber || 'Invoice'}</title>
                                <style>
                                    * { box-sizing: border-box; margin: 0; padding: 0; }
                                    body { 
                                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                                        background: white; 
                                        padding: 40px; 
                                        color: #111827;
                                    }
                                    h1, h2, h3, h4, h5 { margin: 0; }
                                    table { 
                                        border-collapse: collapse; 
                                        width: 100%; 
                                        margin: 20px 0;
                                    }
                                    th, td { 
                                        padding: 10px 12px; 
                                        text-align: left;
                                    }
                                    th { 
                                        background: #f3f4f6; 
                                        font-size: 12px; 
                                        text-transform: uppercase; 
                                        letter-spacing: 0.05em;
                                        font-weight: 600;
                                    }
                                    td { 
                                        border-top: 1px solid #e5e7eb; 
                                        font-size: 14px; 
                                    }
                                    .bg-orange-50 { background-color: #fff7ed !important; }
                                    .text-orange-600 { color: #ea580c !important; }
                                    .border-orange-500 { border-color: #f97316 !important; }
                                    @media print {
                                        body { padding: 20px; }
                                        @page { margin: 1cm; }
                                    }
                                </style>
                            </head>
                            <body>
                                ${printContent.innerHTML}
                            </body>
                        </html>
                    `);
                    
                    printWindow.document.close();
                    
                    // Wait for content to load, then print
                    setTimeout(() => {
                        printWindow.focus();
                        printWindow.print();
                        // Close window after printing (optional)
                        // printWindow.close();
                    }, 250);
                },

                formatDate(dateString) {
                    if (!dateString) return '';
                    try {
                        const date = new Date(dateString + 'T00:00:00');
                        if (isNaN(date.getTime())) return dateString;
                        return date.toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: 'short', 
                            day: 'numeric' 
                        });
                    } catch (e) {
                        return dateString;
                    }
                },

                async submitInvoice(event) {
                    if (this.items.length === 0) {
                        window.notyf?.error('Please add at least one product to the invoice');
                        return;
                    }

                    this.isSubmitting = true;
                    try {
                        event.target.submit();
                    } catch (error) {
                        console.error(error);
                        this.isSubmitting = false;
                        window.notyf?.error('Failed to generate invoice. Please try again.');
                    }
                }
            };
        }
    </script>
</x-app-layout>
