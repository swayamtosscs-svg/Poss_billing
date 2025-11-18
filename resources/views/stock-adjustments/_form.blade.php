@php($editing = isset($stockAdjustment) && $stockAdjustment->exists)
<form method="POST"
      action="{{ $editing ? route('stock-adjustments.update', $stockAdjustment) : route('stock-adjustments.store') }}"
      class="space-y-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Adjustment Number') }}</label>
                <input name="adjustment_number"
                       value="{{ old('adjustment_number', $editing ? $stockAdjustment->adjustment_number : ($adjustmentNumber ?? '')) }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                <x-input-error :messages="$errors->get('adjustment_number')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Adjustment Date') }}</label>
                <input type="date"
                       name="adjustment_date"
                       value="{{ old('adjustment_date', optional($editing ? $stockAdjustment->adjustment_date : now())->format('Y-m-d')) }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                <x-input-error :messages="$errors->get('adjustment_date')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Product') }}</label>
                <select name="product_id"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                    <option value="">{{ __('Select product') }}</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            @selected((int) old('product_id', $editing ? $stockAdjustment->product_id : '') === $product->id)>
                            {{ $product->name }} @if($product->sku) ({{ $product->sku }}) @endif — {{ __('Stock:') }} {{ $product->stock }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('product_id')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Type') }}</label>
                <div class="mt-2 flex items-center gap-6">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="radio" name="type" value="add"
                               @checked(old('type', $editing ? $stockAdjustment->type : 'add') === 'add')
                               class="text-emerald-600 border-gray-300 focus:ring-emerald-500">
                        {{ __('Add to stock') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="radio" name="type" value="remove"
                               @checked(old('type', $editing ? $stockAdjustment->type : 'add') === 'remove')
                               class="text-red-600 border-gray-300 focus:ring-red-500">
                        {{ __('Remove from stock') }}
                    </label>
                </div>
                <x-input-error :messages="$errors->get('type')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Quantity') }}</label>
                <input type="number" min="1"
                       name="quantity"
                       value="{{ old('quantity', $editing ? $stockAdjustment->quantity : '') }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                <x-input-error :messages="$errors->get('quantity')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Reason') }}</label>
                <select name="reason"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900"
                        required>
                    @foreach($reasonOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('reason', $editing ? $stockAdjustment->reason : 'other') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('reason')" class="mt-2"/>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Notes') }}</label>
            <textarea name="notes"
                      rows="4"
                      class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">{{ old('notes', $editing ? $stockAdjustment->notes : '') }}</textarea>
            <p class="mt-1 text-xs text-gray-500">{{ __('Keep a brief note about the adjustment (optional).') }}</p>
            <x-input-error :messages="$errors->get('notes')" class="mt-2"/>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('stock-adjustments.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
            {{ __('Cancel') }}
        </a>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
            {{ $editing ? __('Update Adjustment') : __('Save Adjustment') }}
        </button>
    </div>
</form>





