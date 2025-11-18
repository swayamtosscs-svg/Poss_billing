<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Categories') }}
            </h2>
            <button
                x-data
                @click="$dispatch('category-edit', null); $dispatch('open-modal', 'category-modal')"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                {{ __('Add Category') }}
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
            <form method="GET" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Search') }}</label>
                    <input type="text" name="search" value="{{ $search }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                           placeholder="{{ __('Category name') }}">
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('categories.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Products') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($categories as $category)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $category->description ?: '—' }}</td>
                        <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-200">{{ $category->products_count }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium">
                            <button
                                x-data
                                @click="$dispatch('category-edit', {{ \Illuminate\Support\Js::from(['id' => $category->id, 'name' => $category->name, 'description' => $category->description]) }}); $dispatch('open-modal', 'category-modal')"
                                class="text-blue-600 hover:text-blue-500 mr-3">
                                {{ __('Edit') }}
                            </button>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline"
                                  onsubmit="return confirm('{{ __('Delete this category?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-500">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No categories found.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <x-modal name="category-modal">
        <form method="POST" x-data="categoryForm()" :action="formAction" class="p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="isEditing ? '{{ __('Edit Category') }}' : '{{ __('Add Category') }}'"></h2>
            @csrf
            <template x-if="isEditing">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Name') }}</label>
                <input name="name" x-model="form.name" required
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Description') }}</label>
                <textarea name="description" rows="3" x-model="form.description"
                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button"
                        @click="$dispatch('close')"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md">
                    {{ __('Cancel') }}
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </x-modal>

    <script>
        function categoryForm() {
            return {
                isEditing: false,
                formAction: '{{ route('categories.store') }}',
                form: {
                    name: '',
                    description: '',
                },
                init() {
                    window.addEventListener('category-edit', (event) => {
                        if (event.detail?.id) {
                            this.isEditing = true;
                            this.formAction = '{{ route('categories.update', ':id') }}'.replace(':id', event.detail.id);
                            this.form.name = event.detail.name ?? '';
                            this.form.description = event.detail.description ?? '';
                        } else {
                            this.isEditing = false;
                            this.formAction = '{{ route('categories.store') }}';
                            this.form.name = '';
                            this.form.description = '';
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>

