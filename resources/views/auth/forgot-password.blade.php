<x-guest-layout>
    <!-- Header Block -->
    <div class="mb-6">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-brand-pink-50 text-brand-pink border border-brand-pink-100 mb-2.5">
            Account Recovery
        </div>
        <h2 class="font-display font-extrabold text-2xl sm:text-3xl uppercase tracking-tight text-brand-charcoal">
            Reset Password
        </h2>
        <p class="text-xs text-brand-charcoal/60 mt-1 font-normal leading-relaxed">
            Enter your registered email address below and we'll dispatch an encrypted reset link so you can choose a new password.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Registered Email')" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-charcoal/40">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                    </svg>
                </div>
                <x-text-input
                    id="email"
                    class="block w-full pl-10"
                    type="email"
                    name="email"
                    :value="old('email')"
                    placeholder="client@jessiclassiq.com"
                    required
                    autofocus
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="group">
                <span>{{ __('Email Password Reset Link') }}</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </x-primary-button>
        </div>

        <!-- Back to Login Link -->
        <div class="pt-4 text-center border-t border-black/5">
            <a
                href="{{ route('login') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-charcoal hover:text-brand-pink transition-colors"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>{{ __('Return to Sign In') }}</span>
            </a>
        </div>
    </form>
</x-guest-layout>
