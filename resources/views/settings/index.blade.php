@php($logoPlaceholder = 'https://via.placeholder.com/64x64?text=Logo')
@php($displayLogo = old('remove_logo') ? null : $logoUrl)

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
            {{ __('Store Settings') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow rounded-lg p-6">
            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Store Name') }}</label>
                        <input name="store_name" value="{{ old('store_name', $settings['store.name'] ?? config('app.name')) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <x-input-error :messages="$errors->get('store_name')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tax ID') }}</label>
                        <input name="store_tax_id" value="{{ old('store_tax_id', $settings['store.tax_id'] ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <x-input-error :messages="$errors->get('store_tax_id')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Address') }}</label>
                        <textarea name="store_address" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('store_address', $settings['store.address'] ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('store_address')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Currency Code') }}</label>
                        <input name="currency" value="{{ old('currency', $settings['store.currency'] ?? 'INR') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tax Rate (%)') }}</label>
                        <input type="number" step="0.01" min="0" name="tax_rate" value="{{ old('tax_rate', $settings['tax.rate'] ?? 0) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <x-input-error :messages="$errors->get('tax_rate')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Store Logo') }}</label>
                    <div class="mt-3 flex flex-wrap items-center gap-4">
                        <img id="logoPreview"
                             src="{{ $displayLogo ?? $logoPlaceholder }}"
                             data-placeholder="{{ $logoPlaceholder }}"
                             alt="logo preview"
                             class="h-14 w-14 rounded-2xl border border-gray-200 dark:border-gray-700 object-cover bg-white dark:bg-gray-700 shadow-sm">
                        <div class="flex-1 space-y-2">
                            <input id="logoInput" type="file" name="logo" accept="image/*"
                                   class="block w-full text-sm text-gray-900 dark:text-white">
                            <div class="flex flex-wrap gap-2">
                                <button type="button"
                                        id="logoRemoveBtn"
                                        class="px-3 py-2 rounded-full border text-sm font-semibold text-rose-500 dark:text-rose-400 border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 transition disabled:opacity-40 disabled:cursor-not-allowed"
                                        @disabled(! $displayLogo)>
                                    {{ __('Remove Logo') }}
                                </button>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Upload PNG/JPG up to 2 MB. You can crop after selecting.') }}</p>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="logo_cropped" id="logo_cropped">
                    <input type="hidden" name="remove_logo" id="remove_logo" value="{{ old('remove_logo', '0') }}">
                    <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Payment Methods') }}</label>
                    @php($methods = old('payment_methods', json_decode($settings['payment.methods'] ?? '["cash","card","upi"]', true)))
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        @foreach(['cash','card','upi','netbanking','wallet'] as $method)
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="payment_methods[]" value="{{ $method }}" @checked(in_array($method, $methods ?? []))
                                       class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                {{ ucfirst($method) }}
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('payment_methods')" class="mt-2" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Default Language') }}</label>
                    <select name="locale"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="en" @selected(($settings['locale.default'] ?? 'en') === 'en')>English</option>
                        <option value="hi" @selected(($settings['locale.default'] ?? 'en') === 'hi')>हिन्दी (Hindi)</option>
                    </select>
                    <x-input-error :messages="$errors->get('locale')" class="mt-2" />
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                        {{ __('Save Settings') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Database Backups') }}</h3>
                <form method="POST" action="{{ route('settings.backup') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md shadow hover:bg-emerald-500">
                        {{ __('Run Backup Now') }}
                    </button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm bg-white dark:bg-gray-800">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('File') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Size') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Created') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($backupFiles as $file)
                        <tr>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $file['name'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ number_format($file['size'] / 1024 / 1024, 2) }} MB</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::createFromTimestamp($file['last_modified'])->diffForHumans() }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('settings.download', $file['name']) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">{{ __('Download') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">{{ __('No backups available yet.') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

<div id="logoCropperModal" class="fixed inset-0 bg-black/60 flex items-center justify-center hidden z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Crop Logo') }}</h3>
        <div class="aspect-square w-full overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <img id="logoCropperImage" src="" alt="Cropper" class="w-full h-full object-contain">
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" id="logoCropperCancel" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Cancel') }}</button>
            <button type="button" id="logoCropperApply" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-500">{{ __('Apply Crop') }}</button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" integrity="sha512-24kIR13KqsP+DqgJdPf7fVgvY6DxKxC7+l4IYGRBQajb8VjS9gAAvcZ0dSpo2QJKW1h22PTedHW8D0X1CQX9KA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" integrity="sha512-oj1xkkpFmSPrbr30YIOCwVDDDeWGPAHDq6cDefELwsnAOR5n8i/vHkMIkJAIpRf0ViJvASGJ3BjsqcqTMp7r6g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('logoInput');
    const croppedInput = document.getElementById('logo_cropped');
    const preview = document.getElementById('logoPreview');
    const modal = document.getElementById('logoCropperModal');
    const cropperImage = document.getElementById('logoCropperImage');
    const applyBtn = document.getElementById('logoCropperApply');
    const cancelBtn = document.getElementById('logoCropperCancel');
    const removeInput = document.getElementById('remove_logo');
    const removeBtn = document.getElementById('logoRemoveBtn');
    const placeholderSrc = preview.dataset.placeholder;
    let cropper = null;
    let objectUrl = null;

    const hideModal = () => {
        modal.classList.add('hidden');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }
    };

    fileInput.addEventListener('change', (event) => {
        removeInput.value = '0';
        removeBtn?.removeAttribute('disabled');
        const [file] = event.target.files;
        if (!file) {
            return;
        }
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
        }
        objectUrl = URL.createObjectURL(file);
        cropperImage.src = objectUrl;
        modal.classList.remove('hidden');
        setTimeout(() => {
            cropper?.destroy();
            cropper = new Cropper(cropperImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                background: false,
                guides: false,
            });
        }, 50);
    });

    applyBtn.addEventListener('click', () => {
        if (!cropper) {
            hideModal();
            return;
        }
        const canvas = cropper.getCroppedCanvas({ width: 256, height: 256, imageSmoothingQuality: 'high' });
        if (!canvas) {
            hideModal();
            return;
        }
        canvas.toBlob((blob) => {
            if (!blob) {
                hideModal();
                return;
            }
            const reader = new FileReader();
            reader.onloadend = () => {
                const dataUrl = reader.result;
                croppedInput.value = dataUrl;
                preview.src = dataUrl;
                fileInput.value = '';
                removeInput.value = '0';
                removeBtn?.removeAttribute('disabled');
                hideModal();
            };
            reader.readAsDataURL(blob);
        }, 'image/png');
    });

    cancelBtn.addEventListener('click', () => {
        hideModal();
        fileInput.value = '';
    });

    removeBtn?.addEventListener('click', () => {
        preview.src = placeholderSrc;
        croppedInput.value = '';
        fileInput.value = '';
        removeInput.value = '1';
        removeBtn.setAttribute('disabled', 'disabled');
        modal.classList.add('hidden');
    });

    if (removeInput.value === '1') {
        preview.src = placeholderSrc;
        removeBtn?.setAttribute('disabled', 'disabled');
    }
});
</script>

