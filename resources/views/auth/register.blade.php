<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-100 py-10 px-4">
        <div class="flex w-full max-w-4xl rounded-[32px] overflow-hidden shadow-2xl border border-slate-100 bg-white">
            <div class="hidden lg:flex w-5/12 flex-col justify-between bg-gradient-to-b from-slate-900 to-slate-800 text-white p-10">
                <div>
                    <p class="text-xs font-semibold tracking-[0.4em] text-white/70">{{ __('CREATE ACCOUNT') }}</p>
                    <h1 class="mt-6 text-3xl font-semibold leading-tight">{{ __('Join the TPoss workspace in minutes') }}</h1>
                    <p class="mt-3 text-sm text-white/80">{{ __('Set up access to analytics, inventory and billing tools from anywhere.') }}</p>
                </div>
                <div class="space-y-4 text-sm text-white/80">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-white/80"></span>
                        <span>{{ __('Use a verified business email for faster approvals.') }}</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-white/80"></span>
                        <span>{{ __('Choose a strong password with numbers and symbols.') }}</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-white/80"></span>
                        <span>{{ __('Already invited? Sign in and we will sync your role automatically.') }}</span>
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-7/12 p-8 md:p-12 space-y-8">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ __('Start using') }}</p>
                        <h2 class="mt-2 text-3xl font-semibold text-slate-900">{{ config('app.name', 'TPoss') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('Tell us who you are to create a secure profile') }}</p>
                    </div>
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-900 hover:text-slate-700">
                        {{ __('Already registered?') }}
                    </a>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Full name')" />
                        <x-text-input id="name" class="mt-2 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="{{ __('e.g. Priya Sharma') }}" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email address')" />
                        <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@company.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm password')" />
                        <x-text-input id="password_confirmation" class="mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <x-primary-button class="w-full justify-center py-3 text-base bg-slate-900 hover:bg-slate-800">
                        {{ __('Create account') }}
                    </x-primary-button>
                </form>

                <p class="mt-4 text-xs text-slate-400">{{ __('By signing up you agree to our terms of service and privacy guidelines.') }}</p>
            </div>
        </div>
    </div>
</x-guest-layout>
