<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-100 py-10 px-4">
        <div class="flex w-full max-w-4xl rounded-[32px] overflow-hidden shadow-2xl border border-slate-100 bg-white">
            <div class="hidden lg:flex w-5/12 flex-col justify-between bg-gradient-to-b from-slate-900 to-slate-800 text-white p-10">
                <div>
                    <p class="text-xs font-semibold tracking-[0.4em] text-white/70">{{ __('WELCOME BACK') }}</p>
                    <h1 class="mt-6 text-3xl font-semibold leading-tight">{{ __('Signin to manage your store performance') }}</h1>
                    <p class="mt-3 text-sm text-white/80">{{ __('Access invoices, dashboards, inventory, and customer records from anywhere securely.') }}</p>
                </div>
                <div class="space-y-4 text-sm text-white/80">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-white/80"></span>
                        <span>{{ __('Use your verified business email to continue.') }}</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-white/80"></span>
                        <span>{{ __('Allow only trusted devices to stay signed in.') }}</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-white/80"></span>
                        <span>{{ __('Reset your password anytime with the secure link.') }}</span>
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-7/12 p-8 md:p-12 space-y-8">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ __('Start using') }}</p>
                        <h2 class="mt-2 text-3xl font-semibold text-slate-900">{{ config('app.name', 'TPoss') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('Sign in with your business credentials') }}</p>
                    </div>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-sm font-semibold text-slate-900 hover:text-slate-700">
                            {{ __('Need an account?') }}
                        </a>
                    @endif
                </div>

                <x-auth-session-status class="text-sm text-center text-emerald-600" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email address')" />
                        <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@business.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div x-data="{ show: false }" class="space-y-2">
                        <div class="flex items-center justify-between">
                            <x-input-label for="password" :value="__('Password')" />
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-slate-900 hover:text-slate-700">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <x-text-input
                                id="password"
                                class="mt-2 block w-full pr-12"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                x-bind:type="show ? 'text' : 'password'"
                            />
                            <button type="button"
                                    class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600 text-sm"
                                    x-on:click="show = !show">
                                <span x-text="show ? '{{ __('Hide') }}' : '{{ __('Show') }}'"></span>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <x-primary-button class="w-full justify-center py-3 text-base bg-slate-900 hover:bg-slate-800">
                        {{ __('Log in') }}
                    </x-primary-button>
                </form>

                <p class="text-xs text-slate-400">{{ __('By signing in you agree to follow workspace security guidelines.') }}</p>
            </div>
        </div>
    </div>
</x-guest-layout>
