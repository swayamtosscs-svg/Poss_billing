<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Manage User') }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('Update profile details, role and credentials.') }}</p>
            </div>
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center px-4 py-2 rounded-full border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                {{ __('Back to Users') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white border border-gray-200 shadow-xl rounded-3xl p-8 space-y-8">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="name" :value="__('Full name')" />
                    <x-text-input id="name" name="name" type="text"
                                  class="mt-2 block w-full" required autofocus
                                  value="{{ old('name', $user->name) }}" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email address')" />
                    <x-text-input id="email" name="email" type="email"
                                  class="mt-2 block w-full" required
                                  value="{{ old('email', $user->email) }}" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                    <div>
                        <x-input-label for="role_id" :value="__('Role')" />
                        <select id="role_id" name="role_id"
                                class="mt-2 block w-full rounded-xl border-gray-300 bg-white text-gray-900"
                                required>
                            <option value="">{{ __('Select role') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                    </div>

                <div>
                    <x-input-label for="password" :value="__('Reset password (optional)')" />
                    <x-text-input id="password" name="password" type="password"
                                  class="mt-2 block w-full" placeholder="••••••••" />
                    <p class="text-xs text-gray-500 mt-1">{{ __('Leave blank to keep the current password.') }}</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}"
                       class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        {{ __('Cancel') }}
                    </a>
                    <x-primary-button class="px-6 py-3">
                        {{ __('Save changes') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

