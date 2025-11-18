@php($editing = isset($expense) && $expense->exists)
<form method="POST"
      action="{{ $editing ? route('expenses.update', $expense) : route('expenses.store') }}"
      enctype="multipart/form-data"
      class="space-y-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white border border-gray-200 rounded-lg p-6">
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Expense Number') }}</label>
            <input name="expense_number"
                   value="{{ old('expense_number', $editing ? $expense->expense_number : ($expenseNumber ?? '')) }}"
                   required
                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
            <x-input-error :messages="$errors->get('expense_number')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Date') }}</label>
            <input type="date"
                   name="expense_date"
                   value="{{ old('expense_date', optional($editing ? $expense->expense_date : now())->format('Y-m-d')) }}"
                   required
                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
            <x-input-error :messages="$errors->get('expense_date')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Category') }}</label>
            <select name="expense_category_id"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                <option value="">{{ __('Choose category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        @selected((int) old('expense_category_id', $editing ? $expense->expense_category_id : '') === $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('expense_category_id')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Party / Vendor') }}</label>
            <select name="party_id"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                <option value="">{{ __('No specific vendor') }}</option>
                @foreach($parties as $party)
                    <option value="{{ $party->id }}"
                        @selected((int) old('party_id', $editing ? $expense->party_id : '') === $party->id)>
                        {{ $party->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('party_id')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Amount') }}</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">{{ currency_symbol() }}</span>
                <input type="number" min="0" step="0.01"
                       name="amount"
                       value="{{ old('amount', $editing ? $expense->amount : '') }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 pl-8">
            </div>
            <x-input-error :messages="$errors->get('amount')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Payment Method') }}</label>
            <input name="payment_method"
                   value="{{ old('payment_method', $editing ? $expense->payment_method : '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900"
                   placeholder="{{ __('Cash, UPI, Card...') }}">
            <x-input-error :messages="$errors->get('payment_method')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Bank Account') }}</label>
            <select name="bank_account_id"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                <option value="">{{ __('Not linked') }}</option>
                @foreach($bankAccounts as $account)
                    <option value="{{ $account->id }}"
                        @selected((int) old('bank_account_id', $editing ? $expense->bank_account_id : '') === $account->id)>
                        {{ $account->account_name }} — {{ $account->bank_name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('bank_account_id')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Receipt / Attachment') }}</label>
            <input type="file"
                   name="receipt"
                   accept=".jpg,.jpeg,.png,.pdf"
                   class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-blue-600 hover:file:bg-blue-100">
            <x-input-error :messages="$errors->get('receipt')" class="mt-2"/>
            @if($editing && $expense->receipt_path)
                <p class="mt-1 text-xs text-gray-500">
                    {{ __('Existing file:') }}
                    <a href="{{ Storage::disk('public')->url($expense->receipt_path) }}" target="_blank" class="text-blue-600 hover:text-blue-500 underline">
                        {{ basename($expense->receipt_path) }}
                    </a>
                </p>
            @endif
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">{{ __('Description / Notes') }}</label>
            <textarea name="description"
                      rows="4"
                      class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">{{ old('description', $editing ? $expense->description : '') }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2"/>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('expenses.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
            {{ __('Cancel') }}
        </a>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
            {{ $editing ? __('Update Expense') : __('Save Expense') }}
        </button>
    </div>
</form>





