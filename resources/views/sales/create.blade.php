<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Point of Sale (POS)') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Delight guests with a fast, modern billing experience.') }}
                </p>
            </div>
            <a href="{{ route('sales.index') }}" class="text-sm font-medium text-orange-500 dark:text-orange-400 hover:text-orange-400">
                {{ __('View Sales History') }}
            </a>
        </div>
    </x-slot>

    <div
        class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8"
        x-data="posApp({
            productsUrl: '{{ route('products.search') }}',
            customersUrl: '{{ route('customers.search') }}',
            initialProducts: @json($products ?? []),
            categories: @json($categories ?? [])
        })"
        x-init="init()"
        x-on:keydown.window.escape="scannerOpen && closeScanner()"
    >
        <form method="POST" action="{{ route('sales.store') }}" class="space-y-6" @submit.prevent="submit">
            @csrf

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 xl:gap-8">
                <!-- Product browser -->
                <div class="xl:col-span-2 flex flex-col gap-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                            <div class="flex flex-col gap-3 w-full lg:w-auto lg:flex-row lg:items-center">
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 dark:text-gray-500">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="m21 21-3.5-3.5m0-7a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
                                        </svg>
                                    </span>
                                    <input
                                        type="text"
                                        x-model="productSearch"
                                        @keydown.enter.prevent="searchProducts"
                                        @input.debounce.400ms="searchProducts"
                                        placeholder="{{ __('Search in products, barcode or SKU...') }}"
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white pl-9 pr-4"
                                    >
                                </div>
                                <button
                                    type="button"
                                    class="px-6 py-2 rounded-xl bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold shadow-sm transition"
                                    @click="searchProducts"
                                >
                                    {{ __('Search') }}
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full lg:w-auto">
                                <div>
                            <label class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1">
                                        {{ __('Category') }}
                                    </label>
                                    <select
                                        x-model="selectCategory"
                                        @change="setCategory($event.target.value)"
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    >
                                        <option value="all">{{ __('All Category') }}</option>
                                        <template x-for="category in categories" :key="`select-${category.id}`">
                                            <option :value="category.id" x-text="category.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                            <label class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1">
                                        {{ __('Sort') }}
                                    </label>
                                    <select
                                        x-model="sortMode"
                                        @change="sortProducts"
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    >
                                        <option value="featured">{{ __('Featured') }}</option>
                                        <option value="price_low_high">{{ __('Price: Low to High') }}</option>
                                        <option value="price_high_low">{{ __('Price: High to Low') }}</option>
                                        <option value="name">{{ __('Name A-Z') }}</option>
                                        <option value="stock">{{ __('Stock High to Low') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="w-full lg:w-auto">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1">
                                    {{ __('Quick Actions') }}
                                </label>
                                <button type="button"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-dashed border-orange-300 dark:border-orange-600 bg-orange-50/60 dark:bg-orange-900/20 px-4 py-2 text-sm font-semibold text-orange-600 dark:text-orange-400 hover:bg-orange-100 dark:hover:bg-orange-900/30"
                                        @click="openScanner">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M3 7h4m10 0h4M5 7V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2m2 0v10a2 2 0 0 1-2 2h-3m-6 0H7a2 2 0 0 1-2-2V7m4 10h6m-9-7h12"/>
                                    </svg>
                                    {{ __('Scan Barcode') }}
                                </button>
                                <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">{{ __('Use your device camera to scan product barcodes.') }}</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-2">
                            <button
                                type="button"
                                @click="setCategory('all')"
                                :class="chipClass(activeCategory === 'all')"
                            >
                                {{ __('Show All') }}
                            </button>
                            <template x-for="category in categories" :key="category.id">
                                <button
                                    type="button"
                                    @click="setCategory(category.id)"
                                    :class="chipClass(activeCategory === category.id)"
                                    x-text="category.name"
                                ></button>
                                </template>
                        </div>
                    </div>

                    <div class="relative">
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
                            :class="{'opacity-50 pointer-events-none': isSearchingProducts}"
                        >
                            <template x-for="product in filteredProducts()" :key="product.id">
                                <button
                                    type="button"
                                    class="group relative flex flex-col rounded-2xl border border-gray-100 bg-white p-4 shadow-sm hover:border-orange-400 hover:shadow-lg transition"
                                    @click="addToCart(product)"
                                >
                                    <div class="relative h-32 w-full overflow-hidden rounded-xl bg-gradient-to-br from-orange-50 to-white flex items-center justify-center">
                                        <template x-if="product.image_url">
                                            <img :src="product.image_url" alt=""
                                                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                        </template>
                                        <template x-if="!product.image_url">
                                            <span class="text-lg font-semibold text-orange-500" x-text="product.name.charAt(0)"></span>
                                        </template>
                                        <div class="absolute top-3 right-3 rounded-full bg-white/90 px-2.5 py-1 text-xs font-semibold text-orange-500 shadow">
                                            <span x-text="currencySymbol + Number(product.price).toFixed(2)"></span>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex flex-col gap-1 text-left">
                                        <p class="text-sm font-semibold text-gray-900 truncate" :title="product.name" x-text="product.name"></p>
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span x-text="product.category_name ?? '{{ __('Uncategorized') }}'"></span>
                                            <span x-text="`${Number(product.stock ?? 0)} {{ __('in stock') }}`"></span>
                                        </div>
                        </div>
                                </button>
                                </template>
                        </div>

                        <div
                            x-show="isSearchingProducts"
                            class="absolute inset-0 flex items-center justify-center bg-white/70 rounded-2xl"
                        >
                            <div class="flex items-center gap-3 text-sm font-medium text-gray-500">
                                <svg class="animate-spin h-4 w-4 text-orange-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.364-6.364-2.121 2.121M8.757 15.243l-2.12 2.121m0-12.728 2.12 2.121m8.486 8.486 2.121 2.121"/>
                                </svg>
                                {{ __('Searching products...') }}
                            </div>
                        </div>

                        <div
                            x-show="!isSearchingProducts && !filteredProducts().length"
                            class="mt-6 rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center"
                        >
                            <p class="text-sm font-medium text-gray-600">
                                {{ __('No products match your filters.') }}
                            </p>
                            <button
                                type="button"
                                class="mt-3 inline-flex items-center justify-center rounded-full border border-orange-400 px-4 py-1.5 text-sm font-medium text-orange-500 hover:bg-orange-50"
                                @click="clearFilters"
                            >
                                {{ __('Clear filters') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cart & summary -->
                <div class="flex flex-col gap-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6 space-y-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <span class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ __('Order') }}</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="orderReference"></h3>
                                    <span class="rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-xs font-semibold px-2 py-0.5">
                                        {{ __('Active') }}
                                    </span>
                                </div>
                            </div>
                            <button type="button" @click="clearCart"
                                    class="inline-flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m1 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14Z"/>
                                </svg>
                                {{ __('Clear Cart') }}
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1">
                                    {{ __('Select Dining') }}
                                </label>
                                <select
                                    x-model="selectedDining"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                >
                                    <option value="walk_in">{{ __('Walk-in') }}</option>
                                    <option value="dine_in">{{ __('Dine In') }}</option>
                                    <option value="takeaway">{{ __('Takeaway') }}</option>
                                    <option value="delivery">{{ __('Delivery') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1">
                                    {{ __('Select Table') }}
                                </label>
                                <input
                                    type="text"
                                    x-model="selectedTable"
                                    placeholder="{{ __('E.g., T4 or Counter') }}"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                >
                            </div>
                        </div>

                        <div class="space-y-3" x-show="cart.length">
                            <template x-for="(item, index) in cart" :key="item.id">
                                <div class="flex gap-3 rounded-xl border border-gray-100 bg-gray-50 p-3">
                                    <div class="h-14 w-14 flex items-center justify-center overflow-hidden rounded-lg bg-white border border-gray-100">
                                        <template x-if="item.image_url">
                                            <img :src="item.image_url" alt="" class="h-full w-full object-cover">
                                        </template>
                                        <template x-if="!item.image_url">
                                            <span class="text-sm font-semibold text-orange-500" x-text="item.name.charAt(0)"></span>
                                        </template>
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900" x-text="item.name"></p>
                                                <p class="text-xs text-gray-500">
                                                    <span x-text="currencySymbol + Number(item.price).toFixed(2)"></span> ·
                                                    <span x-text="`${Number(item.stock ?? 0)} {{ __('in stock') }}`"></span>
                                                </p>
                                            </div>
                                            <button type="button" @click="removeItem(index)" class="text-gray-400 hover:text-red-500">
                                                <span class="sr-only">{{ __('Remove') }}</span>
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="m6 6 12 12M6 18 18 6"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-3">
                                            <div class="flex items-center rounded-full border border-gray-200 bg-white">
                                                <button type="button" class="px-3 py-1 text-sm text-gray-500 hover:text-orange-500" @click="decrementQuantity(index)">-</button>
                                                <input type="number" min="1" x-model.number="item.quantity" @input="recalculate"
                                                       class="w-14 border-0 bg-transparent text-center text-sm font-medium text-gray-700 focus:ring-0">
                                                <button type="button" class="px-3 py-1 text-sm text-gray-500 hover:text-orange-500" @click="incrementQuantity(index)">+</button>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                                <span>{{ __('Price') }}</span>
                                                <input type="number" min="0" step="0.01" x-model.number="item.price" @input="recalculate"
                                                       class="w-20 rounded-lg border-gray-300 bg-white text-gray-900 text-sm">
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                                <span>{{ __('Discount') }}</span>
                                                <input type="number" min="0" step="0.01" x-model.number="item.discount" @input="recalculate"
                                                       class="w-20 rounded-lg border-gray-300 bg-white text-gray-900 text-sm">
                                            </div>
                                            <div class="ml-auto text-right">
                                                <p class="text-xs text-gray-400">{{ __('Total') }}</p>
                                                <p class="text-sm font-semibold text-gray-900" x-text="currencySymbol + lineTotal(item).toFixed(2)"></p>
                                            </div>
                                        </div>
                                        <button type="button" class="text-xs font-medium text-orange-500 hover:underline">
                                            {{ __('Add Notes') }}
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div x-show="!cart.length" class="flex flex-col items-center justify-center gap-3 py-10 text-center text-sm text-gray-500">
                            <svg class="h-10 w-10 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M3 5h18M3 10h18M6 15h12M9 20h6"/>
                            </svg>
                            <p>{{ __('Your order is empty. Tap on menu items to add them here.') }}</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Customer') }}</h3>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="customerSearch"
                                @input.debounce.400ms="searchCustomers"
                                placeholder="{{ __('Search by name, email or phone') }}"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white pl-10"
                            >
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 dark:text-gray-500">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M5 7h14M5 12h14M5 17h6"/>
                                </svg>
                            </span>
                        </div>
                        <div class="max-h-48 overflow-y-auto rounded-xl border border-gray-100 divide-y divide-gray-100"
                             x-show="customerResults.length">
                            <template x-for="customer in customerResults" :key="customer.id">
                                <button type="button" class="w-full text-left px-4 py-3 hover:bg-orange-50"
                                        @click="selectCustomer(customer)">
                                    <p class="text-sm font-medium text-gray-900" x-text="customer.name"></p>
                                    <p class="text-xs text-gray-500" x-text="customer.email ?? customer.phone ?? '{{ __('Not available') }}'"></p>
                                </button>
                            </template>
                        </div>
                        <div
                            class="rounded-xl border border-dashed border-orange-300 bg-orange-50/60 px-4 py-3 text-sm text-orange-700"
                            x-show="selectedCustomer"
                        >
                            <p class="font-semibold" x-text="selectedCustomer?.name"></p>
                            <p x-text="selectedCustomer?.email"></p>
                            <p x-text="selectedCustomer?.phone"></p>
                            <p class="text-xs mt-1">
                                {{ __('Points') }}:
                                <span class="font-medium" x-text="selectedCustomer?.points ?? 0"></span>
                            </p>
                                <button type="button" class="mt-2 inline-flex items-center text-xs font-medium text-orange-500 hover:underline"
                                    @click="selectedCustomer = null">
                                {{ __('Clear customer') }}
                            </button>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6 space-y-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Bill Summary') }}</h3>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center justify-between">
                                <span>{{ __('Sub total') }}</span>
                                <span class="dark:text-gray-300" x-text="currencySymbol + grossTotal.toFixed(2)"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>{{ __('Product Discount') }}</span>
                                <span class="text-emerald-600 dark:text-emerald-400" x-text="`- ${currencySymbol}${lineDiscountTotal.toFixed(2)}`"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>{{ __('Extra Discount') }}</span>
                                <div class="flex items-center gap-2">
                                    <input type="number" min="0" step="0.01" x-model.number="discount" @input="recalculate"
                                           class="w-24 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-right text-sm">
                                    <span class="text-emerald-600 dark:text-emerald-400" x-text="`- ${currencySymbol}${Number(discount || 0).toFixed(2)}`"></span>
                                </div>
                        </div>
                            <div class="flex items-center justify-between">
                                <span>{{ __('Coupon Discount') }}</span>
                                <span class="text-emerald-600 dark:text-emerald-400" x-text="`- ${currencySymbol}${Number(couponDiscount || 0).toFixed(2)}`"></span>
                        </div>
                            <div class="flex items-center justify-between">
                            <span>{{ __('Tax') }}</span>
                                <div class="flex items-center gap-2">
                                    <input type="number" min="0" step="0.01" x-model.number="tax" @input="recalculate"
                                           class="w-24 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-right text-sm">
                                    <span class="dark:text-gray-300" x-text="`${currencySymbol}${Number(tax || 0).toFixed(2)}`"></span>
                        </div>
                        </div>
                    </div>
                        <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
                            <span class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Total') }}</span>
                            <span class="text-2xl font-bold text-orange-500 dark:text-orange-400" x-text="currencySymbol + grandTotal.toFixed(2)"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button type="button" @click="printDraft"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-gray-600 px-4 py-3 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:border-orange-400 dark:hover:border-orange-500 hover:text-orange-500 dark:hover:text-orange-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M6 9V4h12v5M6 14H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1m-4 0h-4m-4 0v7h12v-7"/>
                                </svg>
                                {{ __('KOT & Print') }}
                            </button>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold text-white hover:bg-orange-400 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!cart.length || isSubmitting"
                            >
                                <svg x-show="isSubmitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.364-6.364-2.121 2.121M8.757 15.243l-2.12 2.121m0-12.728 2.12 2.121m8.486 8.486 2.121 2.121"/>
                                </svg>
                                <span x-show="!isSubmitting">{{ __('Bill & Payment') }}</span>
                                <span x-show="isSubmitting">{{ __('Processing...') }}</span>
                            </button>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1">{{ __('Payment Method') }}</label>
                                <select
                                    x-model="paymentType"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                >
                                <option value="cash">{{ __('Cash') }}</option>
                                <option value="card">{{ __('Card') }}</option>
                                <option value="upi">{{ __('UPI') }}</option>
                                <option value="wallet">{{ __('Wallet') }}</option>
                                <option value="netbanking">{{ __('Net Banking') }}</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden fields -->
            <template x-for="(item, index) in cart" :key="`hidden-${index}`">
                <div>
                    <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                    <input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity">
                    <input type="hidden" :name="`items[${index}][price]`" :value="item.price">
                    <input type="hidden" :name="`items[${index}][discount]`" :value="item.discount">
                </div>
            </template>
            <input type="hidden" name="customer_id" :value="selectedCustomer?.id ?? ''">
            <input type="hidden" name="payment_type" :value="paymentType">
            <input type="hidden" name="discount" :value="discount">
            <input type="hidden" name="tax" :value="tax">
        </form>

        <div
            x-cloak
            x-show="scannerOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4 py-8"
            @click.self="closeScanner"
        >
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Scan Product Barcode') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Allow camera access and align the barcode in the frame.') }}</p>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600 text-xl leading-none" @click="closeScanner">&times;</button>
                </div>
                <div id="barcode-scanner" class="w-full h-64 rounded-2xl border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-sm text-gray-400">
                    {{ __('Camera starting...') }}
                </div>
                <p class="text-xs text-gray-500 min-h-[1.25rem]" x-text="scannerStatus"></p>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between text-xs text-gray-600">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500" x-model="continuousScan">
                        {{ __('Keep scanner open after each detection') }}
                    </label>
                    <button type="button" class="inline-flex items-center gap-2 text-orange-600 font-semibold hover:text-orange-500" @click="restartScanner">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 4v6h6M20 20v-6h-6M5.64 18.36A9 9 0 1 0 18.36 5.64"/>
                        </svg>
                        {{ __('Restart camera') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.10" defer></script>
    <script>
        function posApp({ productsUrl, customersUrl, initialProducts = [], categories = [] }) {
            return {
                productsUrl,
                customersUrl,
                currencySymbol: @json(currency_symbol()),
                allProducts: initialProducts,
                displayedProducts: initialProducts,
                categories,
                productSearch: '',
                selectCategory: 'all',
                activeCategory: 'all',
                sortMode: 'featured',
                isSearchingProducts: false,
                customerSearch: '',
                customerResults: [],
                selectedCustomer: null,
                cart: [],
                grossTotal: 0,
                lineDiscountTotal: 0,
                subtotal: 0,
                discount: 0,
                couponDiscount: 0,
                tax: 0,
                grandTotal: 0,
                paymentType: 'cash',
                selectedDining: 'walk_in',
                selectedTable: '',
                orderReference: '#20',
                isSubmitting: false,
                scannerOpen: false,
                scannerStatus: '',
                scannerInstance: null,
                continuousScan: false,
                lastScannedCode: null,
                lastScanAt: 0,
                init() {
                    this.recalculate();
                    this.generateReference();
                },
                generateReference() {
                    const random = Math.floor(Math.random() * 900) + 100;
                    this.orderReference = `#${random}`;
                },
                chipClass(isActive) {
                    return [
                        'px-4 py-2 rounded-full text-sm font-medium border transition',
                        isActive
                            ? 'bg-orange-500 text-white border-orange-500 shadow'
                            : 'bg-white text-gray-600 border-gray-200 hover:border-orange-400 hover:text-orange-500'
                    ].join(' ');
                },
                filteredProducts() {
                    let products = [...this.displayedProducts];

                    if (this.activeCategory !== 'all') {
                        const categoryId = Number(this.activeCategory);
                        products = products.filter(product => Number(product.category_id) === categoryId);
                    }

                    if (this.productSearch.trim()) {
                        const term = this.productSearch.trim().toLowerCase();
                        products = products.filter(product =>
                            [product.name, product.barcode, product.sku]
                                .filter(Boolean)
                                .some(value => value.toLowerCase().includes(term))
                        );
                    }

                    return products;
                },
                clearFilters() {
                    this.productSearch = '';
                    this.activeCategory = 'all';
                    this.selectCategory = 'all';
                    this.displayedProducts = [...this.allProducts];
                },
                setCategory(categoryId) {
                    this.activeCategory = categoryId === undefined ? 'all' : categoryId;
                    this.selectCategory = this.activeCategory;
                },
                sortProducts() {
                    const base = this.productSearch.trim()
                        ? this.displayedProducts
                        : this.allProducts;

                    this.displayedProducts = this.applySorting(base);
                },
                applySorting(collection) {
                    const sorter = {
                        price_low_high: (a, b) => Number(a.price) - Number(b.price),
                        price_high_low: (a, b) => Number(b.price) - Number(a.price),
                        name: (a, b) => a.name.localeCompare(b.name),
                        stock: (a, b) => Number(b.stock ?? 0) - Number(a.stock ?? 0),
                    }[this.sortMode];

                    if (!sorter) {
                        return [...collection];
                    }

                    return [...collection].sort(sorter);
                },
                async searchProducts() {
                    if (!this.productSearch.trim()) {
                        this.displayedProducts = this.applySorting(this.allProducts);
                        this.isSearchingProducts = false;
                        return;
                    }

                    this.isSearchingProducts = true;
                    try {
                        const response = await fetch(`${this.productsUrl}?q=${encodeURIComponent(this.productSearch)}`);
                        const { data } = await response.json();
                        const results = Array.isArray(data) ? data : [];
                        this.displayedProducts = this.applySorting(results);
                    } catch (error) {
                        console.error(error);
                        window.notyf?.error('{{ __('Unable to load products') }}');
                    } finally {
                        this.isSearchingProducts = false;
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
                        window.notyf?.error('{{ __('Unable to load customers') }}');
                    }
                },
                selectCustomer(customer) {
                    this.selectedCustomer = customer;
                    this.customerSearch = customer.name;
                    this.customerResults = [];
                },
                addToCart(product) {
                    const existing = this.cart.find(item => item.id === product.id);
                    if (existing) {
                        existing.quantity += 1;
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price: Number(product.price),
                            quantity: 1,
                            discount: 0,
                            image_url: product.image_url ?? null,
                            stock: product.stock ?? 0,
                        });
                    }
                    this.recalculate();
                    window.notyf?.success('{{ __('Added to cart') }}');
                },
                incrementQuantity(index) {
                    this.cart[index].quantity += 1;
                    this.recalculate();
                },
                decrementQuantity(index) {
                    if (this.cart[index].quantity > 1) {
                        this.cart[index].quantity -= 1;
                    } else {
                        this.cart.splice(index, 1);
                    }
                    this.recalculate();
                },
                removeItem(index) {
                    this.cart.splice(index, 1);
                    this.recalculate();
                },
                clearCart() {
                    if (!this.cart.length) {
                        return;
                    }
                    if (confirm('{{ __('Clear all items from the cart?') }}')) {
                        this.cart = [];
                        this.recalculate();
                    }
                },
                lineTotal(item) {
                    return Math.max(0, (Number(item.price) * Number(item.quantity)) - Number(item.discount || 0));
                },
                recalculate() {
                    const gross = this.cart.reduce((carry, item) => carry + (Number(item.price) * Number(item.quantity)), 0);
                    const lineDiscount = this.cart.reduce((carry, item) => carry + Number(item.discount || 0), 0);

                    this.grossTotal = gross;
                    this.lineDiscountTotal = lineDiscount;
                    this.subtotal = Math.max(0, gross - lineDiscount);

                    const extraDiscount = Number(this.discount || 0);
                    const couponDiscount = Number(this.couponDiscount || 0);
                    const tax = Number(this.tax || 0);

                    this.grandTotal = Math.max(0, this.subtotal - extraDiscount - couponDiscount) + tax;
                },
                openScanner() {
                    this.scannerOpen = true;
                    this.scannerStatus = '{{ __('Initializing camera...') }}';
                    this.$nextTick(() => this.startScanner());
                },
                async restartScanner() {
                    if (!this.scannerOpen) {
                        return;
                    }
                    this.scannerStatus = '{{ __('Restarting camera...') }}';
                    await this.startScanner();
                },
                async startScanner() {
                    if (!window.Html5Qrcode) {
                        this.scannerStatus = '{{ __('Scanner library failed to load. Please refresh and try again.') }}';
                        return;
                    }

                    if (!this.scannerInstance) {
                        this.scannerInstance = new Html5Qrcode('barcode-scanner');
                    } else {
                        try {
                            await this.scannerInstance.stop();
                            await this.scannerInstance.clear();
                        } catch (error) {
                            console.error(error);
                        }
                    }

                    try {
                        this.scannerStatus = '{{ __('Requesting camera access...') }}';
                        const cameras = await Html5Qrcode.getCameras();
                        const cameraId = cameras?.[0]?.id;
                        if (!cameraId) {
                            this.scannerStatus = '{{ __('No camera available on this device.') }}';
                            return;
                        }

                        const config = { fps: 10, qrbox: { width: 250, height: 150 } };
                        if (window.Html5QrcodeSupportedFormats) {
                            config.formatsToSupport = [
                                Html5QrcodeSupportedFormats.EAN_13,
                                Html5QrcodeSupportedFormats.EAN_8,
                                Html5QrcodeSupportedFormats.UPC_A,
                                Html5QrcodeSupportedFormats.UPC_E,
                                Html5QrcodeSupportedFormats.CODE_128,
                            ].filter(Boolean);
                        }

                        await this.scannerInstance.start(
                            cameraId,
                            config,
                            decodedText => this.handleScanResult(decodedText)
                        );
                        this.scannerStatus = '{{ __('Aim the barcode inside the frame to scan.') }}';
                    } catch (error) {
                        console.error(error);
                        this.scannerStatus = '{{ __('Unable to start the camera. Please allow camera access and try again.') }}';
                    }
                },
                async handleScanResult(decodedText) {
                    if (!decodedText) {
                        return;
                    }

                    const cleaned = decodedText.trim();
                    if (!cleaned) {
                        return;
                    }

                    const now = Date.now();
                    if (this.lastScannedCode === cleaned && (now - this.lastScanAt) < 1500) {
                        return;
                    }
                    this.lastScannedCode = cleaned;
                    this.lastScanAt = now;

                    this.scannerStatus = `{{ __('Detected') }}: ${cleaned}`;
                    await this.findProductByBarcode(cleaned);

                    if (!this.continuousScan) {
                        await this.closeScanner();
                    }
                },
                async findProductByBarcode(barcode) {
                    try {
                        const response = await fetch(`${this.productsUrl}?q=${encodeURIComponent(barcode)}`);
                        const { data } = await response.json();
                        const lower = barcode.toLowerCase();
                        const product = (data || []).find(item => (item.barcode ?? '').toLowerCase() === lower) ?? (data || [])[0];

                        if (product) {
                            this.addToCart(product);
                            window.notyf?.success('{{ __('Product added from barcode scan.') }}');
                        } else {
                            window.notyf?.error('{{ __('No product matches the scanned barcode.') }}');
                        }
                    } catch (error) {
                        console.error(error);
                        window.notyf?.error('{{ __('Unable to search for the scanned barcode.') }}');
                    }
                },
                async closeScanner() {
                    this.scannerOpen = false;
                    this.scannerStatus = '';
                    this.lastScannedCode = null;
                    if (this.scannerInstance) {
                        try {
                            await this.scannerInstance.stop();
                            await this.scannerInstance.clear();
                        } catch (error) {
                            console.error(error);
                        }
                    }
                },
                printDraft() {
                    window.print();
                },
                async submit(event) {
                    if (!this.cart.length) {
                        window.notyf?.error('{{ __('Add at least one product to the cart.') }}');
                        return;
                    }
                    this.isSubmitting = true;
                    try {
                        event.target.submit();
                    } catch (error) {
                        this.isSubmitting = false;
                        window.notyf?.error('{{ __('Unable to submit sale, please try again.') }}');
                    }
                }
            };
        }
    </script>
</x-app-layout>

