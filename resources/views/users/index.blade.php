<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('Users & Roles') }}
            </h2>
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                {{ __('Create User') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6"
         x-data="{
             selectedUsers: [],
             allUserIds: [@can('is-admin'){{ $users->filter(fn($u) => $u->id !== auth()->id())->map(fn($u) => '"' . $u->id . '"')->join(',') }}@endcan],
             get selectAll() {
                 return this.allUserIds.length > 0 && this.allUserIds.every(id => this.selectedUsers.includes(id));
             },
             set selectAll(value) {
                 this.selectedUsers = value ? [...this.allUserIds] : [];
             }
         }">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow rounded-lg p-6">
            <form method="GET" class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:gap-4">
                    <label class="flex flex-col text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Search') }}</span>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                               class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="{{ __('Name or email') }}">
                    </label>
                    <label class="flex flex-col text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Role') }}</span>
                        <select name="role" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">{{ __('All') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected(($filters['role'] ?? null) == $role->id)>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('users.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        @can('is-admin')
            <div x-show="selectedUsers.length > 0" x-cloak
                 class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 flex items-center justify-between">
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span x-text="selectedUsers.length"></span> {{ __('user(s) selected') }}
                </span>
                <form method="POST" action="{{ route('users.bulk-destroy') }}" class="inline"
                      x-ref="bulkDeleteForm"
                      @submit.prevent="if(confirm('{{ __('Are you sure you want to delete the selected users?') }}')) { $refs.bulkDeleteForm.submit(); }">
                    @csrf
                    <template x-for="userId in selectedUsers" :key="userId">
                        <input type="hidden" name="user_ids[]" :value="userId">
                    </template>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md shadow hover:bg-red-500">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M6 7h12M9 7V4h6v3m-8 4h10l-1 9H8l-1-9z"/>
                        </svg>
                        {{ __('Delete Selected') }}
                    </button>
                </form>
            </div>
        @endcan

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    @can('is-admin')
                        <th class="px-4 py-3 text-left w-12">
                            <input type="checkbox" x-model="selectAll"
                                   class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                        </th>
                    @endcan
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('User') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Role') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Created') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($users as $user)
                    <tr>
                        @can('is-admin')
                            <td class="px-4 py-3">
                                @if($user->id !== auth()->id())
                                    <input type="checkbox" value="{{ $user->id }}" x-model="selectedUsers"
                                           class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                @else
                                    <input type="checkbox" disabled
                                           class="rounded border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-600 cursor-not-allowed"
                                           title="{{ __('You cannot delete your own account') }}">
                                @endif
                            </td>
                        @endcan
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                            <div class="font-semibold">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ $user->role?->name ?? __('—') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            {{ $user->created_at?->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right space-x-3">
                            <a href="{{ route('users.edit', $user) }}"
                               class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-semibold">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M15.232 5.232 18.768 8.768M16.5 3.5l4 4L7 21H3v-4L16.5 3.5z"/>
                                </svg>
                                {{ __('Manage') }}
                            </a>
                            @can('is-admin')
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300"
                                                onclick="return confirm('{{ __('Are you sure you want to delete this user?') }}')">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                      d="M6 7h12M9 7V4h6v3m-8 4h10l-1 9H8l-1-9z"/>
                                            </svg>
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->can('is-admin') ? '5' : '4' }}" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No users found.') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 bg-gray-100 dark:bg-gray-700">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

