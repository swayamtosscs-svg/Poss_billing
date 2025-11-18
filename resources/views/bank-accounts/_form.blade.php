@php($editing = isset($bankAccount) && $bankAccount->exists)

<form method="POST"
      action="{{ $editing ? route('bank-accounts.update', $bankAccount) : route('bank-accounts.store') }}"
      class="space-y-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Account Name') }}</label>
                <input name="account_name"
                       value="{{ old('account_name', $bankAccount->account_name ?? '') }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                <x-input-error :messages="$errors->get('account_name')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Bank Name') }}</label>
                <input name="bank_name"
                       value="{{ old('bank_name', $bankAccount->bank_name ?? '') }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                <x-input-error :messages="$errors->get('bank_name')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Account Number') }}</label>
                <input name="account_number"
                       value="{{ old('account_number', $bankAccount->account_number ?? '') }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                <x-input-error :messages="$errors->get('account_number')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('IFSC / Routing Code') }}</label>
                <input name="ifsc_code"
                       value="{{ old('ifsc_code', $bankAccount->ifsc_code ?? '') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900"
                       placeholder="{{ __('Optional') }}">
                <x-input-error :messages="$errors->get('ifsc_code')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Branch') }}</label>
                <input name="branch"
                       value="{{ old('branch', $bankAccount->branch ?? '') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900"
                       placeholder="{{ __('Optional') }}">
                <x-input-error :messages="$errors->get('branch')" class="mt-2"/>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:col-span-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Opening Balance') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">{{ currency_symbol() }}</span>
                        <input type="number"
                               name="opening_balance"
                               min="0"
                               step="0.01"
                               value="{{ old('opening_balance', $bankAccount->opening_balance ?? 0) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 pl-8">
                    </div>
                    <x-input-error :messages="$errors->get('opening_balance')" class="mt-2"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Current Balance') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">{{ currency_symbol() }}</span>
                        <input type="number"
                               name="current_balance"
                               min="0"
                               step="0.01"
                               value="{{ old('current_balance', $bankAccount->current_balance ?? ($bankAccount->opening_balance ?? 0)) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 pl-8">
                    </div>
                    <x-input-error :messages="$errors->get('current_balance')" class="mt-2"/>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Leave blank to use the opening balance.') }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <input id="is_active"
                   type="checkbox"
                   name="is_active"
                   value="1"
                   @checked(old('is_active', ($bankAccount->is_active ?? true)) == true)
                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="is_active" class="text-sm font-medium text-gray-700">
                {{ __('Account is active and available for transactions') }}
            </label>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('bank-accounts.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
            {{ __('Cancel') }}
        </a>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
            {{ $editing ? __('Update Account') : __('Create Account') }}
        </button>
    </div>
</form>


