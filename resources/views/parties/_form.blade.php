@php($editing = isset($party) && $party->exists)
<form method="POST"
      action="{{ $editing ? route('parties.update', $party) : route('parties.store') }}"
      class="space-y-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Party Type') }}</label>
            <select name="type"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                <option value="supplier" @selected(old('type', $party->type ?? '') === 'supplier')>{{ __('Supplier') }}</option>
                <option value="customer" @selected(old('type', $party->type ?? '') === 'customer')>{{ __('Customer') }}</option>
            </select>
            <x-input-error :messages="$errors->get('type')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Party Name') }}</label>
            <input name="name"
                   value="{{ old('name', $party->name ?? '') }}"
                   required
                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Phone') }}</label>
            <input name="phone"
                   value="{{ old('phone', $party->phone ?? '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
            <x-input-error :messages="$errors->get('phone')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $party->email ?? '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('GSTIN') }}</label>
            <input name="gstin"
                   value="{{ old('gstin', $party->gstin ?? '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
            <x-input-error :messages="$errors->get('gstin')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Balance Type') }}</label>
            <div class="mt-2 flex items-center gap-6">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="radio" name="balance_type" value="receivable"
                           @checked(old('balance_type', $party->balance_type ?? 'receivable') === 'receivable')
                           class="text-blue-600 border-gray-300 focus:ring-blue-500">
                    {{ __('Receivable') }}
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="radio" name="balance_type" value="payable"
                           @checked(old('balance_type', $party->balance_type ?? 'receivable') === 'payable')
                           class="text-blue-600 border-gray-300 focus:ring-blue-500">
                    {{ __('Payable') }}
                </label>
            </div>
            <x-input-error :messages="$errors->get('balance_type')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Opening Balance') }}</label>
            <input type="number"
                   name="opening_balance"
                   min="0"
                   step="0.01"
                   value="{{ old('opening_balance', $party->opening_balance ?? 0) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
            <x-input-error :messages="$errors->get('opening_balance')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Credit Limit') }}</label>
            <input type="number"
                   name="credit_limit"
                   min="0"
                   step="0.01"
                   value="{{ old('credit_limit', $party->credit_limit ?? '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
            <x-input-error :messages="$errors->get('credit_limit')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Credit Days') }}</label>
            <input type="number"
                   name="credit_days"
                   min="0"
                   step="1"
                   value="{{ old('credit_days', $party->credit_days ?? '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
            <x-input-error :messages="$errors->get('credit_days')" class="mt-2"/>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Billing Address') }}</label>
            <textarea name="billing_address" rows="4"
                      class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">{{ old('billing_address', $party->billing_address ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('billing_address')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Shipping Address') }}</label>
            <textarea name="shipping_address" rows="4"
                      class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">{{ old('shipping_address', $party->shipping_address ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('shipping_address')" class="mt-2"/>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('parties.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
            {{ __('Cancel') }}
        </a>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
            {{ $editing ? __('Update Party') : __('Create Party') }}
        </button>
    </div>
</form>


