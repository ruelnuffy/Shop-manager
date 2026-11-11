<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.platform_name', 'ShopManager') }} &mdash; Modern Retail POS &amp; Inventory Operating System</title>

        <!-- Google Fonts: Plus Jakarta Sans for UI & headlines -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            }
            .font-display {
                font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
                letter-spacing: -0.035em;
            }
            .bg-warm-sand {
                background-color: #f7f5f0;
            }
            [x-cloak] {
                display: none !important;
            }
        </style>
    </head>
    <body
        x-data="{
            billingCycle: 'monthly',
            openFaq: null,
            mobileNav: false,
            toggleFaq(idx) {
                this.openFaq = this.openFaq === idx ? null : idx;
            }
        }"
        class="bg-white text-slate-900 antialiased selection:bg-blue-600 selection:text-white"
    >

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- HERO & FLOATING NAVBAR WRAPPER (Full-Bleed Royal Blue)      -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div class="relative bg-gradient-to-br from-[#1d4ed8] via-[#2563eb] to-[#1e40af] text-white overflow-hidden">

            <!-- FLOATING iOS 27 DYNAMIC CAPSULE NAVBAR -->
            <header class="sticky top-2 sm:top-3 z-50 px-3 sm:px-6 max-w-7xl mx-auto pt-2 sm:pt-3">
                <div class="relative bg-white/90 backdrop-blur-2xl border border-white/60 shadow-xl shadow-blue-950/20 rounded-full px-3.5 sm:px-6 h-13 sm:h-15 flex items-center justify-between ring-1 ring-white/30 transition-all">

                <!-- Brand Logo (Uploaded Official Brand Mark) -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 group shrink-0">
                    <img
                        src="{{ asset('images/shopmanager-logo.png') }}"
                        alt="{{ config('app.platform_name', 'ShopManager') }}"
                        class="h-7 sm:h-8 md:h-9 w-auto object-contain max-w-[170px] sm:max-w-[210px] drop-shadow-xs group-hover:opacity-95 transition"
                    />
                    <span class="sr-only">{{ config('app.platform_name', 'ShopManager') }} Unified Retail, Inventory</span>
                </a>

                <!-- Desktop Navigation Links (iOS segmented style) -->
                <nav class="hidden lg:flex items-center gap-1 p-1 bg-slate-100/70 rounded-full border border-slate-200/50 text-xs font-semibold text-slate-600">
                    <a href="#features" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">Features</a>
                    <a href="#pos" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">Walk-in POS</a>
                    <a href="#pricing" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">Pricing</a>
                    <a href="#testimonials" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">Reviews</a>
                    <a href="#faq" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">FAQ</a>
                    <a href="#stores" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">Stores</a>
                </nav>

                <!-- Auth & Dynamic Action Cluster -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-[11px] sm:text-xs font-bold uppercase tracking-wider transition shadow-md shadow-blue-600/25 active:scale-95">
                            <span class="hidden sm:inline">Dashboard</span>
                            <span class="sm:hidden">Console</span>
                            <span>&rarr;</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-block px-3 py-1.5 text-xs font-bold text-slate-700 hover:text-blue-600 transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-[11px] sm:text-xs font-bold uppercase tracking-wider transition shadow-md shadow-blue-600/25 flex items-center gap-1 active:scale-95">
                            <span>Try Free</span>
                            <span class="hidden xs:inline">&rarr;</span>
                        </a>
                    @endauth

                    <!-- Mobile iOS Control Center Menu Toggle / Collapse Button -->
                    <button
                        type="button"
                        id="mobileNavToggleBtn"
                        onclick="toggleShopMobileMenu(event)"
                        aria-expanded="false"
                        aria-label="Toggle navigation menu"
                        class="lg:hidden w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 active:scale-90 flex items-center justify-center transition border border-slate-200 text-slate-700 cursor-pointer select-none"
                    >
                        <svg id="navBurgerIcon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 7h16M4 17h16" />
                        </svg>
                        <svg id="navCloseIcon" class="w-4 h-4 text-slate-900 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>

            <!-- iOS 27 Frosted Acrylic Control Center Sheet (Mobile Only) -->
            <div
                id="mobileNavSheet"
                class="hidden lg:hidden mt-2 p-3 sm:p-4 rounded-3xl bg-white/95 backdrop-blur-3xl border border-white/80 shadow-2xl shadow-slate-900/15 ring-1 ring-slate-900/10 space-y-2.5 transition-all text-slate-900"
            >
                <!-- Sheet Header with explicit Close / Done Button -->
                <div class="flex items-center justify-between pb-2 px-2 border-b border-slate-100">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Navigation</span>
                    <button
                        type="button"
                        onclick="toggleShopMobileMenu(event, false)"
                        class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer flex items-center gap-1 active:scale-95"
                    >
                        <span>Close</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Grouped Navigation Tiles -->
                <div class="grid grid-cols-1 gap-1">
                    <a
                        onclick="toggleShopMobileMenu(event, false)"
                        href="#features"
                        class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-100/80 active:bg-slate-200/70 transition group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-900">Features &amp; Hub</div>
                                <div class="text-[10px] text-slate-400">Unified retail, inventory &amp; POS</div>
                            </div>
                        </div>
                        <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
                    </a>

                    <a
                        onclick="toggleShopMobileMenu(event, false)"
                        href="#pos"
                        class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-100/80 active:bg-slate-200/70 transition group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-900">Walk-in POS Till</div>
                                <div class="text-[10px] text-slate-400">Fast checkout &amp; thermal slips</div>
                            </div>
                        </div>
                        <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
                    </a>

                    <a
                        onclick="toggleShopMobileMenu(event, false)"
                        href="#pricing"
                        class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-100/80 active:bg-slate-200/70 transition group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-900">Pricing &amp; Plans</div>
                                <div class="text-[10px] text-slate-400">14-day free trial, cancel anytime</div>
                            </div>
                        </div>
                        <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
                    </a>

                    <a
                        onclick="toggleShopMobileMenu(event, false)"
                        href="#testimonials"
                        class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-100/80 active:bg-slate-200/70 transition group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-900">Vendor Reviews</div>
                                <div class="text-[10px] text-slate-400">Stories from Nigerian retailers</div>
                            </div>
                        </div>
                        <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
                    </a>

                    <a
                        onclick="toggleShopMobileMenu(event, false)"
                        href="#faq"
                        class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-100/80 active:bg-slate-200/70 transition group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-900">FAQ &amp; Hardware</div>
                                <div class="text-[10px] text-slate-400">Bluetooth printer pairing &amp; setup</div>
                            </div>
                        </div>
                        <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
                    </a>

                    <a
                        onclick="toggleShopMobileMenu(event, false)"
                        href="#stores"
                        class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-100/80 active:bg-slate-200/70 transition group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-900">Live Boutiques</div>
                                <div class="text-[10px] text-slate-400">Explore active client shops</div>
                            </div>
                        </div>
                        <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
                    </a>
                </div>

                <!-- Mobile Action CTA Row -->
                <div class="pt-2 border-t border-slate-100/80 flex flex-col gap-2">
                    <a
                        href="{{ route('register') }}"
                        class="w-full py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-extrabold text-xs uppercase tracking-wider text-center transition shadow-lg shadow-blue-600/30 active:scale-95"
                    >
                        Start 14-Day Free Trial &rarr;
                    </a>
                    <a
                        href="{{ route('login') }}"
                        class="w-full py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs text-center transition active:scale-95"
                    >
                        Merchant Login
                    </a>
                </div>
            </div>
        </header>

        <script>
            function toggleShopMobileMenu(event, forceState) {
                if (event && event.stopPropagation) {
                    event.stopPropagation();
                }
                var sheet = document.getElementById('mobileNavSheet');
                var burger = document.getElementById('navBurgerIcon');
                var close = document.getElementById('navCloseIcon');
                var btn = document.getElementById('mobileNavToggleBtn');
                if (!sheet) return;

                var shouldOpen = forceState !== undefined ? forceState : sheet.classList.contains('hidden');

                if (shouldOpen) {
                    sheet.classList.remove('hidden');
                    if (burger) burger.classList.add('hidden');
                    if (close) close.classList.remove('hidden');
                    if (btn) btn.setAttribute('aria-expanded', 'true');
                } else {
                    sheet.classList.add('hidden');
                    if (burger) burger.classList.remove('hidden');
                    if (close) close.classList.add('hidden');
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                }
            }

            // Dismiss sheet when clicking or tapping outside on touchscreens
            document.addEventListener('click', function(e) {
                var sheet = document.getElementById('mobileNavSheet');
                var btn = document.getElementById('mobileNavToggleBtn');
                if (!sheet || sheet.classList.contains('hidden')) return;
                if (!sheet.contains(e.target) && !btn.contains(e.target)) {
                    toggleShopMobileMenu(null, false);
                }
            });

            // Dismiss sheet on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    toggleShopMobileMenu(null, false);
                }
            });
        </script>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 1: ROYAL BLUE HERO CONTENT (TIGHT & COMPACT)        -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-3 sm:pt-5 pb-6 sm:pb-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-8 items-center">

                <!-- Left: Hero Text & Call to Actions -->
                <div class="lg:col-span-6 space-y-4 sm:space-y-5 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-white text-[11px] font-bold tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>FOR RETAILERS &amp; COMMERCE BRANDS</span>
                    </div>

                    <h1 class="font-display font-extrabold text-3xl sm:text-5xl lg:text-5xl tracking-tight leading-[1.08]">
                        Sell anywhere.<br>
                        It all lands in<br>
                        one place.
                    </h1>

                    <p class="text-sm sm:text-base text-blue-100/95 max-w-lg mx-auto lg:mx-0 leading-relaxed font-normal">
                        {{ config('app.platform_name', 'ShopManager') }} is the operating system for modern retail brands. Unified Retail, Inventory, physical POS counter sales, wireless receipts, and WhatsApp orders &mdash; without ever losing track of inventory or revenue.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 pt-1">
                        <a
                            href="{{ route('register') }}"
                            class="w-full sm:w-auto px-7 py-3.5 rounded-full bg-white hover:bg-slate-50 text-blue-800 font-extrabold text-xs uppercase tracking-wider transition shadow-xl shadow-blue-950/20 flex items-center justify-center gap-2 active:scale-95"
                        >
                            <span>Start free trial</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>

                        <a
                            href="{{ route('admin.sales.create') }}"
                            class="w-full sm:w-auto px-6 py-3.5 rounded-full bg-blue-800/60 hover:bg-blue-800 text-white font-bold text-xs uppercase tracking-wider transition border border-white/20 flex items-center justify-center gap-2"
                        >
                            <span>Try Walk-in POS Till</span>
                            <span>&nearr;</span>
                        </a>
                    </div>

                    <!-- Micro Trust Badges -->
                    <div class="pt-1 flex flex-wrap items-center justify-center lg:justify-start gap-3 text-[11px] font-semibold text-blue-200">
                        <span>&check; No credit card required</span>
                        <span>&bull;</span>
                        <span>&check; 14-day free trial</span>
                        <span>&bull;</span>
                        <span>&check; Cancel anytime</span>
                    </div>
                </div>

                <!-- Right: High-Converting Retail Dashboard Composition -->
                <div class="lg:col-span-6 relative">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <!-- Background Ambient Aura -->
                        <div class="absolute -inset-3 bg-gradient-to-r from-blue-300 to-indigo-300 rounded-3xl blur-xl opacity-25"></div>

                        <!-- Main Floating App Window Card -->
                        <div class="relative bg-white rounded-3xl p-5 sm:p-6 shadow-2xl text-slate-900 space-y-4">
                            <!-- Top Window Chrome -->
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                    <span class="text-xs font-bold text-slate-400 ml-2 font-mono">shopmanager.console &bull; Live</span>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>POS Terminal Online</span>
                                </span>
                            </div>

                            <!-- Key Metrics Row -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 rounded-2xl bg-slate-50 space-y-1">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Today's Revenue</div>
                                    <div class="font-display font-extrabold text-xl sm:text-2xl text-slate-900">₦285,000</div>
                                    <div class="text-[10px] text-emerald-600 font-bold">+18.4% vs yesterday</div>
                                </div>
                                <div class="p-3 rounded-2xl bg-slate-50 space-y-1">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stock Valuation</div>
                                    <div class="font-display font-extrabold text-xl sm:text-2xl text-slate-900">₦4.2M</div>
                                    <div class="text-[10px] text-slate-400 font-semibold">16 categories synced</div>
                                </div>
                            </div>

                            <!-- Recent Live Walk-in Transaction Pill (with real product image) -->
                            <div class="p-3.5 rounded-2xl bg-slate-900 text-white flex items-center justify-between gap-3 shadow-md">
                                <div class="flex items-center gap-3">
                                    <img
                                        src="{{ asset('images/products/versse-snakeskin-accent-bag.png') }}"
                                        alt="Snake-Accent Leather Bag"
                                        class="w-10 h-10 rounded-xl object-cover bg-slate-800 border border-slate-700 shrink-0 shadow-xs"
                                    />
                                    <div>
                                        <div class="font-bold text-xs">Snake-Accent Leather Bag &bull; Set</div>
                                        <div class="text-[10px] text-slate-400">Cashier: Blessing &bull; Paid via SPLIT</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-display font-bold text-xs sm:text-sm text-emerald-400">₦45,000</div>
                                    <div class="text-[9px] text-slate-400">Receipt Printed &check;</div>
                                </div>
                            </div>

                            <!-- Floating Merchant WhatsApp Notification Bubble -->
                            <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-between text-xs text-emerald-950">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/></svg>
                                    <span class="text-[11px]">Customer receipt sent via WhatsApp</span>
                                </div>
                                <span class="text-emerald-700 font-bold text-[10px]">Delivered</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Trusted Retail Partners / Logos Row (Myshoplet Reference Style) -->
            <div class="mt-6 pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <span class="text-[11px] font-semibold text-blue-200 uppercase tracking-wider">
                    Trusted by retail brands &amp; connected with:
                </span>
                <div class="flex flex-wrap items-center justify-center gap-2.5 text-xs font-bold text-white/90">
                    <span class="px-3 py-1 rounded-full bg-white/10 backdrop-blur-xs border border-white/10">Paystack</span>
                    <span class="px-3 py-1 rounded-full bg-white/10 backdrop-blur-xs border border-white/10">Moniepoint</span>
                    <span class="px-3 py-1 rounded-full bg-white/10 backdrop-blur-xs border border-white/10">OPay</span>
                    <span class="px-3 py-1 rounded-full bg-white/10 backdrop-blur-xs border border-white/10">Flutterwave</span>
                    <span class="px-3 py-1 rounded-full bg-white/10 backdrop-blur-xs border border-white/10">58mm Thermal</span>
                </div>
            </div>

        </div>

        <!-- Crisp Clean Wave Bottom Divider -->
        <div class="w-full overflow-hidden leading-none pointer-events-none mt-1">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-6 sm:h-9 fill-white">
                <path d="M0,0 C150,90 350,-40 500,45 C650,130 900,10 1200,60 L1200,120 L0,120 Z"></path>
            </svg>
        </div>
    </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 2: "ONE SYSTEM, NOT EIGHT APPS" (Hub & Satellites)  -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section id="features" class="pt-8 sm:pt-12 pb-14 sm:pb-18 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-12">
                <!-- Section Header -->
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Unified Architecture</span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-950 tracking-tight">
                        One system, not eight apps
                    </h2>
                    <p class="text-sm sm:text-base text-slate-500 leading-relaxed">
                        Stop juggling WhatsApp messages, physical paper ledgers, separate card swipe machines, and complex spreadsheets. {{ config('app.platform_name', 'ShopManager') }} connects your entire retail ecosystem into one unified workspace.
                    </p>
                </div>

                <!-- Interactive Orbit Satellite Grid Layout -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 pt-4">

                    <!-- Satellite 1: Walk-In POS -->
                    <div class="p-6 rounded-3xl bg-slate-50/80 hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 transition duration-200 space-y-3 text-center border border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900">Walk-in POS Till</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Fast barcode &amp; tap checkout on any device.</p>
                    </div>

                    <!-- Satellite 2: Wireless Receipts -->
                    <div class="p-6 rounded-3xl bg-slate-50/80 hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 transition duration-200 space-y-3 text-center border border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900">Wireless 58mm Slips</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Direct Bluetooth mobile thermal printing.</p>
                    </div>

                    <!-- Satellite 3: WhatsApp Receipts -->
                    <div class="p-6 rounded-3xl bg-slate-50/80 hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 transition duration-200 space-y-3 text-center border border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900">WhatsApp Receipts</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Instant customer receipts formatted in 1 tap.</p>
                    </div>

                    <!-- Satellite 4: Real-time Stock Sync -->
                    <div class="p-6 rounded-3xl bg-slate-50/80 hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 transition duration-200 space-y-3 text-center border border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900">Inventory Sync</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Automatic deduction across all channels.</p>
                    </div>

                    <!-- Satellite 5: Split Payments -->
                    <div class="p-6 rounded-3xl bg-slate-50/80 hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 transition duration-200 space-y-3 text-center border border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900">Split Payments</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Collect partly in Cash &amp; partly by Transfer.</p>
                    </div>

                    <!-- Satellite 6: Daily Z-Reports -->
                    <div class="p-6 rounded-3xl bg-slate-50/80 hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 transition duration-200 space-y-3 text-center border border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900">Daily Closeout Z-Report</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">1-click register balancing at end of shift.</p>
                    </div>

                    <!-- Satellite 7: Paystack Integration -->
                    <div class="p-6 rounded-3xl bg-slate-50/80 hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 transition duration-200 space-y-3 text-center border border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-cyan-100 text-cyan-700 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900">Paystack Checkout</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Instant cards &amp; automated virtual accounts.</p>
                    </div>

                    <!-- Satellite 8: Multi-Store Isolation -->
                    <div class="p-6 rounded-3xl bg-slate-50/80 hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 transition duration-200 space-y-3 text-center border border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900">Multi-Location Branch</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Manage multiple boutiques from one login.</p>
                    </div>

                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 3: "SET UP IN THREE STEPS"                          -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section class="py-14 sm:py-20 bg-slate-50/60 border-y border-slate-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
                <!-- Section Header -->
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Fast Onboarding</span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-950 tracking-tight">
                        Set up in three steps
                    </h2>
                    <p class="text-sm sm:text-base text-slate-500 leading-relaxed">
                        From signing up to ringing up your first customer sale in under 10 minutes.
                    </p>
                </div>

                <!-- 3 Steps Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                    <!-- Step 1 -->
                    <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-900/5 space-y-6 flex flex-col justify-between">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white font-display font-black text-xl flex items-center justify-center">
                                1
                            </div>
                            <h3 class="font-display font-bold text-xl text-slate-900">Add your products</h3>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                                Use our conversational 5-question wizard to enter items, sizes, colors, and prices in seconds.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 text-xs font-bold text-blue-600 flex items-center gap-1">
                            <span>Step 1: Inventory catalog</span>
                            <span>&rarr;</span>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-900/5 space-y-6 flex flex-col justify-between">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white font-display font-black text-xl flex items-center justify-center">
                                2
                            </div>
                            <h3 class="font-display font-bold text-xl text-slate-900">Connect your till &amp; staff</h3>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                                Pair your mobile phone with any 58mm portable Bluetooth thermal printer and set cashier security passwords.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 text-xs font-bold text-blue-600 flex items-center gap-1">
                            <span>Step 2: Terminal setup</span>
                            <span>&rarr;</span>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-900/5 space-y-6 flex flex-col justify-between">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white font-display font-black text-xl flex items-center justify-center">
                                3
                            </div>
                            <h3 class="font-display font-bold text-xl text-slate-900">Start selling &amp; balancing</h3>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                                Ring up walk-ins, split payments, print slips, send WhatsApp receipts, and balance your daily drawer with 1 click.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 text-xs font-bold text-blue-600 flex items-center gap-1">
                            <span>Step 3: Ring &amp; close shift</span>
                            <span>&rarr;</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 4: "START FREE FOR 14 DAYS, GROW AT YOUR PACE"      -->
        <!-- Warm Sand / Beige background matching myshoplet screenshot   -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section id="pricing" class="py-14 sm:py-20 bg-warm-sand">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <!-- Section Header -->
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Pricing Plans</span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-950 tracking-tight">
                        Start free for 14 days, grow at your pace
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                        Transparent plans built for Nigerian boutiques and merchants. No contracts, cancel anytime.
                    </p>

                    <!-- Billing Toggle Pills -->
                    <div class="pt-4 inline-flex items-center p-1.5 rounded-full bg-white shadow-sm border border-slate-200">
                        <button
                            type="button"
                            @click="billingCycle = 'monthly'"
                            :class="billingCycle === 'monthly' ? 'bg-blue-600 text-white font-bold' : 'text-slate-600 font-semibold'"
                            class="px-5 py-2 rounded-full text-xs transition"
                        >
                            Monthly
                        </button>
                        <button
                            type="button"
                            @click="billingCycle = 'annual'"
                            :class="billingCycle === 'annual' ? 'bg-blue-600 text-white font-bold' : 'text-slate-600 font-semibold'"
                            class="px-5 py-2 rounded-full text-xs transition flex items-center gap-1.5"
                        >
                            <span>Annually</span>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">Save 20%</span>
                        </button>
                    </div>
                </div>

                <!-- 3 Pricing Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch pt-4">

                    <!-- Plan 1: Starter -->
                    <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-900/5 space-y-6 flex flex-col justify-between border border-slate-200/80">
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-display font-bold text-lg text-slate-900">Starter Store</h3>
                                <p class="text-xs text-slate-500 mt-1">For single boutique owners &amp; walk-in stores</p>
                            </div>

                            <div class="flex items-baseline gap-1">
                                <span class="font-display font-extrabold text-3xl sm:text-4xl text-slate-950" x-text="billingCycle === 'annual' ? '₦6,400' : '₦8,000'"></span>
                                <span class="text-xs text-slate-400 font-medium">/ month</span>
                            </div>

                            <div class="border-b border-slate-100 pt-2"></div>

                            <ul class="space-y-3 text-xs text-slate-600">
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Full Walk-In POS Till</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Direct WhatsApp Receipts</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Up to 150 Products</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>2 Cashier / Staff Logins</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Paystack Payment Gateway</span>
                                </li>
                            </ul>
                        </div>

                        <a href="{{ route('register') }}" class="w-full py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-xs uppercase tracking-wider text-center transition">
                            Start Free Trial
                        </a>
                    </div>

                    <!-- Plan 2: Pro Retailer (Highlighted) -->
                    <div class="bg-white rounded-3xl p-8 shadow-2xl shadow-blue-600/10 space-y-6 flex flex-col justify-between relative border-2 border-blue-600">
                        <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-blue-600 text-white text-[10px] font-extrabold uppercase tracking-wider shadow-sm">
                            MOST POPULAR
                        </span>

                        <div class="space-y-4">
                            <div>
                                <h3 class="font-display font-bold text-lg text-slate-900">Pro Retailer</h3>
                                <p class="text-xs text-slate-500 mt-1">For busy retail shops, boutiques &amp; growing brands</p>
                            </div>

                            <div class="flex items-baseline gap-1">
                                <span class="font-display font-extrabold text-3xl sm:text-4xl text-blue-600" x-text="billingCycle === 'annual' ? '₦14,400' : '₦18,000'"></span>
                                <span class="text-xs text-slate-400 font-medium">/ month</span>
                            </div>

                            <div class="border-b border-slate-100 pt-2"></div>

                            <ul class="space-y-3 text-xs text-slate-600">
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Everything in Starter</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <strong class="text-slate-900">58mm Wireless Thermal Slips</strong>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <strong class="text-slate-900">Split Payments (Cash + Transfer)</strong>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <strong class="text-slate-900">Cashier Password Authorization</strong>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Daily Closeout Z-Reports</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Unlimited Products &amp; Staff</span>
                                </li>
                            </ul>
                        </div>

                        <a href="{{ route('register') }}" class="w-full py-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider text-center transition shadow-lg shadow-blue-600/30 active:scale-95">
                            Start 14-Day Free Trial &rarr;
                        </a>
                    </div>

                    <!-- Plan 3: Enterprise -->
                    <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-900/5 space-y-6 flex flex-col justify-between border border-slate-200/80">
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-display font-bold text-lg text-slate-900">Enterprise Multi-Store</h3>
                                <p class="text-xs text-slate-500 mt-1">For multi-location retail chains &amp; franchises</p>
                            </div>

                            <div class="flex items-baseline gap-1">
                                <span class="font-display font-extrabold text-3xl sm:text-4xl text-slate-950" x-text="billingCycle === 'annual' ? '₦36,000' : '₦45,000'"></span>
                                <span class="text-xs text-slate-400 font-medium">/ month</span>
                            </div>

                            <div class="border-b border-slate-100 pt-2"></div>

                            <ul class="space-y-3 text-xs text-slate-600">
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Up to 5 Store Branches</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Centralized Warehouse Stock Sync</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Automated Staff Payroll Transfers</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Custom Domain Integration</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Dedicated Account Manager</span>
                                </li>
                            </ul>
                        </div>

                        <a href="{{ route('register') }}" class="w-full py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-xs uppercase tracking-wider text-center transition">
                            Contact Sales
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 5: "A REAL SHOP. FROM BROWSING TO PAID."            -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section class="py-14 sm:py-20 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Fast Checkout Experience</span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-950 tracking-tight">
                        A real shop. From browsing to paid.
                    </h2>
                    <p class="text-sm sm:text-base text-slate-500 leading-relaxed">
                        Whether walk-in customers tap their cards, transfer to your bank, or pay cash, transactions are confirmed and recorded in under 5 seconds.
                    </p>
                </div>

                <!-- Showcase Media Card -->
                <div class="bg-gradient-to-tr from-slate-900 via-slate-950 to-slate-900 text-white rounded-3xl p-8 sm:p-14 shadow-2xl overflow-hidden relative">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div class="space-y-6">
                            <span class="px-3.5 py-1.5 rounded-full bg-blue-600/30 border border-blue-500/40 text-blue-300 text-xs font-bold">
                                Mobile POS &bull; Wireless Ready
                            </span>
                            <h3 class="font-display font-bold text-2xl sm:text-3xl text-white">
                                Print receipts directly from your smartphone.
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                                Pair your Android or iOS phone with any 58mm portable Bluetooth thermal printer (Goojprt, MPT-II, Netum). Tap "Print Slip" and watch the receipt feed cleanly past the tear bar. Or share formatted receipts straight to customer WhatsApp chats.
                            </p>
                            <div class="flex items-center gap-3 pt-2">
                                <a href="{{ route('admin.sales.create') }}" class="px-6 py-3 rounded-full bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition">
                                    Open POS Terminal &rarr;
                                </a>
                            </div>
                        </div>

                        <!-- Thermal Receipt Code Sample / Visual Ticket -->
                        <div class="bg-white rounded-2xl p-6 font-mono text-xs text-slate-900 shadow-xl max-w-sm mx-auto space-y-3 border-t-4 border-slate-900">
                            <div class="text-center space-y-0.5">
                                <div class="font-extrabold text-sm uppercase">JESSI'S CLASSIQ STORE</div>
                                <div class="text-[10px] text-slate-500 font-sans">Flagship Boutique &bull; Lagos</div>
                                <div class="text-[9px] text-slate-400">*** OFFICIAL STORE RECEIPT ***</div>
                            </div>
                            <div class="border-b border-dashed border-slate-300 my-2"></div>
                            <div class="space-y-1 text-[11px]">
                                <div class="flex justify-between"><span>Receipt:</span><strong>SR-1024</strong></div>
                                <div class="flex justify-between"><span>Date:</span><span>Today, 02:45 PM</span></div>
                                <div class="flex justify-between"><span>Cashier:</span><span>Blessing</span></div>
                            </div>
                            <div class="border-b border-dashed border-slate-300 my-2"></div>
                            <div class="space-y-1">
                                <div class="font-bold">Snake-Accent Handbag &amp; Wallet Set</div>
                                <div class="flex justify-between text-[11px] text-slate-600">
                                    <span>1 x ₦45,000</span>
                                    <strong class="text-black">₦45,000</strong>
                                </div>
                            </div>
                            <div class="border-b border-dashed border-slate-300 my-2"></div>
                            <div class="flex justify-between items-baseline pt-1">
                                <span class="font-bold text-xs">TOTAL PAID</span>
                                <span class="font-extrabold text-base text-black font-sans">₦45,000</span>
                            </div>
                            <div class="text-[10px] text-slate-500">PAYMENT METHOD: SPLIT (Cash + Transfer)</div>
                            <div class="border-b border-dashed border-slate-300 my-2"></div>
                            <div class="text-center text-[9px] text-slate-400 font-sans">
                                Thank you for your patronage!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 6: "WHAT VENDORS ARE SAYING" (Testimonials)         -->
        <!-- Warm Sand / Beige background matching myshoplet screenshot   -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section id="testimonials" class="py-14 sm:py-20 bg-warm-sand">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Social Proof</span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-950 tracking-tight">
                        What vendors are saying
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                        Trusted by fashion boutiques, shoe stores, and retail merchants across Nigeria.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Review 1 -->
                    <div class="bg-white rounded-3xl p-7 shadow-xl shadow-slate-900/5 space-y-4 flex flex-col justify-between border border-slate-200/60">
                        <div class="space-y-3">
                            <div class="flex text-amber-400 text-xs gap-1">
                                <span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                "The POS counter is as fast as our Moniepoint terminal. Our walk-in customers love getting their receipts right on WhatsApp, and the wireless printer setup took literally 2 minutes on my phone."
                            </p>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center">
                                JA
                            </div>
                            <div>
                                <div class="font-bold text-xs text-slate-900">Jessica Adebayo</div>
                                <div class="text-[10px] text-slate-400">Jessi's ClassiQ Boutique, Victoria Island</div>
                            </div>
                        </div>
                    </div>

                    <!-- Review 2 -->
                    <div class="bg-white rounded-3xl p-7 shadow-xl shadow-slate-900/5 space-y-4 flex flex-col justify-between border border-slate-200/60">
                        <div class="space-y-3">
                            <div class="flex text-amber-400 text-xs gap-1">
                                <span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                "Before ShopManager, we were losing track of silk gowns sold between our Instagram page and our Lekki boutique. Now stock updates everywhere automatically the instant a sale happens."
                            </p>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center">
                                CE
                            </div>
                            <div>
                                <div class="font-bold text-xs text-slate-900">Chidinma Eze</div>
                                <div class="text-[10px] text-slate-400">Aura Luxury Apparel, Lekki Phase 1</div>
                            </div>
                        </div>
                    </div>

                    <!-- Review 3 -->
                    <div class="bg-white rounded-3xl p-7 shadow-xl shadow-slate-900/5 space-y-4 flex flex-col justify-between border border-slate-200/60">
                        <div class="space-y-3">
                            <div class="flex text-amber-400 text-xs gap-1">
                                <span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                "The daily Z-report closeout is a lifesaver. At 7 PM when my staff closes the store, they print the slip showing exactly how much cash and transfer came in. No missing money."
                            </p>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center">
                                TO
                            </div>
                            <div>
                                <div class="font-bold text-xs text-slate-900">Tunde Ogunleye</div>
                                <div class="text-[10px] text-slate-400">Urban Kicks &amp; Accessories, Ikeja</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 7: "QUESTIONS? ANSWERED." (Interactive FAQ)         -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section id="faq" class="py-14 sm:py-20 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center space-y-3">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Frequently Asked Questions</span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-950 tracking-tight">
                        Questions? Answered.
                    </h2>
                    <p class="text-sm sm:text-base text-slate-500">
                        Everything you need to know about setting up and running your boutique.
                    </p>
                </div>

                <div class="divide-y divide-slate-100 border-y border-slate-100">

                    <!-- FAQ 1 -->
                    <div class="py-5">
                        <button
                            type="button"
                            @click="toggleFaq(1)"
                            class="w-full text-left flex items-center justify-between text-base font-bold text-slate-900 hover:text-blue-600 transition cursor-pointer"
                        >
                            <span>Do I need to buy expensive POS hardware?</span>
                            <span class="text-slate-400 text-lg" x-text="openFaq === 1 ? '−' : '+'"></span>
                        </button>
                        <div x-show="openFaq === 1" x-transition class="pt-3 text-xs sm:text-sm text-slate-600 leading-relaxed">
                            No! {{ config('app.platform_name', 'ShopManager') }} runs in the browser on any smartphone, iPad, tablet, or laptop you already own. You don't need dedicated bank terminals or POS hardware contracts.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="py-5">
                        <button
                            type="button"
                            @click="toggleFaq(2)"
                            class="w-full text-left flex items-center justify-between text-base font-bold text-slate-900 hover:text-blue-600 transition cursor-pointer"
                        >
                            <span>How do wireless thermal printers work with the system?</span>
                            <span class="text-slate-400 text-lg" x-text="openFaq === 2 ? '−' : '+'"></span>
                        </button>
                        <div x-show="openFaq === 2" x-transition class="pt-3 text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Our POS receipt slip is formatted strictly for 58mm &amp; 80mm wireless portable Bluetooth thermal printers (like Goojprt PT-210, MPT-II, Netum). It supports direct Android Bluetooth printing via RawBT as well as iOS AirPrint.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="py-5">
                        <button
                            type="button"
                            @click="toggleFaq(3)"
                            class="w-full text-left flex items-center justify-between text-base font-bold text-slate-900 hover:text-blue-600 transition cursor-pointer"
                        >
                            <span>Can customers pay with split payments (Cash + Bank Transfer)?</span>
                            <span class="text-slate-400 text-lg" x-text="openFaq === 3 ? '−' : '+'"></span>
                        </button>
                        <div x-show="openFaq === 3" x-transition class="pt-3 text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Yes! Our terminal has a built-in Split Pay mode. If a customer buys a ₦50,000 dress and pays ₦20,000 cash and ₦30,000 by transfer, both amounts are recorded and clearly shown on the customer's receipt.
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="py-5">
                        <button
                            type="button"
                            @click="toggleFaq(4)"
                            class="w-full text-left flex items-center justify-between text-base font-bold text-slate-900 hover:text-blue-600 transition cursor-pointer"
                        >
                            <span>How do customer WhatsApp receipts work?</span>
                            <span class="text-slate-400 text-lg" x-text="openFaq === 4 ? '−' : '+'"></span>
                        </button>
                        <div x-show="openFaq === 4" x-transition class="pt-3 text-xs sm:text-sm text-slate-600 leading-relaxed">
                            When entering the customer's phone number at checkout, clicking "Share on WhatsApp" directly opens the chat with that customer on your phone or web WhatsApp with their itemized receipt pre-filled and ready to send.
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="py-5">
                        <button
                            type="button"
                            @click="toggleFaq(5)"
                            class="w-full text-left flex items-center justify-between text-base font-bold text-slate-900 hover:text-blue-600 transition cursor-pointer"
                        >
                            <span>Can I manage multiple branch stores under one account?</span>
                            <span class="text-slate-400 text-lg" x-text="openFaq === 5 ? '−' : '+'"></span>
                        </button>
                        <div x-show="openFaq === 5" x-transition class="pt-3 text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Yes! Our multi-tenant architecture allows you to run multiple stores or retail branches with separate inventories, staff accounts, and daily takings reports from one master login.
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 8: "PRACTICAL TIPS TO GROW YOUR RETAIL SALES"       -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section class="py-14 sm:py-20 bg-white border-t border-slate-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div class="space-y-3">
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Retail Insights &amp; Guides</span>
                        <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-950 tracking-tight">
                            Practical tips to grow your online &amp; retail sales
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-xl">
                            Field-tested playbooks, cashier management tips, and retail inventory strategies from fast-growing merchants.
                        </p>
                    </div>
                    <a href="#pricing" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                        <span>Read all guides</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Guide 1 -->
                    <article class="group rounded-3xl bg-slate-50/70 border border-slate-100 overflow-hidden hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 transition duration-200 flex flex-col justify-between">
                        <div class="p-6 space-y-4">
                            <div class="h-44 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 p-6 flex flex-col justify-between text-white relative overflow-hidden">
                                <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-wider">
                                    <span class="px-2.5 py-1 rounded-full bg-white/20 backdrop-blur-xs">Retail Ops</span>
                                    <span>5 min read</span>
                                </div>
                                <div class="font-display font-extrabold text-lg leading-snug">
                                    Balancing cash &amp; bank transfer drawers without missing money
                                </div>
                            </div>
                            <div class="space-y-2">
                                <span class="text-[11px] text-slate-400 font-medium">Updated 3 days ago</span>
                                <h3 class="font-bold text-base text-slate-900 group-hover:text-blue-600 transition leading-snug">
                                    How Lagos boutiques use daily closeout Z-reports to audit cashiers
                                </h3>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Step-by-step cashier shifts handoff: separating transfer notifications from cash at register closing.
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-2 text-xs font-bold text-blue-600 flex items-center gap-1">
                            <span>Read article</span>
                            <span>&rarr;</span>
                        </div>
                    </article>

                    <!-- Guide 2 -->
                    <article class="group rounded-3xl bg-slate-50/70 border border-slate-100 overflow-hidden hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 transition duration-200 flex flex-col justify-between">
                        <div class="p-6 space-y-4">
                            <div class="h-44 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 p-6 flex flex-col justify-between text-white relative overflow-hidden">
                                <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-wider">
                                    <span class="px-2.5 py-1 rounded-full bg-white/20 backdrop-blur-xs">Customer Loyalty</span>
                                    <span>4 min read</span>
                                </div>
                                <div class="font-display font-extrabold text-lg leading-snug">
                                    Converting 1-time walk-ins into repeat buyers via WhatsApp
                                </div>
                            </div>
                            <div class="space-y-2">
                                <span class="text-[11px] text-slate-400 font-medium">Updated this week</span>
                                <h3 class="font-bold text-base text-slate-900 group-hover:text-blue-600 transition leading-snug">
                                    Why digital receipts on WhatsApp outperform paper slips for VIP re-engagement
                                </h3>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Capture genuine phone numbers at POS checkout and follow up with tailored restock announcements.
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-2 text-xs font-bold text-blue-600 flex items-center gap-1">
                            <span>Read article</span>
                            <span>&rarr;</span>
                        </div>
                    </article>

                    <!-- Guide 3 -->
                    <article class="group rounded-3xl bg-slate-50/70 border border-slate-100 overflow-hidden hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 transition duration-200 flex flex-col justify-between">
                        <div class="p-6 space-y-4">
                            <div class="h-44 rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-500 p-6 flex flex-col justify-between text-white relative overflow-hidden">
                                <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-wider">
                                    <span class="px-2.5 py-1 rounded-full bg-white/20 backdrop-blur-xs">Hardware Setup</span>
                                    <span>3 min read</span>
                                </div>
                                <div class="font-display font-extrabold text-lg leading-snug">
                                    Setting up wireless 58mm thermal printers in under 3 minutes
                                </div>
                            </div>
                            <div class="space-y-2">
                                <span class="text-[11px] text-slate-400 font-medium">Hardware Guide</span>
                                <h3 class="font-bold text-base text-slate-900 group-hover:text-blue-600 transition leading-snug">
                                    The ultimate guide to Bluetooth thermal receipt printing on Android &amp; iOS
                                </h3>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Tested on Goojprt PT-210, MPT-II, and Netum mini printers with no special ink or ribbons needed.
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-2 text-xs font-bold text-blue-600 flex items-center gap-1">
                            <span>Read article</span>
                            <span>&rarr;</span>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 9: COMMERCIAL PROMO OFFER (50% Off / Migration)     -->
        <!-- Gradient banner matching myshoplet screenshot               -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section class="py-12 bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-700 text-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6 p-6 sm:p-10 rounded-3xl bg-white/10 backdrop-blur-md border border-white/20 shadow-2xl">
                    <div class="space-y-2 text-center md:text-left">
                        <span class="px-3 py-1 rounded-full bg-white/20 text-white text-[10px] font-extrabold uppercase tracking-wider">
                            Limited Time Merchant Offer
                        </span>
                        <h3 class="font-display font-extrabold text-2xl sm:text-3xl text-white">
                            Switching from another POS or manual ledgers?
                        </h3>
                        <p class="text-xs sm:text-sm text-blue-100 max-w-xl">
                            We will import your inventory catalog for free and give you <strong>50% off your first 3 months</strong> on any plan.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0">
                        <div class="text-center sm:text-right hidden sm:block">
                            <div class="font-display font-black text-3xl text-amber-300">50% OFF</div>
                            <div class="text-[10px] text-blue-100 uppercase tracking-wider">Use code: SWITCH50</div>
                        </div>
                        <a
                            href="{{ route('register') }}"
                            class="px-8 py-3.5 rounded-full bg-white hover:bg-slate-100 text-blue-800 font-extrabold text-xs uppercase tracking-wider transition shadow-lg active:scale-95"
                        >
                            Claim offer &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 10: ACTIVE STORES DIRECTORY (Quick Launch)          -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        @php
            $activeStores = \App\Models\Tenant::where('status', 'active')->latest()->get();
        @endphp
        <section id="stores" class="py-16 bg-slate-50 border-t border-slate-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
                    <div>
                        <h2 class="font-display font-extrabold text-xl text-slate-900">Connected Stores &amp; Boutiques</h2>
                        <p class="text-xs text-slate-500">Launch a live store console or customer-facing boutique storefront.</p>
                    </div>
                    <a href="{{ route('register') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                        + Onboard New Store
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse ($activeStores as $store)
                        <div class="p-5 rounded-3xl bg-white shadow-md shadow-slate-900/5 flex flex-col justify-between gap-4 border border-slate-100">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-extrabold text-sm shadow-sm">
                                        {{ strtoupper(substr($store->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $store->name }}</h3>
                                        <span class="text-xs text-slate-400 font-mono">{{ $store->slug }}</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700">
                                    Live Store
                                </span>
                            </div>

                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                @if ($store->hasFeature('storefront'))
                                    <a href="{{ url('/?tenant=' . $store->slug) }}" class="flex-1 py-2 px-3 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold text-center transition">
                                        Storefront &nearr;
                                    </a>
                                @endif
                                <a href="{{ url('/admin?tenant=' . $store->slug) }}" class="flex-1 py-2 px-3 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold text-center transition shadow-xs">
                                    Admin Console &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8 text-xs text-slate-400">
                            No stores created yet. Click "+ Onboard New Store" to get started.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 9: FINAL DARK CTA BANNER (myshoplet style)          -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section class="py-14 sm:py-20 bg-[#0b0f19] text-white relative overflow-hidden">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8 relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-white text-xs font-bold">
                    <span>GET STARTED IN 2 MINUTES</span>
                </div>

                <h2 class="font-display font-extrabold text-3xl sm:text-5xl lg:text-6xl tracking-tight leading-tight max-w-3xl mx-auto">
                    Ready to grow your retail business?
                </h2>

                <p class="text-sm sm:text-lg text-slate-400 max-w-xl mx-auto leading-relaxed font-normal">
                    Join modern fashion boutiques and retail merchants taking full control of their counter, inventory, and revenue.
                </p>

                <div class="pt-2">
                    <a
                        href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 px-9 py-4 rounded-full bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs uppercase tracking-wider transition shadow-2xl shadow-blue-600/40 active:scale-95"
                    >
                        <span>Start your free trial</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 10: CLEAN COMPREHENSIVE FOOTER                      -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <footer class="bg-white border-t border-slate-200/80 py-12 text-slate-500 text-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                    <!-- Col 1: Brand Info -->
                    <div class="col-span-2 space-y-4">
                        <div class="flex items-center gap-2.5">
                            <img
                                src="{{ asset('images/shopmanager-logo.png') }}"
                                alt="{{ config('app.platform_name', 'ShopManager') }}"
                                class="h-8 w-auto object-contain"
                            />
                            <span class="sr-only">{{ config('app.platform_name', 'ShopManager') }}</span>
                        </div>
                        <p class="text-xs text-slate-500 max-w-sm leading-relaxed">
                            The modern retail POS and multi-store inventory operating system for African boutiques, fashion merchants, and walk-in retailers.
                        </p>
                        <div class="text-[11px] text-slate-400">
                            Powered by Paystack &bull; Wireless 58mm Thermal Ready
                        </div>
                    </div>

                    <!-- Col 2: Product -->
                    <div class="space-y-3">
                        <div class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">Product</div>
                        <ul class="space-y-2 text-xs">
                            <li><a href="{{ route('admin.sales.create') }}" class="hover:text-blue-600 transition">Walk-in POS</a></li>
                            <li><a href="#receipts" class="hover:text-blue-600 transition">Wireless Receipts</a></li>
                            <li><a href="#inventory" class="hover:text-blue-600 transition">Inventory Sync</a></li>
                            <li><a href="#pricing" class="hover:text-blue-600 transition">Pricing Plans</a></li>
                        </ul>
                    </div>

                    <!-- Col 3: Resources -->
                    <div class="space-y-3">
                        <div class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">Resources</div>
                        <ul class="space-y-2 text-xs">
                            <li><a href="{{ route('login') }}" class="hover:text-blue-600 transition">Merchant Login</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-blue-600 transition">Create Store</a></li>
                            <li><a href="#faq" class="hover:text-blue-600 transition">Support &amp; FAQ</a></li>
                            <li><a href="#stores" class="hover:text-blue-600 transition">Active Boutiques</a></li>
                        </ul>
                    </div>

                    <!-- Col 4: Platform -->
                    <div class="space-y-3">
                        <div class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">Platform</div>
                        <ul class="space-y-2 text-xs">
                            <li><a href="{{ route('super-admin.dashboard') }}" class="hover:text-blue-600 transition">Super Admin</a></li>
                            <li><a href="{{ route('cpanel.deploy') }}" class="hover:text-blue-600 transition">System Status</a></li>
                            <li><span class="text-slate-400">Version 2.4.0</span></li>
                        </ul>
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-slate-400 text-[11px]">
                    <div>
                        &copy; {{ date('Y') }} {{ config('app.platform_name', 'ShopManager') }}. All rights reserved.
                    </div>
                    <div class="flex items-center gap-6">
                        <a href="#" class="hover:text-slate-600">Privacy Policy</a>
                        <a href="#" class="hover:text-slate-600">Terms of Service</a>
                        <a href="#" class="hover:text-slate-600">Security</a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- FLOATING BOTTOM CONVERSION PILL (myshoplet style)           -->
        <!-- Only surfaces after scrolling down past the hero fold       -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div
            x-data="{ showStickyPill: false, dismissed: false }"
            x-init="window.addEventListener('scroll', () => { if (!dismissed) { showStickyPill = window.scrollY > 400; } })"
            x-show="showStickyPill && !dismissed"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-6"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-6"
            class="fixed bottom-6 inset-x-4 sm:inset-x-auto sm:left-1/2 sm:-translate-x-1/2 z-40 max-w-md w-full"
        >
            <div class="bg-slate-900/95 backdrop-blur-md text-white rounded-2xl p-3 px-4 shadow-2xl border border-slate-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        SM
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-xs text-white truncate">Open your free store in 2 mins</div>
                        <div class="text-[10px] text-slate-400 flex items-center gap-1.5">
                            <span class="text-amber-400">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                            <span>Rated 4.9/5 by 300+ merchants</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a
                        href="{{ route('register') }}"
                        class="px-4 py-2 rounded-full bg-blue-600 hover:bg-blue-500 text-white font-bold text-[11px] uppercase tracking-wider transition active:scale-95"
                    >
                        Try free &rarr;
                    </a>
                    <button
                        type="button"
                        @click="dismissed = true; showStickyPill = false"
                        class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
                        title="Dismiss"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

    </body>
</html>
