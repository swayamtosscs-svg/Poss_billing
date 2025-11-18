@php($editing = isset($customer) && $customer->exists)
<form method="POST" action="{{ $editing ? route('customers.update', $customer) : route('customers.store') }}" class="space-y-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Full Name') }}</label>
            <input name="name" value="{{ old('name', $customer->name ?? '') }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Email') }}</label>
            <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Phone') }}</label>
            <input name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <x-input-error :messages="$errors->get('phone')" class="mt-2"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Loyalty Points') }}</label>
            <input type="number" min="0" name="points" value="{{ old('points', $customer->points ?? 0) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <x-input-error :messages="$errors->get('points')" class="mt-2"/>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Address') }}</label>
        <textarea name="address" rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">{{ old('address', $customer->address ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('address')" class="mt-2"/>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('customers.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md">
            {{ __('Cancel') }}
        </a>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
            {{ $editing ? __('Update Customer') : __('Create Customer') }}
        </button>
    </div>
</form>

