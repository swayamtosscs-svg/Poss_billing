<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('is-admin', fn(User $user) => optional($user->role)->name === 'Admin');
        Gate::define('is-manager', fn(User $user) => in_array(optional($user->role)->name, ['Admin', 'Manager'], true));
        Gate::define('is-cashier', fn(User $user) => in_array(optional($user->role)->name, ['Admin', 'Manager', 'Cashier'], true));
        Gate::define('permission', function (User $user, string $ability) {
            $perms = (array) (optional($user->role)->permissions ?? []);
            return in_array('*', $perms, true) || in_array($ability, $perms, true);
        });

        try {
            if (Schema::hasTable('roles') && Schema::hasTable('users')) {
            $defaultRoleId = Role::query()->firstWhere('name', 'Manager')
                ?->id ?? Role::query()->firstWhere('name', 'Admin')
                ?->id ?? Role::query()->min('id');

            if ($defaultRoleId) {
                User::query()
                    ->whereNull('role_id')
                    ->update(['role_id' => $defaultRoleId]);
            }
        }

        $locale = null;
        if (session()->has('app_locale')) {
            $locale = session('app_locale');
        } elseif (Schema::hasTable('settings')) {
            $locale = Setting::where('key', 'locale.default')->value('value');
        }

        if ($locale) {
            app()->setLocale($locale);
        }
        } catch (\Exception $e) {
            // Database not connected yet, skip initialization
        }

        view()->composer('*', function ($view) {
            $logo = null;
            try {
                if (Schema::hasTable('settings')) {
                    $logoPath = Setting::where('key', 'store.logo_path')->value('value');
                    if ($logoPath) {
                        $logoPath = Str::replace('\\', '/', $logoPath);
                        $normalized = Str::startsWith($logoPath, 'assets/') ? $logoPath : 'assets/' . ltrim($logoPath, '/');
                        $diskPath = Str::after($normalized, 'assets/');

                        if (file_exists(public_path($normalized)) || \Storage::disk('assets')->exists($diskPath)) {
                            $logo = asset($normalized);
                        } elseif (\Storage::disk('public')->exists($logoPath)) {
                            $logo = \Storage::disk('public')->url($logoPath);
                        } elseif (file_exists(public_path($logoPath))) {
                            $logo = asset($logoPath);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // ignore during first migrate
            }

            $view->with('appLogo', $logo);
        });
    }
}
