<x-app-layout>
    {{-- Top App Bar / Header Slot --}}
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 sm:gap-3">
            {{-- User Greeting & Avatar --}}
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <div class="relative shrink-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-tr from-brand-charcoal to-[#2a1b24] p-0.5 shadow-md shadow-brand-pink/15">
                        <div class="w-full h-full rounded-full bg-white flex items-center justify-center text-brand-charcoal font-display font-bold text-xs sm:text-base uppercase tracking-wider">
                            {{ substr(Auth::user()->name, 0, 1) }}{{ substr(strstr(Auth::user()->name, ' ') ?: ' ', 1, 1) }}
                        </div>
                    </div>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-emerald-500 border-2 border-white shadow-xs"></span>
                </div>

                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="inline-flex items-center gap-1 px-1.5 sm:px-2 py-0.5 rounded-full text-[8px] sm:text-[9px] font-bold uppercase tracking-wider bg-black text-white">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                            <span>{{ Auth::user()->role === 'admin' ? 'Atelier Lead' : (Auth::user()->role === 'staff' ? 'Boutique Staff' : 'Client Privé') }}</span>
                        </span>
                        <span class="text-[9px] sm:text-[10px] text-brand-charcoal/50 font-mono hidden xs:inline">
                            #JC-{{ str_pad(Auth::user()->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <h1 class="font-display font-extrabold text-base sm:text-2xl text-brand-charcoal tracking-tight truncate mt-0.5">
                        {{ Auth::user()->name }}
                    </h1>
                </div>
            </div>

            {{-- Action Buttons & Portal Jump --}}
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-full border border-black/10 hover:border-black text-xs font-bold uppercase tracking-wider text-brand-charcoal hover:bg-black hover:text-white transition shadow-xs shrink-0"
                    title="Return to Storefront Homepage"
                >
                    <svg class="w-3.5 h-3.5 text-brand-pink shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="hidden sm:inline">Storefront</span>
                </a>

                @if (Auth::user()->isStaff())
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="hidden md:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-brand-pink text-white hover:bg-brand-pink-600 text-xs font-bold uppercase tracking-wider transition shadow-sm"
                    >
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                        <span>Management &rarr;</span>
                    </a>
                @endif

                <a
                    href="{{ route('cart.index') }}"
                    class="relative w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white hover:bg-black/5 border border-black/[0.08] flex items-center justify-center text-brand-charcoal transition"
                    title="Shopping Bag"
                >
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    @if(isset($cartCount) && $cartCount > 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-brand-pink text-white text-[9px] font-black flex items-center justify-center">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white hover:bg-black/5 border border-black/[0.08] flex items-center justify-center text-brand-charcoal transition"
                    title="Account Settings"
                >
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Main App View Container --}}
    <div x-data="{ currentTab: 'overview', showCardModal: false, showFundModal: false, fundAmount: 25000 }" class="py-4 sm:py-8 pb-28 sm:pb-16 max-w-6xl mx-auto px-3 sm:px-6 lg:px-8 space-y-5 sm:space-y-6 overflow-x-hidden">

        {{-- Session Flash Alerts --}}
        @if (session('success'))
            <div class="p-3.5 sm:p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 flex items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-3.5 sm:p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-900 flex items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs font-semibold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Staff/Admin Notice Bar (if logged in with staff privileges) --}}
        @if (Auth::user()->isStaff())
            <div class="p-3 sm:p-4 rounded-2xl bg-brand-charcoal text-white flex items-center justify-between gap-3 border border-black/10 shadow-md">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="flex items-center justify-center w-7 h-7 rounded-xl bg-brand-pink/20 text-brand-pink text-xs shrink-0">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                    </span>
                    <div class="min-w-0">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-pink-200 block truncate">Atelier Access Active</span>
                        <span class="text-[11px] text-white/60 truncate block">You can manage stock, process walk-in orders, and view analytics.</span>
                    </div>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-xl bg-brand-pink hover:bg-brand-pink-600 text-white font-bold text-[11px] uppercase tracking-wider shrink-0 transition">
                    Portal &rarr;
                </a>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- SECTION 1: LUXURY DIGITAL VIP MEMBERSHIP CARD               --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#121015] via-[#1a1722] to-[#2e1925] text-white p-4 sm:p-7 shadow-2xl border border-white/10">
            {{-- Ambient Shimmer Background Accent --}}
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand-pink/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-rose-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col justify-between min-h-[160px] sm:min-h-[190px]">
                {{-- Top Card Bar: Logo + Chip --}}
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Jessi's ClassiQ Store" class="h-5 sm:h-7 w-auto object-contain brightness-0 invert opacity-95">
                        <div class="h-3.5 w-px bg-white/20"></div>
                        <span class="text-[9px] uppercase tracking-[0.25em] font-bold text-white/60">Privé Club</span>
                    </div>

                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[10px] font-bold text-brand-pink-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-pink animate-pulse"></span>
                        <span>{{ $vipTier ?? 'VIP Privé' }}</span>
                    </div>
                </div>

                {{-- Center Card Details --}}
                <div class="my-2.5 sm:my-3">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
                        <div class="min-w-0">
                            <span class="text-[9px] font-mono uppercase tracking-widest text-white/40 block">Member Account</span>
                            <div class="font-display font-extrabold text-lg sm:text-2xl text-white tracking-wide uppercase truncate">
                                {{ Auth::user()->name }}
                            </div>
                        </div>

                        <div class="sm:text-right shrink-0">
                            <span class="text-[9px] font-mono uppercase tracking-widest text-white/40 block">Boutique Wallet Balance</span>
                            <div class="font-display font-black text-base sm:text-xl text-brand-pink tracking-tight">
                                ₦{{ number_format(Auth::user()->wallet_balance ?? 0, 0) }} <span class="text-[11px] sm:text-xs text-white/60 font-sans font-semibold">({{ number_format(floor((Auth::user()->wallet_balance ?? 0) / 10), 0) }} Pts)</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Card Bar: Card Number & Action Button --}}
                <div class="pt-2 border-t border-white/10 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-2 font-mono text-[10px] sm:text-xs text-white/60 tracking-wider">
                        <span>•••• •••• •••• {{ str_pad(Auth::user()->id, 4, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-white/30 hidden xs:inline">&bull;</span>
                        <span class="text-[10px] hidden xs:inline">Exp 12/28</span>
                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <button
                            type="button"
                            @click="showFundModal = true"
                            class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-1 rounded-full bg-brand-pink hover:bg-brand-pink-600 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm transition cursor-pointer"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Fund</span>
                        </button>

                        <button
                            type="button"
                            @click="showCardModal = true"
                            class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-1 rounded-full bg-white/10 hover:bg-white/20 text-white text-[10px] font-bold uppercase tracking-wider border border-white/15 transition cursor-pointer"
                        >
                            <span>Pass</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- SECTION 2: APP QUICK-ACTION ICON SHORTCUTS                  --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 sm:gap-3">
            {{-- Shortcut 1: My Orders --}}
            <button
                type="button"
                @click="currentTab = 'orders'"
                class="group flex flex-col items-center justify-center p-2.5 sm:p-3 rounded-2xl bg-white hover:bg-black/5 border border-black/[0.06] hover:border-black/20 text-center transition duration-150 shadow-xs cursor-pointer min-w-0"
            >
                <div class="relative w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-rose-50 group-hover:bg-brand-pink text-brand-pink group-hover:text-white flex items-center justify-center transition duration-150 mb-1.5 shadow-xs">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    @if($orders->count() > 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-brand-pink group-hover:bg-black text-white text-[9px] font-bold flex items-center justify-center">
                            {{ $orders->count() }}
                        </span>
                    @endif
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-brand-charcoal uppercase tracking-wider truncate w-full">My Orders</span>
                <span class="text-[8px] sm:text-[9px] text-brand-charcoal/45 font-medium truncate w-full">Tracking</span>
            </button>

            {{-- Shortcut 2: Shop Catalog --}}
            <a
                href="{{ route('products.index') }}"
                class="group flex flex-col items-center justify-center p-2.5 sm:p-3 rounded-2xl bg-white hover:bg-black/5 border border-black/[0.06] hover:border-black/20 text-center transition duration-150 shadow-xs min-w-0"
            >
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-amber-50 group-hover:bg-black text-amber-700 group-hover:text-white flex items-center justify-center transition duration-150 mb-1.5 shadow-xs">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-brand-charcoal uppercase tracking-wider truncate w-full">Catalog</span>
                <span class="text-[8px] sm:text-[9px] text-brand-charcoal/45 font-medium truncate w-full">New Drops</span>
            </a>

            {{-- Shortcut 3: Flagship Boutique --}}
            <a
                href="{{ route('store.location') }}"
                class="group flex flex-col items-center justify-center p-2.5 sm:p-3 rounded-2xl bg-white hover:bg-black/5 border border-black/[0.06] hover:border-black/20 text-center transition duration-150 shadow-xs min-w-0"
            >
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-blue-50 group-hover:bg-blue-600 text-blue-700 group-hover:text-white flex items-center justify-center transition duration-150 mb-1.5 shadow-xs">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-brand-charcoal uppercase tracking-wider truncate w-full">Jos Boutique</span>
                <span class="text-[8px] sm:text-[9px] text-brand-charcoal/45 font-medium truncate w-full">West Mines</span>
            </a>

            {{-- Shortcut 4: WhatsApp Support --}}
            <a
                href="https://wa.me/2349069498376?text=Hello%20Jessi's%20ClassiQ,%20I'm%20contacting%20you%20from%20my%20dashboard"
                target="_blank"
                class="group flex flex-col items-center justify-center p-2.5 sm:p-3 rounded-2xl bg-white hover:bg-black/5 border border-black/[0.06] hover:border-black/20 text-center transition duration-150 shadow-xs cursor-pointer min-w-0"
            >
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-emerald-50 group-hover:bg-emerald-600 text-emerald-600 group-hover:text-white flex items-center justify-center transition duration-150 mb-1.5 shadow-xs">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.301-.15-1.78-.879-2.056-.98-.276-.1-.476-.15-.677.15-.2.301-.777.98-.953 1.18-.175.201-.351.226-.652.075-.301-.15-1.272-.469-2.423-1.496-.896-.799-1.501-1.786-1.677-2.087-.175-.301-.019-.464.132-.614.136-.135.301-.351.451-.527.15-.175.201-.301.301-.502.1-.2.05-.376-.025-.526-.075-.15-.677-1.631-.928-2.238-.244-.59-.492-.51-.677-.52l-.577-.01c-.2 0-.526.075-.802.376s-1.053 1.028-1.053 2.507c0 1.48 1.078 2.908 1.229 3.109.15.2 2.121 3.238 5.138 4.542.718.31 1.278.496 1.715.635.722.23 1.378.197 1.898.12.579-.087 1.78-.727 2.03-1.43.251-.703.251-1.304.176-1.43-.076-.126-.276-.201-.577-.351zM12.04 2C6.527 2 2.05 6.477 2.05 11.99c0 1.82.49 3.528 1.344 5.003L2 22l5.143-1.348A9.94 9.94 0 0012.04 22c5.513 0 9.99-4.477 9.99-9.99A9.99 9.99 0 0012.04 2z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-brand-charcoal uppercase tracking-wider truncate w-full">WhatsApp</span>
                <span class="text-[8px] sm:text-[9px] text-emerald-600 font-semibold truncate w-full">+234 906</span>
            </a>

            {{-- Shortcut 5: Buy Now --}}
            <a
                href="{{ route('products.index') }}"
                class="group flex flex-col items-center justify-center p-2.5 sm:p-3 rounded-2xl bg-white hover:bg-black/5 border border-black/[0.06] hover:border-black/20 text-center transition duration-150 shadow-xs cursor-pointer min-w-0"
            >
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-brand-pink-50 group-hover:bg-brand-pink text-brand-pink group-hover:text-white flex items-center justify-center transition duration-150 mb-1.5 shadow-xs">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 13v4m-2-2h4" />
                    </svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-brand-charcoal uppercase tracking-wider truncate w-full">Buy Now</span>
                <span class="text-[8px] sm:text-[9px] text-brand-pink font-semibold truncate w-full">Instant</span>
            </a>

            {{-- Shortcut 6: Profile & Delivery Address --}}
            <a
                href="{{ route('profile.edit') }}"
                class="group flex flex-col items-center justify-center p-2.5 sm:p-3 rounded-2xl bg-white hover:bg-black/5 border border-black/[0.06] hover:border-black/20 text-center transition duration-150 shadow-xs min-w-0"
            >
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-neutral-100 group-hover:bg-neutral-800 text-neutral-700 group-hover:text-white flex items-center justify-center transition duration-150 mb-1.5 shadow-xs">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-brand-charcoal uppercase tracking-wider truncate w-full">Profile</span>
                <span class="text-[8px] sm:text-[9px] text-brand-charcoal/45 font-medium truncate w-full">Addresses</span>
            </a>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- SECTION 3: APP SEGMENTED TABS                               --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="p-1 rounded-2xl bg-[#ebebeb] flex items-center gap-1 text-[11px] sm:text-xs font-bold uppercase tracking-wider select-none overflow-x-auto scrollbar-none">
            <button
                type="button"
                @click="currentTab = 'overview'"
                :class="currentTab === 'overview' ? 'bg-white text-brand-charcoal shadow-sm' : 'text-brand-charcoal/60 hover:text-black'"
                class="shrink-0 sm:flex-1 py-2 px-3 sm:px-4 rounded-xl transition duration-150 text-center whitespace-nowrap cursor-pointer"
            >
                Overview
            </button>
            <button
                type="button"
                @click="currentTab = 'wallet'"
                :class="currentTab === 'wallet' ? 'bg-white text-brand-charcoal shadow-sm' : 'text-brand-charcoal/60 hover:text-black'"
                class="shrink-0 sm:flex-1 py-2 px-3 sm:px-4 rounded-xl transition duration-150 text-center whitespace-nowrap cursor-pointer flex items-center justify-center gap-1.5"
            >
                <span>Wallet</span>
                <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-brand-pink text-white font-bold">₦{{ number_format((Auth::user()->wallet_balance ?? 0) / 1000, 0) }}k</span>
            </button>
            <button
                type="button"
                @click="currentTab = 'orders'"
                :class="currentTab === 'orders' ? 'bg-white text-brand-charcoal shadow-sm' : 'text-brand-charcoal/60 hover:text-black'"
                class="shrink-0 sm:flex-1 py-2 px-3 sm:px-4 rounded-xl transition duration-150 text-center whitespace-nowrap cursor-pointer flex items-center justify-center gap-1.5"
            >
                <span>Orders</span>
                @if($orders->count() > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-brand-pink text-white font-bold">{{ $orders->count() }}</span>
                @endif
            </button>
            <button
                type="button"
                @click="currentTab = 'curations'"
                :class="currentTab === 'curations' ? 'bg-white text-brand-charcoal shadow-sm' : 'text-brand-charcoal/60 hover:text-black'"
                class="shrink-0 sm:flex-1 py-2 px-3 sm:px-4 rounded-xl transition duration-150 text-center whitespace-nowrap cursor-pointer"
            >
                Curations
            </button>
            <button
                type="button"
                @click="currentTab = 'concierge'"
                :class="currentTab === 'concierge' ? 'bg-white text-brand-charcoal shadow-sm' : 'text-brand-charcoal/60 hover:text-black'"
                class="shrink-0 sm:flex-1 py-2 px-3 sm:px-4 rounded-xl transition duration-150 text-center whitespace-nowrap cursor-pointer"
            >
                Atelier Perks
            </button>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- TAB CONTENT: OVERVIEW                                       --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div x-show="currentTab === 'overview'" class="space-y-6">

            {{-- 1. Live Order Tracker Card (if active order exists) --}}
            @if(isset($activeOrder) && $activeOrder)
                <div class="rounded-3xl bg-white p-5 sm:p-6 border border-black/[0.08] shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-black/[0.06]">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-brand-charcoal">Active Delivery Tracker</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold text-brand-pink uppercase tracking-widest px-2.5 py-1 rounded-full bg-brand-pink-50 border border-brand-pink-100">
                            {{ $activeOrder->order_number }}
                        </span>
                    </div>

                    {{-- Stepper Progress Flow --}}
                    <div>
                        <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-brand-charcoal mb-2">
                            <span class="{{ in_array($activeOrder->status, ['pending', 'processing', 'dispatched', 'delivered']) ? 'text-emerald-700' : 'text-brand-charcoal/40' }}">Confirmed</span>
                            <span class="{{ in_array($activeOrder->status, ['processing', 'dispatched', 'delivered']) ? 'text-emerald-700' : 'text-brand-charcoal/40' }}">Tailored</span>
                            <span class="{{ in_array($activeOrder->status, ['dispatched', 'delivered']) ? 'text-brand-pink font-extrabold' : 'text-brand-charcoal/40' }}">Dispatched</span>
                            <span class="{{ $activeOrder->status === 'delivered' ? 'text-emerald-700' : 'text-brand-charcoal/40' }}">Delivered</span>
                        </div>

                        {{-- Bar --}}
                        <div class="w-full h-2 rounded-full bg-black/[0.06] overflow-hidden flex">
                            <div
                                class="h-full bg-gradient-to-r from-emerald-500 to-brand-pink transition-all duration-500"
                                style="width: {{ $activeOrder->status === 'delivered' ? '100%' : ($activeOrder->status === 'dispatched' ? '75%' : ($activeOrder->status === 'processing' ? '50%' : '25%')) }};"
                            ></div>
                        </div>

                        <div class="flex items-center justify-between text-[10px] text-brand-charcoal/50 font-medium mt-2">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-brand-pink shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a2 2 0 11-4 0m4 0h3m4-3v3m-3 0a2 2 0 11-4 0m4 0h3a1 1 0 001-1v-4.586a1 1 0 00-.293-.707l-2.414-2.414A1 1 0 0016.586 9H13"/></svg>
                                <span>Plateau Express Logistics</span>
                            </span>
                            <span>Destination: {{ $activeOrder->shipping_city ?? 'Lagos' }}</span>
                        </div>
                    </div>

                    {{-- Order Items Preview Pill List --}}
                    @if($activeOrder->items->count() > 0)
                        <div class="p-3 rounded-2xl bg-[#fafafa] border border-black/[0.04] space-y-2">
                            <span class="text-[9px] uppercase tracking-wider font-bold text-brand-charcoal/50 block">Package Contents:</span>
                            @foreach($activeOrder->items as $item)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-brand-charcoal truncate">{{ $item->product_name }}</span>
                                    <span class="font-bold text-brand-charcoal shrink-0">₦{{ number_format($item->subtotal, 0) }}</span>
                                </div>
                            @endforeach
                            <div class="pt-2 border-t border-black/[0.05] flex items-center justify-between text-xs font-bold text-brand-charcoal">
                                <span>Total Paid:</span>
                                <span class="text-brand-pink">₦{{ number_format($activeOrder->total_amount, 0) }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-1">
                        <button
                            type="button"
                            @click="currentTab = 'orders'"
                            class="text-xs font-bold text-brand-charcoal hover:text-brand-pink uppercase tracking-wider transition"
                        >
                            View Order Details &rarr;
                        </button>
                        <span class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Verified Payment</span>
                        </span>
                    </div>
                </div>
            @endif

            {{-- 2. Responsive 4 Metrics Row (Properly Proportioned, No Broken Overflow) --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white p-4 rounded-2xl border border-black/[0.06] shadow-xs">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-brand-charcoal/50 block mb-1">Orders Placed</span>
                    <div class="font-display font-black text-2xl text-brand-charcoal">
                        {{ $orders->count() }}
                    </div>
                    <span class="text-[10px] text-brand-charcoal/50 block mt-0.5">Live tracking active</span>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-black/[0.06] shadow-xs">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-brand-charcoal/50 block mb-1">Membership</span>
                    <div class="font-display font-black text-sm sm:text-base text-brand-charcoal uppercase truncate">
                        {{ Auth::user()->role === 'admin' ? 'Atelier Lead' : 'Client Privé' }}
                    </div>
                    <span class="text-[10px] text-emerald-600 font-semibold block mt-0.5">&bull; Priority Access</span>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-black/[0.06] shadow-xs">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-brand-charcoal/50 block mb-1">Preferred Boutique</span>
                    <div class="font-display font-black text-sm sm:text-base text-brand-charcoal uppercase truncate">
                        Jos Boutique
                    </div>
                    <span class="text-[10px] text-brand-charcoal/50 block mt-0.5">West of Mines</span>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-black/[0.06] shadow-xs">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-brand-charcoal/50 block mb-1">Payment Security</span>
                    <div class="font-display font-black text-sm sm:text-base text-brand-charcoal uppercase truncate">
                        Encrypted
                    </div>
                    <span class="text-[10px] text-emerald-600 font-semibold block mt-0.5">&bull; 256-Bit SSL</span>
                </div>
            </div>

            {{-- 3. Curated SS-2026 Season Experience Banner --}}
            <div class="relative overflow-hidden rounded-3xl bg-black text-white p-6 sm:p-8 shadow-xl border border-black/10">
                <div class="absolute right-0 top-0 bottom-0 w-1/2 bg-gradient-to-l from-brand-pink/30 via-brand-pink/10 to-transparent pointer-events-none"></div>
                <div class="relative z-10 max-w-lg">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider bg-white/10 text-brand-pink-200 border border-white/15 mb-3">
                        <svg class="w-3.5 h-3.5 text-brand-pink shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L9.5 9.5 2 12l7.5 2.5L12 22l2.5-7.5L22 12l-7.5-2.5z"/></svg>
                        <span>Season SS / 2026 Drop</span>
                    </div>
                    <h2 class="font-display font-black text-2xl sm:text-3xl uppercase tracking-tight text-white mb-2">
                        The ClassiQ Experience
                    </h2>
                    <p class="text-xs text-white/70 leading-relaxed mb-5 font-normal">
                        Explore hand-cut silk evening gowns, Italian crocodile skin tote sets, and blush stiletto heels crafted with timeless elegance.
                    </p>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-full bg-brand-pink hover:bg-brand-pink-600 text-white font-bold text-xs uppercase tracking-wider transition shadow-md shadow-brand-pink/25">
                            Shop New Pieces
                        </a>
                        <a href="{{ route('profile.edit') }}" class="px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider border border-white/20 transition">
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            {{-- 4. Mini Curated Drops Preview --}}
            @if(isset($curatedProducts) && $curatedProducts->count() > 0)
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display font-bold text-sm uppercase tracking-wider text-brand-charcoal">Curated For You</h3>
                        <a href="{{ route('products.index') }}" class="text-xs font-bold text-brand-pink hover:underline uppercase tracking-wider">
                            View All ({{ $curatedProducts->count() }}) &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($curatedProducts->take(4) as $product)
                            <a
                                href="{{ route('products.show', $product->slug) }}"
                                class="group bg-white rounded-2xl p-2.5 border border-black/[0.06] hover:border-black/20 transition duration-150 flex flex-col justify-between shadow-xs"
                            >
                                <div class="aspect-square w-full rounded-xl overflow-hidden bg-neutral-100 mb-2 relative">
                                    @if($product->featured_image)
                                        <img src="{{ asset($product->featured_image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-brand-charcoal/30 text-xs">No image</div>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-brand-pink block truncate">
                                        {{ $product->category->name ?? 'Boutique' }}
                                    </span>
                                    <h4 class="text-xs font-bold text-brand-charcoal truncate mt-0.5 group-hover:text-brand-pink transition">
                                        {{ $product->name }}
                                    </h4>
                                    <div class="flex items-center justify-between mt-1.5 pt-1.5 border-t border-black/[0.04]">
                                        <div class="font-display font-black text-xs text-brand-charcoal">
                                            ₦{{ number_format($product->base_price, 0) }}
                                        </div>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-brand-charcoal group-hover:bg-brand-pink text-white text-[9px] font-bold uppercase tracking-wider transition">
                                            <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            <span>Buy</span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- TAB CONTENT: WALLET & STORE CREDIT                          --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div x-show="currentTab === 'wallet'" class="space-y-5" style="display: none;">
            {{-- Big Balance & Actions Hero Card --}}
            <div class="bg-white rounded-3xl p-4 sm:p-6 border border-black/[0.06] shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-brand-charcoal/50 block">Available Boutique Wallet Balance</span>
                    <div class="font-display font-black text-2xl sm:text-4xl text-brand-charcoal tracking-tight flex items-baseline gap-2 flex-wrap">
                        <span>₦{{ number_format(Auth::user()->wallet_balance ?? 0, 0) }}</span>
                        <span class="text-xs sm:text-sm font-sans font-semibold text-brand-pink">({{ number_format(floor((Auth::user()->wallet_balance ?? 0) / 10), 0) }} Loyalty Points)</span>
                    </div>
                    <p class="text-xs text-brand-charcoal/50">
                        Use this balance directly at checkout to purchase dresses, shoes, handbags, and accessories with zero card friction.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 shrink-0">
                    <button
                        type="button"
                        @click="showFundModal = true"
                        class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full bg-brand-pink hover:bg-brand-pink-600 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm flex items-center gap-1.5 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Fund Wallet</span>
                    </button>
                    <a
                        href="{{ route('products.index') }}"
                        class="px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-full bg-black text-white hover:bg-neutral-800 font-bold text-xs uppercase tracking-wider transition"
                    >
                        Spend Balance &rarr;
                    </a>
                </div>
            </div>

            {{-- 3 Features Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="p-4 rounded-2xl bg-white border border-black/[0.06] shadow-xs space-y-1">
                    <div class="w-8 h-8 rounded-xl bg-brand-pink-50 text-brand-pink flex items-center justify-center mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h4 class="text-xs font-bold text-brand-charcoal uppercase tracking-wider">1-Click Checkout</h4>
                    <p class="text-[11px] text-brand-charcoal/60 leading-relaxed">
                        Skip typing card details during flash sales. Your wallet balance pays instantly.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-black/[0.06] shadow-xs space-y-1">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h4 class="text-xs font-bold text-brand-charcoal uppercase tracking-wider">Secure & Protected</h4>
                    <p class="text-[11px] text-brand-charcoal/60 leading-relaxed">
                        Funds are safely stored under your encrypted profile with real-time audit ledger tracking.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-black/[0.06] shadow-xs space-y-1">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center mb-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/></svg>
                    </div>
                    <h4 class="text-xs font-bold text-brand-charcoal uppercase tracking-wider">Loyalty Perks</h4>
                    <p class="text-[11px] text-brand-charcoal/60 leading-relaxed">
                        Earn 1 Privé loyalty point for every ₦10 funded. Redeemable on seasonal trunk shows.
                    </p>
                </div>
            </div>

            {{-- Transaction History Ledger --}}
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-black/[0.06] shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-black/[0.06]">
                    <div>
                        <h4 class="font-display font-extrabold text-sm uppercase tracking-tight text-brand-charcoal">Transaction Statement</h4>
                        <p class="text-[11px] text-brand-charcoal/50">Recent deposits, boutique purchases, and adjustments</p>
                    </div>
                    <span class="text-[10px] font-mono uppercase tracking-wider text-brand-charcoal/40">
                        {{ $walletTransactions->count() }} Records
                    </span>
                </div>

                @if($walletTransactions->count() > 0)
                    <div class="divide-y divide-black/[0.04]">
                        @foreach($walletTransactions as $tx)
                            <div class="py-3 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ in_array($tx->type, ['deposit', 'credit']) ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                        @if(in_array($tx->type, ['deposit', 'credit']))
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-brand-charcoal truncate">{{ $tx->description }}</div>
                                        <div class="text-[10px] text-brand-charcoal/40 flex items-center gap-2 mt-0.5">
                                            <span>{{ $tx->created_at->format('M d, Y • h:i A') }}</span>
                                            @if($tx->reference)
                                                <span>&bull;</span>
                                                <span class="font-mono">{{ $tx->reference }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <div class="font-mono font-extrabold text-sm {{ in_array($tx->type, ['deposit', 'credit']) ? 'text-emerald-600' : 'text-brand-charcoal' }}">
                                        {{ in_array($tx->type, ['deposit', 'credit']) ? '+' : '-' }}₦{{ number_format($tx->amount, 0) }}
                                    </div>
                                    <span class="text-[10px] text-brand-charcoal/40 block mt-0.5">
                                        Bal: ₦{{ number_format($tx->balance_after, 0) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 text-center space-y-2">
                        <div class="w-10 h-10 rounded-2xl bg-neutral-100 flex items-center justify-center mx-auto text-brand-charcoal/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <p class="text-xs font-semibold text-brand-charcoal/60">No transaction records yet</p>
                        <p class="text-[11px] text-brand-charcoal/40 max-w-xs mx-auto">Top up your wallet to enable instant one-click boutique orders.</p>
                        <button
                            type="button"
                            @click="showFundModal = true"
                            class="inline-block mt-2 px-4 py-1.5 rounded-full bg-brand-charcoal hover:bg-brand-pink text-white text-[11px] font-bold uppercase tracking-wider transition cursor-pointer"
                        >
                            + Fund First ₦10,000
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- TAB CONTENT: ORDERS HISTORY & TRACKING                      --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div x-show="currentTab === 'orders'" class="space-y-4" style="display: none;">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-display font-black text-lg text-brand-charcoal uppercase tracking-tight">Order History</h3>
                    <p class="text-xs text-brand-charcoal/50">Track your boutique parcels and review digital receipts</p>
                </div>
                <a href="{{ route('products.index') }}" class="px-3.5 py-1.5 rounded-full bg-black text-white hover:bg-brand-pink text-xs font-bold uppercase tracking-wider transition">
                    + New Order
                </a>
            </div>

            @if($orders->count() > 0)
                <div class="space-y-3">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-3xl p-5 border border-black/[0.06] shadow-xs space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-black/[0.05]">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-xs text-brand-charcoal">{{ $order->order_number }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $order->status === 'delivered' ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'dispatched' ? 'bg-brand-pink-50 text-brand-pink' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $order->status }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-brand-charcoal/40 block mt-0.5">{{ $order->created_at->format('M d, Y • h:i A') }}</span>
                                </div>

                                <div class="sm:text-right">
                                    <div class="font-display font-black text-sm text-brand-charcoal">
                                        ₦{{ number_format($order->total_amount, 0) }}
                                    </div>
                                    <span class="text-[10px] text-emerald-600 font-semibold">{{ ucfirst($order->payment_status) }} via {{ ucfirst($order->payment_method) }}</span>
                                </div>
                            </div>

                            {{-- Items in this order --}}
                            <div class="space-y-1.5">
                                @foreach($order->items as $item)
                                    <div class="flex items-center justify-between text-xs py-1">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-pink shrink-0"></span>
                                            <span class="font-medium text-brand-charcoal truncate">{{ $item->product_name }}</span>
                                            @if($item->variant_description)
                                                <span class="text-[10px] text-brand-charcoal/50 shrink-0">({{ $item->variant_description }})</span>
                                            @endif
                                            <span class="text-[10px] text-brand-charcoal/40 shrink-0">&times;{{ $item->quantity }}</span>
                                        </div>
                                        <span class="font-mono text-brand-charcoal/70 shrink-0">₦{{ number_format($item->subtotal, 0) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Actions Footer --}}
                            <div class="pt-2 border-t border-black/[0.05] flex items-center justify-between text-xs">
                                <span class="text-[11px] text-brand-charcoal/50">Shipping to: {{ $order->shipping_city }}</span>
                                <a href="{{ route('order.confirmation', $order->order_number) }}" class="text-xs font-bold text-brand-pink hover:underline">
                                    View Receipt &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 rounded-3xl bg-white text-center border border-black/[0.06] shadow-xs space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-neutral-100 flex items-center justify-center mx-auto text-brand-charcoal/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <h4 class="font-display font-bold text-base text-brand-charcoal uppercase tracking-wider">No Orders Yet</h4>
                    <p class="text-xs text-brand-charcoal/60 max-w-sm mx-auto">
                        Your boutique wardrobe is waiting. Browse our handcrafted leather bags, gowns, and stilettos.
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-block px-5 py-2.5 rounded-full bg-brand-pink text-white font-bold text-xs uppercase tracking-wider transition shadow-sm">
                        Explore Collection
                    </a>
                </div>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- TAB CONTENT: CURATIONS                                      --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div x-show="currentTab === 'curations'" class="space-y-4" style="display: none;">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-display font-black text-lg text-brand-charcoal uppercase tracking-tight">Atelier Boutique Curations</h3>
                    <p class="text-xs text-brand-charcoal/50">Exclusive seasonal releases tailored for your VIP profile</p>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs font-bold text-brand-pink hover:underline uppercase tracking-wider">
                    Full Catalog &rarr;
                </a>
            </div>

            @if(isset($curatedProducts) && $curatedProducts->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5">
                    @foreach($curatedProducts as $product)
                        <div class="group bg-white rounded-3xl p-3 border border-black/[0.06] hover:border-black/20 transition duration-150 flex flex-col justify-between shadow-xs">
                            <div class="aspect-square w-full rounded-2xl overflow-hidden bg-neutral-100 mb-3 relative">
                                @if($product->featured_image)
                                    <img src="{{ asset($product->featured_image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-brand-charcoal/30 text-xs">No image</div>
                                @endif
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-black/70 backdrop-blur-md text-white text-[9px] font-bold uppercase tracking-wider">
                                    {{ $product->category->name ?? 'ClassiQ' }}
                                </span>
                            </div>

                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-brand-charcoal line-clamp-1 group-hover:text-brand-pink transition">
                                    {{ $product->name }}
                                </h4>
                                <div class="flex items-center justify-between pt-1">
                                    <span class="font-display font-black text-xs text-brand-charcoal">
                                        ₦{{ number_format($product->base_price, 0) }}
                                    </span>
                                    <a
                                        href="{{ route('products.show', $product->slug) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-brand-charcoal hover:bg-brand-pink text-white text-[10px] font-bold uppercase tracking-wider transition"
                                    >
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        <span>Buy Now</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- TAB CONTENT: ATELIER PERKS & CONCIERGE                      --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div x-show="currentTab === 'concierge'" class="space-y-4" style="display: none;">
            <div class="bg-white rounded-3xl p-6 border border-black/[0.06] shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-10 h-10 rounded-2xl bg-brand-pink-50 text-brand-pink shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L9.5 9.5 2 12l7.5 2.5L12 22l2.5-7.5L22 12l-7.5-2.5z"/></svg>
                    </span>
                    <div>
                        <h3 class="font-display font-extrabold text-base text-brand-charcoal uppercase tracking-tight">Client Privé Privileges</h3>
                        <p class="text-xs text-brand-charcoal/50">Your membership tier benefits at Jessi's ClassiQ Boutique</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div class="p-3.5 rounded-2xl bg-[#fafafa] border border-black/[0.04]">
                        <span class="text-xs font-bold text-brand-charcoal flex items-center gap-1.5 mb-1">
                            <svg class="w-4 h-4 text-brand-pink shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Complimentary Gift Packaging</span>
                        </span>
                        <p class="text-[11px] text-brand-charcoal/60 leading-relaxed">
                            Every piece is carefully packaged in our signature matte black and blush gift boxes with hand-tied satin ribbons.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-[#fafafa] border border-black/[0.04]">
                        <span class="text-xs font-bold text-brand-charcoal flex items-center gap-1.5 mb-1">
                            <svg class="w-4 h-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd"/></svg>
                            <span>First-Access Releases</span>
                        </span>
                        <p class="text-[11px] text-brand-charcoal/60 leading-relaxed">
                            Receive notifications 24 hours before limited silk gowns and Italian leather bag drops are released to the public.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-[#fafafa] border border-black/[0.04]">
                        <span class="text-xs font-bold text-brand-charcoal flex items-center gap-1.5 mb-1">
                            <svg class="w-4 h-4 text-brand-pink shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a2 2 0 11-4 0m4 0h3m4-3v3m-3 0a2 2 0 11-4 0m4 0h3a1 1 0 001-1v-4.586a1 1 0 00-.293-.707l-2.414-2.414A1 1 0 0016.586 9H13"/></svg>
                            <span>Priority Delivery</span>
                        </span>
                        <p class="text-[11px] text-brand-charcoal/60 leading-relaxed">
                            Express insured dispatch across Jos, Abuja, and Lagos with tamper-evident boutique ribbon packaging.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-[#fafafa] border border-black/[0.04]">
                        <span class="text-xs font-bold text-brand-charcoal flex items-center gap-1.5 mb-1">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>Direct VIP Concierge</span>
                        </span>
                        <p class="text-[11px] text-brand-charcoal/60 leading-relaxed">
                            Direct WhatsApp line with our boutique team for custom size requests, gift wrapping, and reservations.
                        </p>
                    </div>
                </div>

                <div class="pt-3 border-t border-black/[0.06] flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs text-brand-charcoal/60">
                        <strong>Flagship Location:</strong> Across Chrisbest Plaza, West of Mines, Jos, Plateau State
                    </div>
                    <a
                        href="https://wa.me/2349069498376?text=Hello%20Jessi's%20ClassiQ,%20I'm%20contacting%20you%20from%20my%20dashboard"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm"
                    >
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.301-.15-1.78-.879-2.056-.98-.276-.1-.476-.15-.677.15-.2.301-.777.98-.953 1.18-.175.201-.351.226-.652.075-.301-.15-1.272-.469-2.423-1.496-.896-.799-1.501-1.786-1.677-2.087-.175-.301-.019-.464.132-.614.136-.135.301-.351.451-.527.15-.175.201-.301.301-.502.1-.2.05-.376-.025-.526-.075-.15-.677-1.631-.928-2.238-.244-.59-.492-.51-.677-.52l-.577-.01c-.2 0-.526.075-.802.376s-1.053 1.028-1.053 2.507c0 1.48 1.078 2.908 1.229 3.109.15.2 2.121 3.238 5.138 4.542.718.31 1.278.496 1.715.635.722.23 1.378.197 1.898.12.579-.087 1.78-.727 2.03-1.43.251-.703.251-1.304.176-1.43-.076-.126-.276-.201-.577-.351zM12.04 2C6.527 2 2.05 6.477 2.05 11.99c0 1.82.49 3.528 1.344 5.003L2 22l5.143-1.348A9.94 9.94 0 0012.04 22c5.513 0 9.99-4.477 9.99-9.99A9.99 9.99 0 0012.04 2z"/></svg>
                        <span>WhatsApp Desk: +234 906 949 8376</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- FUND BOUTIQUE WALLET MODAL                                  --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div
            x-show="showFundModal"
            x-transition
            @click.outside="showFundModal = false"
            class="fixed inset-0 z-[100000] bg-black/70 backdrop-blur-sm flex items-center justify-center p-4"
            style="display: none;"
        >
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-black/[0.06]">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-brand-pink-50 flex items-center justify-center text-brand-pink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-display font-extrabold text-sm uppercase tracking-tight text-brand-charcoal">Top Up Boutique Wallet</h3>
                            <p class="text-[10px] text-brand-charcoal/50">Current Balance: ₦{{ number_format(Auth::user()->wallet_balance ?? 0, 0) }}</p>
                        </div>
                    </div>
                    <button type="button" @click="showFundModal = false" class="text-xs font-bold text-brand-charcoal/40 hover:text-black">✕</button>
                </div>

                {{-- Presets --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-charcoal/60 mb-2">Select Amount</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach([10000, 25000, 50000, 100000] as $preset)
                            <button
                                type="button"
                                @click="fundAmount = {{ $preset }}"
                                :class="fundAmount == {{ $preset }} ? 'bg-black text-white border-black ring-2 ring-black/20' : 'bg-[#fafafa] text-brand-charcoal border-black/10 hover:border-black/30'"
                                class="py-2.5 px-2 rounded-xl border text-xs font-bold font-mono transition text-center cursor-pointer"
                            >
                                ₦{{ number_format($preset / 1000) }}k
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Custom Amount Input Form --}}
                <form method="POST" action="{{ route('wallet.fund') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-charcoal/60 mb-1">Custom Amount (NGN)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-sm text-brand-charcoal/50">₦</span>
                            <input
                                type="number"
                                name="amount"
                                x-model.number="fundAmount"
                                min="500"
                                step="500"
                                required
                                class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-black/15 focus:border-brand-pink focus:ring-2 focus:ring-brand-pink/20 text-sm font-bold text-brand-charcoal"
                                placeholder="Enter amount..."
                            >
                        </div>
                        <span class="text-[10px] text-brand-charcoal/40 mt-1 block">Minimum deposit is ₦500. Balance never expires.</span>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-3 rounded-full bg-brand-pink hover:bg-brand-pink-600 text-white text-xs font-bold uppercase tracking-wider transition shadow-md shadow-brand-pink/25 flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Pay ₦<span x-text="Number(fundAmount || 0).toLocaleString()"></span> with Paystack</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- IN-STORE SCAN MODAL (Apple Wallet barcode simulation)        --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div
            x-show="showCardModal"
            x-transition
            @click.outside="showCardModal = false"
            class="fixed inset-0 z-[100000] bg-black/70 backdrop-blur-sm flex items-center justify-center p-4"
            style="display: none;"
        >
            <div class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl text-center space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-black/[0.06]">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-charcoal">Jessi's ClassiQ Boutique Pass</span>
                    <button type="button" @click="showCardModal = false" class="text-xs font-bold text-brand-charcoal/40 hover:text-black">✕</button>
                </div>

                <div class="p-4 rounded-2xl bg-neutral-900 text-white space-y-2">
                    <span class="text-[10px] uppercase tracking-widest text-white/50 block">Digital Member ID</span>
                    <div class="font-display font-extrabold text-xl text-white">{{ Auth::user()->name }}</div>
                    <span class="font-mono text-xs text-brand-pink font-bold">#JC-{{ str_pad(Auth::user()->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>

                {{-- Stylized Barcode Graphic --}}
                <div class="py-3 px-4 rounded-xl bg-neutral-50 border border-black/10 flex flex-col items-center justify-center">
                    <div class="flex items-center gap-1 h-16 w-full justify-center">
                        @foreach([3,1,4,2,1,5,2,4,1,3,2,5,1,4,2,3,1,5,3,2,4,1,5] as $barWidth)
                            <div class="h-full bg-black rounded-xs" style="width: {{ $barWidth * 2 }}px;"></div>
                        @endforeach
                    </div>
                    <span class="font-mono text-[10px] text-brand-charcoal/50 mt-2 tracking-widest">
                        * JC-000{{ Auth::user()->id }}-2026 *
                    </span>
                </div>

                <p class="text-[11px] text-brand-charcoal/60 leading-relaxed">
                    Present this screen to the cashier at our West of Mines, Jos boutique to earn double points and apply VIP privileges.
                </p>

                <button
                    type="button"
                    @click="showCardModal = false"
                    class="w-full py-2.5 rounded-full bg-black text-white text-xs font-bold uppercase tracking-wider hover:bg-brand-pink transition"
                >
                    Done
                </button>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- FIXED MOBILE BOTTOM NAVIGATION BAR (NATIVE APP FEEL)        --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <nav class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-xl border-t border-black/[0.08] px-3 py-2 flex items-center justify-around shadow-2xl">
        {{-- 1. Storefront --}}
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 text-brand-charcoal/50 hover:text-black transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[9px] font-bold uppercase tracking-wider">Home</span>
        </a>

        {{-- 2. Catalog --}}
        <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-0.5 text-brand-charcoal/50 hover:text-black transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="text-[9px] font-bold uppercase tracking-wider">Catalog</span>
        </a>

        {{-- 3. Shopping Bag --}}
        <a href="{{ route('cart.index') }}" class="relative flex flex-col items-center gap-0.5 text-brand-charcoal/50 hover:text-black transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            @if(isset($cartCount) && $cartCount > 0)
                <span class="absolute -top-1 right-2 w-3.5 h-3.5 rounded-full bg-brand-pink text-white text-[8px] font-black flex items-center justify-center">
                    {{ $cartCount }}
                </span>
            @endif
            <span class="text-[9px] font-bold uppercase tracking-wider">Bag</span>
        </a>

        {{-- 4. Active Dashboard (Highlighted) --}}
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-0.5 text-brand-pink transition font-bold">
            <div class="relative">
                <svg class="w-5 h-5 text-brand-pink" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-brand-pink"></span>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-wider text-brand-pink">Account</span>
        </a>
    </nav>
</x-app-layout>
