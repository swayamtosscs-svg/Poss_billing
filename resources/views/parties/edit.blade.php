<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900">
                {{ __('Edit Party') }}
            </h2>
            <a href="{{ route('parties.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Update Details') }}</h3>
                <p class="text-sm text-gray-500">{{ __('Modify supplier contact, credit limits or address details.') }}</p>
            </div>

            @include('parties._form', ['party' => $party])
        </div>
    </div>
</x-app-layout>


