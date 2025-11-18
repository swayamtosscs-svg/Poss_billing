<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('Products') }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('products.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                    {{ __('Add Product') }}
                </a>
                <a href="{{ route('products.export', request()->query()) }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md shadow hover:bg-emerald-500">
                    {{ __('Export Excel') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="{
            viewMode: localStorage.getItem('products:viewMode') ?? 'grid',
            setView(mode) {
                this.viewMode = mode;
                localStorage.setItem('products:viewMode', mode);
            }
        }"
         class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Search') }}</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="{{ __('Name, barcode or SKU') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Category') }}</label>
                    <select name="category"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('All') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category'] ?? null) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="low_stock" value="1" @checked($filters['low_stock'] ?? false)
                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Low stock (< 10)') }}</span>
                    </label>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Import Products') }}</h3>
            </div>
            <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data" class="flex items-center gap-4">
                @csrf
                <input type="file" name="file"
                       class="rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                       accept=".xls,.xlsx,.csv" required>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-500">
                    {{ __('Upload & Import') }}
                </button>
            </form>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                {{ __('Columns: name, category, stock, price, barcode, sku, description') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between gap-4 flex-wrap sm:flex-nowrap">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Product Catalog') }}</h3>
                <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-700 p-1 shadow-sm">
                    <button type="button"
                            @click="setView('list')"
                            :class="viewMode === 'list' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow' : 'text-gray-600 dark:text-gray-400'"
                            class="px-3 py-1.5 text-sm font-medium rounded-md transition">
                        {{ __('List') }}
                    </button>
                    <button type="button"
                            @click="setView('grid')"
                            :class="viewMode === 'grid' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow' : 'text-gray-600 dark:text-gray-400'"
                            class="px-3 py-1.5 text-sm font-medium rounded-md transition">
                        {{ __('Grid') }}
                    </button>
                </div>
            </div>

            <div x-show="viewMode === 'list'" x-cloak class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Name') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Category') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Stock') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Price') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Barcode') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('SKU') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($products as $product)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="h-10 w-10 rounded object-cover">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $product->description ? \Illuminate\Support\Str::limit($product->description, 50) : __('No description') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $product->category?->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $product->stock < 10 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ currency_format($product->price) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $product->barcode ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $product->sku ?? '—' }}</td>
                            <td class="px-4 py-3 text-right text-sm font-medium">
                                <a href="{{ route('products.edit', $product) }}"
                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-semibold mr-3">{{ __('Edit') }}</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline"
                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 font-semibold">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('No products found.') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div x-show="viewMode === 'grid'" x-cloak>
                @if($products->isEmpty())
                    <div class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center text-sm text-gray-500 dark:text-gray-400 border border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                        <svg class="h-10 w-10 text-gray-300 dark:text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 7h2l3 9h8l3-9h2M5 11h4m6 0h4M9 21h0m6 0h0"/>
                        </svg>
                        <p>{{ __('No products match your current filters.') }}</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($products as $product)
                            <article class="group relative rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-lg transition">
                                <div class="flex items-start gap-4 p-5">
                                    <div class="relative h-16 w-16 flex-shrink-0 overflow-hidden rounded-xl bg-gradient-to-br from-orange-50 dark:from-orange-900/20 to-white dark:to-gray-800 border border-gray-100 dark:border-gray-700">
                                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <div class="space-y-1.5">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $product->name }}
                                            </h4>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2">
                                                {{ $product->description ? \Illuminate\Support\Str::limit($product->description, 80) : __('No description') }}
                                            </p>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                            <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 px-2 py-1 font-medium">
                                                {{ $product->category?->name ?? __('Uncategorized') }}
                                            </span>
                                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 font-semibold {{ $product->stock < 10 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' }}">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="M4 19h16M4 15l8-10 8 10"/>
                                                </svg>
                                                {{ $product->stock }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between gap-3 border-t border-gray-100 dark:border-gray-700 px-5 py-4 text-sm">
                                    <div class="space-y-1">
                                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Price') }}</p>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ currency_format($product->price) }}</p>
                                    </div>
                                    <div class="space-y-1 text-right text-xs text-gray-600 dark:text-gray-400">
                                        <p>{{ __('Barcode') }}: <span class="font-medium text-gray-800 dark:text-gray-300">{{ $product->barcode ?? '—' }}</span></p>
                                        <p>{{ __('SKU') }}: <span class="font-medium text-gray-800 dark:text-gray-300">{{ $product->sku ?? '—' }}</span></p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-700 px-5 py-4 text-sm">
                                    <a href="{{ route('products.edit', $product) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-semibold">{{ __('Edit') }}</a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 font-semibold">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="pt-2">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

