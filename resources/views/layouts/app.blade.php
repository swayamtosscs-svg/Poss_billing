<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak]{display:none!important;}</style>
    </head>
    <body class="font-sans antialiased text-slate-900" 
          x-data="{ 
              darkMode: localStorage.getItem('darkMode') === 'true',
              init() {
                  if (this.darkMode) {
                      document.documentElement.classList.add('dark');
                  }
                  this.$watch('darkMode', value => {
                      localStorage.setItem('darkMode', value);
                      document.documentElement.classList.toggle('dark', value);
                  });
              }
          }">
        <div class="min-h-screen flex app-shell text-gray-900 dark:bg-gray-900">
            <!-- Sidebar -->
            <aside class="hidden md:flex md:w-72 vapar-sidebar md:h-screen md:sticky md:top-0 md:flex-col md:overflow-y-auto soft-scroll dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 flex items-center gap-3 border-b border-gray-100/70">
                    <div class="relative">
                        @if(!empty($appLogo))
                            <img src="{{ $appLogo }}" alt="{{ config('app.name', 'TPoss') }} logo" class="h-12 w-12 rounded-2xl object-cover shadow-lg border border-white/40">
                        @else
                            <span class="absolute -inset-1 rounded-full bg-gradient-to-br from-indigo-500/40 to-purple-500/25 blur-lg"></span>
                            <div class="relative h-12 w-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-semibold text-lg shadow-lg">
                                {{ strtoupper(substr(config('app.name', 'TPoss'), 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-slate-900 dark:text-white">{{ config('app.name', 'TPoss') }}</p>
                        <p class="text-sm text-slate-500 dark:text-gray-400">{{ __('Retail Control Hub') }}</p>
                    </div>
                </div>
                <nav class="px-5 py-6 space-y-6 flex-1">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ __('Overview') }}</p>
                        <div class="space-y-1.5">
                            <a href="{{ route('dashboard') }}" class="vapar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l9-9 9 9M5 10v10h4m6 0h4V10"/>
                                </svg>
                                {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('reports.index') }}" class="vapar-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h3v7H5zm6-5h3v12h-3zm6 6h3v6h-3z"/>
                                </svg>
                                {{ __('Reports') }}
                            </a>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ __('Sales') }}</p>
                        <div class="space-y-1.5">
                            <a href="{{ route('sales.index') }}" class="vapar-nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h3l3 12h10l2-7H8"/>
                                </svg>
                                {{ __('POS / Invoices') }}
                            </a>
                            <a href="{{ route('quotations.index') }}" class="vapar-nav-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 4h11v16H5V7z"/>
                                </svg>
                                {{ __('Quotations') }}
                            </a>
                            <a href="{{ route('customers.index') }}" class="vapar-nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-5 7h2a6 6 0 016 6H5a6 6 0 016-6z"/>
                                </svg>
                                {{ __('Customers') }}
                            </a>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ __('Operations') }}</p>
                        <div class="space-y-1.5">
                            <a href="{{ route('purchases.index') }}" class="vapar-nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l1-2h10l1 2m-12 0h12v13H6z"/>
                                </svg>
                                {{ __('Purchase Bills') }}
                            </a>
                            <a href="{{ route('expenses.index') }}" class="vapar-nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.5 0-3 .8-3 2s1.5 2 3 2 3 .8 3 2-1.5 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ __('Expenses') }}
                            </a>
                            <a href="{{ route('payments.index') }}" class="vapar-nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7h18M7 11h2m6 0h2M4 7v9a3 3 0 003 3h10a3 3 0 003-3V7"/>
                                </svg>
                                {{ __('Payments') }}
                            </a>
                            <a href="{{ route('bank-accounts.index') }}" class="vapar-nav-link {{ request()->routeIs('bank-accounts.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 10h16M7 14h1m4 0h1m-7 5h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                {{ __('Bank Accounts') }}
                            </a>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ __('Catalog') }}</p>
                        <div class="space-y-1.5">
                            <a href="{{ route('products.index') }}" class="vapar-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 12H4m16 0l-4 8H8l-4-8 4-8h8l4 8z"/>
                                </svg>
                                {{ __('Products') }}
                            </a>
                            <a href="{{ route('categories.index') }}" class="vapar-nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 4h10l3 5-8 11-8-11 3-5z"/>
                                </svg>
                                {{ __('Categories') }}
                            </a>
                            <a href="{{ route('stock-adjustments.index') }}" class="vapar-nav-link {{ request()->routeIs('stock-adjustments.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7v10c0 2.21 3.6 4 8 4s8-1.79 8-4V7c0-2.21-3.6-4-8-4s-8 1.79-8 4z"/>
                                </svg>
                                {{ __('Stock Adjust') }}
                            </a>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ __('People & Settings') }}</p>
                        <div class="space-y-1.5">
                            <a href="{{ route('users.index') }}" class="vapar-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 5a4 4 0 11-4 4 4 4 0 014-4zm0 8c4 0 7 2 7 4v3H5v-3c0-2 3-4 7-4z"/>
                                </svg>
                                {{ __('Users') }}
                            </a>
                            <a href="{{ route('settings.index') }}" class="vapar-nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15a3 3 0 100-6 3 3 0 000 6zm7.94-2.06a1 1 0 00.06-1.52l-1.73-1.42.25-2.2a1 1 0 00-1.15-1.1l-2.2.26-1.42-1.73a1 1 0 00-1.52-.06l-1.43 1.72-2.2-.25a1 1 0 00-1.1 1.15l.26 2.2-1.73 1.42a1 1 0 00-.06 1.52l1.72 1.43-.25 2.2a1 1 0 001.15 1.1l2.2-.26 1.42 1.73a1 1 0 001.52.06l1.43-1.72 2.2.25a1 1 0 001.1-1.15l-.26-2.2z"/>
                                </svg>
                                {{ __('Settings') }}
                            </a>
                        </div>
                    </div>
                </nav>
            </aside>

            <div class="flex-1 min-h-screen flex flex-col soft-scroll">
                <!-- Topbar -->
                <header class="vapar-topbar sticky top-0 z-30 dark:bg-gray-800 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="md:hidden">
                                @include('layouts.navigation')
                            </div>
                            <div>
                                <p class="vapar-heading text-xl sm:text-2xl dark:text-white">{{ __('Welcome back') }}, {{ auth()->user()->name ?? 'User' }}</p>
                                <p class="vapar-subtitle text-sm sm:text-base dark:text-gray-400">{{ __("Let's keep your business data in sync.") }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-4">
                            <!-- Dark Mode Toggle -->
                            <button @click="darkMode = !darkMode" 
                                    type="button"
                                    class="p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                    title="Toggle Dark Mode">
                                <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                <svg x-show="darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </button>
                            
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="flex items-center gap-3 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-3 py-1.5 shadow-sm hover:shadow transition">
                                    <img class="h-10 w-10 rounded-full object-cover border-2 border-white dark:border-gray-700 shadow"
                                         src="{{ auth()->user()->avatar ? asset(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name ?? 'U') }}"
                                         alt="avatar">
                                    <div class="hidden sm:flex flex-col text-left">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ auth()->user()->name ?? '' }}</span>
                                        <span class="text-xs text-slate-500 dark:text-gray-400">{{ auth()->user()->email ?? '' }}</span>
                                    </div>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-3 w-56 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 py-2 z-50">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-gray-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11a4 4 0 10-4-4 4 4 0 004 4zm0 2c-3.33 0-6 1.79-6 4v1h12v-1c0-2.21-2.67-4-6-4z"/></svg>
                                        {{ __('Profile') }}
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H9m4 4v1a3 3 0 01-3 3H7a3 3 0 01-3-3V7a3 3 0 013-3h3a3 3 0 013 3v1"/></svg>
                                            {{ __('Logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Heading -->
                @isset($header)
                    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-8">
                        <div class="vapar-card p-6 sm:p-8">
                            {{ $header }}
                        </div>
                    </section>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 w-full">
                    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if (session('success'))
                    window.notyf?.success(@js(session('success')));
                @endif
                @if (session('error'))
                    window.notyf?.error(@js(session('error')));
                @endif
                @if ($errors->any())
                    window.notyf?.error(@js(__('Please fix the highlighted errors and try again.')));
                @endif
            });
        </script>
    </body>
</html>
