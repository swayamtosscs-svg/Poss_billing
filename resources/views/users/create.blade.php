<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Create User') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Invite a teammate and assign the right role.') }}</p>
            </div>
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center px-4 py-2 rounded-full border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                {{ __('Back to Users') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl rounded-3xl p-8 space-y-8">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Full name')" class="dark:text-gray-300" />
                    <x-text-input id="name" name="name" type="text" class="mt-2 block w-full dark:bg-gray-700 dark:text-white dark:border-gray-600" required autofocus value="{{ old('name') }}" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email address')" class="dark:text-gray-300" />
                    <x-text-input id="email" name="email" type="email" class="mt-2 block w-full dark:bg-gray-700 dark:text-white dark:border-gray-600" required value="{{ old('email') }}" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="role_id" :value="__('Role')" class="dark:text-gray-300" />
                    <select id="role_id" name="role_id"
                            class="mt-2 block w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            required>
                        <option value="">{{ __('Select role') }}</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Temporary password')" class="dark:text-gray-300" />
                    <x-text-input id="password" name="password" type="password" class="mt-2 block w-full dark:bg-gray-700 dark:text-white dark:border-gray-600" required placeholder="••••••••" />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Share this password with the user; they can change it later.') }}</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}"
                       class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{ __('Cancel') }}
                    </a>
                    <x-primary-button class="px-6 py-3">
                        {{ __('Create user') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

