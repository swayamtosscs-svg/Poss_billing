@php
    $editing = isset($payment) && $payment->exists;
    $currentPurchaseId = old('purchase_id', $editing && $payment->paymentable instanceof \App\Models\Purchase ? $payment->paymentable_id : '');
    $currentSaleId = old('sale_id', $editing && $payment->paymentable instanceof \App\Models\Sale ? $payment->paymentable_id : '');
@endphp

<form method="POST"
      action="{{ $editing ? route('payments.update', $payment) : route('payments.store') }}"
      class="space-y-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Payment Number') }}</label>
                <input name="payment_number"
                       value="{{ old('payment_number', $editing ? $payment->payment_number : ($paymentNumber ?? '')) }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                <x-input-error :messages="$errors->get('payment_number')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Payment Date') }}</label>
                <input type="date"
                       name="payment_date"
                       value="{{ old('payment_date', optional($editing ? $payment->payment_date : now())->format('Y-m-d')) }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                <x-input-error :messages="$errors->get('payment_date')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Payment Type') }}</label>
                <div class="mt-2 flex items-center gap-6">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="radio" name="type" value="in"
                               @checked(old('type', $editing ? $payment->type : 'in') === 'in')
                               class="text-emerald-600 border-gray-300 focus:ring-emerald-500">
                        {{ __('Payment In (Received)') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="radio" name="type" value="out"
                               @checked(old('type', $editing ? $payment->type : 'in') === 'out')
                               class="text-red-600 border-gray-300 focus:ring-red-500">
                        {{ __('Payment Out (Paid)') }}
                    </label>
                </div>
                <x-input-error :messages="$errors->get('type')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Party') }}</label>
                <select name="party_id"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                    <option value="">{{ __('Optional') }}</option>
                    @foreach($parties as $party)
                        <option value="{{ $party->id }}"
                            @selected((int) old('party_id', $editing ? $payment->party_id : '') === $party->id)>
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
                           value="{{ old('amount', $editing ? $payment->amount : '') }}"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 pl-8">
                </div>
                <x-input-error :messages="$errors->get('amount')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Payment Method') }}</label>
                <input name="payment_method"
                       value="{{ old('payment_method', $editing ? $payment->payment_method : '') }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900"
                       placeholder="{{ __('Cash, UPI, Card, Cheque...') }}">
                <x-input-error :messages="$errors->get('payment_method')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Bank Account') }}</label>
                <select name="bank_account_id"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                    <option value="">{{ __('Not linked') }}</option>
                    @foreach($bankAccounts as $account)
                        <option value="{{ $account->id }}"
                            @selected((int) old('bank_account_id', $editing ? $payment->bank_account_id : '') === $account->id)>
                            {{ $account->account_name }} — {{ $account->bank_name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('bank_account_id')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Reference Number') }}</label>
                <input name="reference_number"
                       value="{{ old('reference_number', $editing ? $payment->reference_number : '') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900"
                       placeholder="{{ __('Transaction ID / Cheque # / UTR') }}">
                <x-input-error :messages="$errors->get('reference_number')" class="mt-2"/>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Link to Purchase') }}</label>
                <select name="purchase_id"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                    <option value="">{{ __('No purchase linked') }}</option>
                    @foreach($purchases as $purchase)
                        <option value="{{ $purchase->id }}" @selected((int) $currentPurchaseId === $purchase->id)>
                            {{ __('Purchase #:id', ['id' => $purchase->id]) }} — {{ currency_format($purchase->total_amount) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('purchase_id')" class="mt-2"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Link to Sale') }}</label>
                <select name="sale_id"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">
                    <option value="">{{ __('No sale linked') }}</option>
                    @foreach($sales as $sale)
                        <option value="{{ $sale->id }}" @selected((int) $currentSaleId === $sale->id)>
                            {{ __('Sale #:id', ['id' => $sale->id]) }} — {{ currency_format($sale->total_amount) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('sale_id')" class="mt-2"/>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Notes') }}</label>
            <textarea name="notes"
                      rows="4"
                      class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900">{{ old('notes', $editing ? $payment->notes : '') }}</textarea>
            <x-input-error :messages="$errors->get('notes')" class="mt-2"/>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('payments.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
            {{ __('Cancel') }}
        </a>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
            {{ $editing ? __('Update Payment') : __('Save Payment') }}
        </button>
    </div>
</form>





