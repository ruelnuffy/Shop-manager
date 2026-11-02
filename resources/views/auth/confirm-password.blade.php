<x-guest-layout>
    <!-- Header Block -->
    <div class="mb-6">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-brand-pink-50 text-brand-pink border border-brand-pink-100 mb-2.5">
            Security Checkpoint
        </div>
        <h2 class="font-display font-extrabold text-2xl sm:text-3xl uppercase tracking-tight text-brand-charcoal">
            Confirm Password
        </h2>
        <p class="text-xs text-brand-charcoal/60 mt-1 font-normal leading-relaxed">
            This is a secure area of the boutique. Please confirm your current password to continue.
        </p>
    </div>

    <form
        method="POST"
        action="{{ route('password.confirm') }}"
        x-data="{ showPass: false }"
        class="space-y-4"
    >
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Current Password')" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-charcoal/40">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input
                    id="password"
                    x-bind:type="showPass ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                    class="w-full pl-10 pr-10 py-3 bg-white border border-black/15 focus:border-brand-pink focus:ring-4 focus:ring-brand-pink/15 rounded-xl shadow-xs text-brand-charcoal placeholder:text-brand-charcoal/30 text-sm transition duration-150"
                />
                <button
                    type="button"
                    @click="showPass = !showPass"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-brand-charcoal/40 hover:text-brand-charcoal transition-colors cursor-pointer"
                    tabindex="-1"
                    aria-label="Toggle password visibility"
                >
                    <svg x-show="!showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="group">
                <span>{{ __('Confirm & Continue') }}</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
