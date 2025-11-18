<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-100 py-10 px-4">
        <div class="flex w-full max-w-4xl rounded-[32px] overflow-hidden shadow-2xl border border-slate-100 bg-white">
            <div class="hidden lg:flex w-5/12 flex-col justify-between bg-gradient-to-b from-slate-900 to-slate-800 text-white p-10">
                <div>
                    <p class="text-xs font-semibold tracking-[0.4em] text-white/70">{{ __('ACCOUNT RECOVERY') }}</p>
                    <h1 class="mt-6 text-3xl font-semibold leading-tight">{{ __('Reset your password in two quick steps') }}</h1>
                    <p class="mt-3 text-sm text-white/80">{{ __('Provide your registered email address. We will send you a secure link valid for 60 minutes.') }}</p>
                </div>
                <div class="space-y-4 text-sm text-white/80">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span>{{ __('Works for every user role (Admin, Manager, Cashier).') }}</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span>{{ __('Need help? support@TPoss.in or +91 90000 00000') }}</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span>{{ __('Add notifications@TPoss.in to your safe senders list.') }}</span>
                    </div>
                    <div class="text-sm text-white/70 pt-4 border-t border-white/10">
                        {{ __('Remember the password?') }}
                        <a href="{{ route('login') }}" class="font-semibold text-emerald-300 hover:text-emerald-200">{{ __('Return to login') }}</a>
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-7/12 p-8 md:p-12 space-y-8">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ __('Send reset link for') }}</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-900">{{ config('app.name', 'TPoss') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('We will email you instructions to set a new password.') }}</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email address')" />
                        <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="you@company.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <x-primary-button class="w-full justify-center py-3 text-base bg-slate-900 hover:bg-slate-800">
                        {{ __('Email password reset link') }}
                    </x-primary-button>
                </form>

                <p class="text-xs text-slate-400">{{ __('Did not receive the email? Check spam or request again after 2 minutes.') }}</p>
            </div>
        </div>
    </div>
</x-guest-layout>
