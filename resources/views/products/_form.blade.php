@php($editing = isset($product) && $product->exists)
<form method="POST"
      action="{{ $editing ? route('products.update', $product) : route('products.store') }}"
      enctype="multipart/form-data"
      class="space-y-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Name') }}</label>
            <input name="name" value="{{ old('name', $product->name ?? '') }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Category') }}</label>
            <select name="category_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <option value="">{{ __('Select category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('category_id')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Price') }}</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <x-input-error :messages="$errors->get('price')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Stock') }}</label>
            <input type="number" name="stock" min="0" value="{{ old('stock', $product->stock ?? 0) }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <x-input-error :messages="$errors->get('stock')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Barcode') }}</label>
            <input name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border_gray-700 dark:bg-gray-800 dark:text-gray-100">
            <x-input-error :messages="$errors->get('barcode')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('SKU') }}</label>
            <input name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <x-input-error :messages="$errors->get('sku')" class="mt-2"/>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Description') }}</label>
        <textarea name="description" rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">{{ old('description', $product->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2"/>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Product Image') }}</label>
        <input type="file" name="image" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-100">
        <x-input-error :messages="$errors->get('image')" class="mt-2"/>
        @if($editing && $product->image_path)
            <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}"
                 class="mt-3 h-24 rounded object-cover">
        @endif
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('products.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md">
            {{ __('Cancel') }}
        </a>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
            {{ $editing ? __('Update Product') : __('Create Product') }}
        </button>
    </div>
</form>

