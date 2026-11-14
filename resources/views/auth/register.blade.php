@php
    $currentTenant = app(\App\Services\CurrentTenant::class)->get();
    $platformName = config('app.platform_name', 'ShopManager');
@endphp

<x-guest-layout>
    <!-- Header Block -->
    <div class="mb-6">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100 mb-2.5">
            {{ $currentTenant ? 'New Account Registration' : $platformName . ' Onboarding' }}
        </div>
        <h2 class="font-display font-extrabold text-2xl sm:text-3xl uppercase tracking-tight text-brand-charcoal">
            {{ $currentTenant ? 'Create Your Account' : 'Get Started with ' . $platformName }}
        </h2>
        <p class="text-xs text-brand-charcoal/60 mt-1 font-normal">
            {{ $currentTenant ? 'Join our store for order tracking, wishlist, and exclusive member privileges.' : 'Start managing your multi-location store inventory, sales, and automated payroll.' }}
        </p>
    </div>

    <form
        method="POST"
        action="{{ route('register') }}"
        x-data="{ showPass: false, showConfirm: false, storeName: '{{ old('store_name', '') }}', plan: '{{ request('plan', old('plan', 'standard')) }}' }"
        class="space-y-4"
    >
        @csrf

        @if (! $currentTenant)
            <!-- Store / Business Name (Platform Merchant Onboarding) -->
            <div>
                <x-input-label for="store_name" :value="__('Store / Business Name')" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-charcoal/40">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <x-text-input
                        id="store_name"
                        x-model="storeName"
                        class="block w-full pl-10"
                        type="text"
                        name="store_name"
                        :value="old('store_name')"
                        placeholder="e.g. Aura Retail & Co."
                        required
                        autofocus
                    />
                </div>
                <p x-show="storeName.trim().length > 0" class="text-[11px] text-brand-charcoal/55 mt-1.5 font-medium" style="display: none;">
                    Store URL slug: <span class="font-bold text-indigo-600" x-text="storeName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'your-store'"></span>
                </p>
                <x-input-error :messages="$errors->get('store_name')" class="mt-2" />
            </div>

            <!-- Starting Plan Selection -->
            <div>
                <x-input-label for="plan" :value="__('Select Starting Plan')" />
                <div class="grid grid-cols-2 gap-2 mt-1">
                    <label class="cursor-pointer border rounded-xl p-2.5 flex flex-col justify-between transition hover:border-indigo-400 bg-white" :class="plan === 'standard' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-200'">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">14-Day Free Trial</span>
                            <input type="radio" name="plan" value="standard" x-model="plan" class="text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <span class="text-[10px] text-slate-500 mt-1">Full access &bull; Free forever basic</span>
                    </label>

                    <label class="cursor-pointer border rounded-xl p-2.5 flex flex-col justify-between transition hover:border-indigo-400 bg-white" :class="plan === 'starter' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-200'">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">Starter Store</span>
                            <input type="radio" name="plan" value="starter" x-model="plan" class="text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <span class="text-[10px] text-emerald-700 font-semibold mt-1">₦8,000 / month</span>
                    </label>

                    <label class="cursor-pointer border rounded-xl p-2.5 flex flex-col justify-between transition hover:border-indigo-400 bg-white" :class="plan === 'pro' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-200'">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                <span class="text-xs font-bold text-slate-900">Pro Retailer</span>
                                <span class="text-[9px] bg-blue-100 text-blue-700 px-1 py-0.2 rounded font-extrabold uppercase">Popular</span>
                            </div>
                            <input type="radio" name="plan" value="pro" x-model="plan" class="text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <span class="text-[10px] text-blue-700 font-semibold mt-1">₦18,000 / month</span>
                    </label>

                    <label class="cursor-pointer border rounded-xl p-2.5 flex flex-col justify-between transition hover:border-indigo-400 bg-white" :class="plan === 'enterprise' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-200'">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">Enterprise Chain</span>
                            <input type="radio" name="plan" value="enterprise" x-model="plan" class="text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <span class="text-[10px] text-purple-700 font-semibold mt-1">₦45,000 / month</span>
                    </label>
                </div>
            </div>
        @endif

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-charcoal/40">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <x-text-input
                    id="name"
                    class="block w-full pl-10"
                    type="text"
                    name="name"
                    :value="old('name')"
                    placeholder="e.g. Alex Morgan"
                    required
                    :autofocus="(bool) $currentTenant"
                    autocomplete="name"
                />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
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
                    placeholder="e.g. alex@company.com"
                    required
                    autocomplete="username"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
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
                    autocomplete="new-password"
                    placeholder="At least 8 characters"
                    class="w-full pl-10 pr-10 py-3 bg-white border border-black/15 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/15 rounded-xl shadow-xs text-brand-charcoal placeholder:text-brand-charcoal/30 text-sm transition duration-150"
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

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-charcoal/40">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <input
                    id="password_confirmation"
                    x-bind:type="showConfirm ? 'text' : 'password'"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Re-enter your password"
                    class="w-full pl-10 pr-10 py-3 bg-white border border-black/15 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/15 rounded-xl shadow-xs text-brand-charcoal placeholder:text-brand-charcoal/30 text-sm transition duration-150"
                />
                <button
                    type="button"
                    @click="showConfirm = !showConfirm"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-brand-charcoal/40 hover:text-brand-charcoal transition-colors cursor-pointer"
                    tabindex="-1"
                    aria-label="Toggle password visibility"
                >
                    <svg x-show="!showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Perks Snippet -->
        <div class="p-3 rounded-2xl bg-brand-cream/80 border border-black/5 space-y-1.5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-brand-charcoal/60 block mb-1">
                {{ $currentTenant ? 'Your Member Privileges:' : 'Platform Capabilities Included:' }}
            </span>
            <div class="flex items-center gap-2 text-xs text-brand-charcoal/80">
                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span>{{ $currentTenant ? 'Personal order history and boutique privileges' : 'Multi-location physical inventory & catalog sync' }}</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-brand-charcoal/80">
                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span>{{ $currentTenant ? 'Live dispatch tracking & digital purchase receipts' : 'Automated staff payroll disbursement & OCR ledgers' }}</span>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <x-primary-button class="group">
                <span>{{ $currentTenant ? __('Create Store Account') : __('Launch Your Store') }}</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </x-primary-button>
        </div>

        <!-- Login Link -->
        <div class="pt-4 text-center border-t border-black/5">
            <p class="text-xs text-brand-charcoal/60">
                {{ $currentTenant ? __('Already have a boutique account?') : __('Already have a platform account?') }}
                <a
                    href="{{ route('login') }}"
                    class="font-bold text-brand-charcoal hover:text-blue-600 underline underline-offset-4 transition-colors ml-1"
                >
                    {{ __('Sign in here') }} &rarr;
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
