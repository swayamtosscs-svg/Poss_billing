<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    protected array $keys = [
        'store.name',
        'store.address',
        'store.tax_id',
        'store.logo_path',
        'tax.rate',
        'store.currency',
        'payment.methods',
        'locale.default',
    ];

    public function index(): View
    {
        $settings = Setting::whereIn('key', $this->keys)->pluck('value', 'key');
        $logoPath = $settings['store.logo_path'] ?? null;

        $logoUrl = null;
        if ($logoPath) {
            $logoPath = Str::replace('\\', '/', $logoPath);
            $normalizedPath = Str::startsWith($logoPath, 'assets/') ? $logoPath : 'assets/' . ltrim($logoPath, '/');
            $diskPath = Str::after($normalizedPath, 'assets/');

            if (file_exists(public_path($normalizedPath)) || Storage::disk('assets')->exists($diskPath)) {
                $logoUrl = asset($normalizedPath);
            } elseif (Storage::disk('public')->exists($logoPath)) {
                $logoUrl = Storage::disk('public')->url($logoPath);
            } elseif (file_exists(public_path($logoPath))) {
                $logoUrl = asset($logoPath);
            }
        }

        return view('settings.index', [
            'settings' => $settings,
            'logoUrl' => $logoUrl,
            'backupFiles' => $this->getBackups(),
        ]);
    }

    public function update(SettingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $currentLogo = Setting::where('key', 'store.logo_path')->value('value');
        $croppedData = $data['logo_cropped'] ?? null;
        $removeLogo = $request->boolean('remove_logo');
        unset($data['logo_cropped'], $data['remove_logo']);

        $logoPath = $currentLogo;

        if ($removeLogo) {
            $this->deleteLogo($currentLogo);
            $logoPath = null;
        } elseif ($croppedData) {
            if ($path = $this->storeCroppedLogo($croppedData, $currentLogo)) {
                $logoPath = $path;
            }
        } elseif ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $this->deleteLogo($currentLogo);
            $storedPath = $request->file('logo')->store('branding', 'assets');
            $storedPath = Str::replace('\\', '/', $storedPath);
            $logoPath = 'assets/' . ltrim($storedPath, '/');
        }

        $this->storeSetting('store.logo_path', $logoPath);

        $this->storeSetting('store.name', $data['store_name']);
        $this->storeSetting('store.address', $data['store_address'] ?? '');
        $this->storeSetting('store.tax_id', $data['store_tax_id'] ?? '');
        $this->storeSetting('store.currency', $data['currency']);
        $this->storeSetting('tax.rate', $data['tax_rate'] ?? 0);
        $this->storeSetting('payment.methods', json_encode($data['payment_methods']));
        $this->storeSetting('locale.default', $data['locale']);

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }

    public function runBackup(): RedirectResponse
    {
        Artisan::call('backup:run --only-db');

        return redirect()->route('settings.index')->with('success', 'Backup started. Check logs for progress.');
    }

    public function downloadBackup(string $file)
    {
        $path = 'laravel-backup/'.$file;
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path);
    }

    protected function storeSetting(string $key, mixed $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    protected function getBackups(): array
    {
        $disk = Storage::disk('local');
        if (! $disk->exists('laravel-backup')) {
            return [];
        }

        return collect($disk->files('laravel-backup'))
            ->filter(fn ($file) => str_ends_with($file, '.zip'))
            ->sortDesc()
            ->take(10)
            ->map(fn ($file) => [
                'name' => basename($file),
                'size' => $disk->size($file),
                'last_modified' => $disk->lastModified($file),
            ])
            ->values()
            ->all();
    }

    protected function deleteLogo(?string $path): void
    {
        if (! $path) {
            return;
        }

        $path = Str::replace('\\', '/', $path);
        $normalized = Str::startsWith($path, 'assets/') ? $path : 'assets/' . ltrim($path, '/');
        $diskPath = Str::after($normalized, 'assets/');

        if (Storage::disk('assets')->exists($diskPath)) {
            Storage::disk('assets')->delete($diskPath);
        }

        $assetPath = public_path($normalized);
        if (file_exists($assetPath)) {
            @unlink($assetPath);
        }

        $publicPath = public_path($path);
        if (file_exists($publicPath)) {
            @unlink($publicPath);
        }
    }

    protected function storeCroppedLogo(string $data, ?string $currentLogo = null): ?string
    {
        if (! str_starts_with($data, 'data:image')) {
            return null;
        }

        $parts = explode(',', $data, 2);
        if (count($parts) !== 2) {
            return null;
        }

        $decoded = base64_decode($parts[1], true);
        if ($decoded === false) {
            return null;
        }

        $filename = 'branding/' . Str::uuid()->toString() . '.png';
        if (! Storage::disk('assets')->exists('branding')) {
            Storage::disk('assets')->makeDirectory('branding');
        }

        Storage::disk('assets')->put($filename, $decoded);

        $relativePath = 'assets/' . ltrim($filename, '/');

        if ($currentLogo) {
            $this->deleteLogo($currentLogo);
        }

        return $relativePath;
    }
}

