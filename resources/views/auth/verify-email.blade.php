<x-guest-layout>
    <!-- Header Block -->
    <div class="mb-6">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100 mb-2.5">
            Email Confirmation
        </div>
        <h2 class="font-display font-extrabold text-2xl sm:text-3xl uppercase tracking-tight text-brand-charcoal">
            Verify Email
        </h2>
        <p class="text-xs text-brand-charcoal/60 mt-1 font-normal leading-relaxed">
            {{ __('Thanks for creating an account with Jessi\'s ClassiQ Store! Before proceeding, please click on the verification link we dispatched to your email address.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-medium flex items-center gap-2.5 shadow-xs">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ __('A fresh verification link has been dispatched to your registered email.') }}</span>
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button class="group">
                <span>{{ __('Resend Verification Email') }}</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center pt-2 border-t border-black/5">
            @csrf

            <button
                type="submit"
                class="text-xs font-semibold text-brand-charcoal/60 hover:text-blue-600 underline underline-offset-4 transition-colors cursor-pointer"
            >
                {{ __('Log Out of Current Session') }}
            </button>
        </form>
    </div>
</x-guest-layout>
