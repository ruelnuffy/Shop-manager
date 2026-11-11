<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.platform_name', 'ShopManager') }} &mdash; Modern Retail POS &amp; Inventory Operating System</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@700;800;900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            }
            .font-display {
                font-family: 'Syne', 'Plus Jakarta Sans', sans-serif;
            }
        </style>
    </head>
    <body
        x-data="{
            mobileNav: false,
            activeTab: 'pos',
            demoItem: 'Luxury Snake-Accent Bag',
            demoPrice: 45000,
            demoQty: 1,
            demoMethod: 'split',
            demoSuccess: false,
            triggerDemoCharge() {
                this.demoSuccess = true;
                setTimeout(() => { this.demoSuccess = false; }, 3500);
            }
        }"
        class="bg-[#fafbfe] text-slate-900 antialiased selection:bg-slate-900 selection:text-white min-h-screen flex flex-col"
    >

        <!-- Ambient Background Soft Gradient Orbs -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[1000px] h-[550px] bg-gradient-to-b from-emerald-100/40 via-indigo-50/30 to-transparent rounded-full blur-3xl"></div>
            <div class="absolute top-[45%] -left-40 w-[600px] h-[600px] bg-violet-100/30 rounded-full blur-3xl"></div>
            <div class="absolute top-[65%] -right-40 w-[600px] h-[600px] bg-emerald-50/40 rounded-full blur-3xl"></div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- FLOATING TOP NAVIGATION PILL                                -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <header class="sticky top-3 z-50 px-4 sm:px-6 pt-2 pb-4">
            <div class="max-w-6xl mx-auto bg-white/90 backdrop-blur-md shadow-xl shadow-slate-900/5 rounded-full px-5 py-3 flex items-center justify-between gap-4">
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center font-extrabold text-sm shadow-md group-hover:scale-105 transition-transform">
                        SM
                    </div>
                    <span class="font-display font-extrabold text-lg tracking-tight text-slate-900">
                        {{ config('app.platform_name', 'ShopManager') }}
                    </span>
                </a>

                <!-- Nav Links (Desktop) -->
                <nav class="hidden md:flex items-center gap-1 text-xs font-semibold text-slate-600">
                    <a href="#pos-feature" class="px-3.5 py-1.5 rounded-full hover:text-slate-900 hover:bg-slate-100/80 transition">Walk-in POS</a>
                    <a href="#receipts" class="px-3.5 py-1.5 rounded-full hover:text-slate-900 hover:bg-slate-100/80 transition">Wireless Receipts</a>
                    <a href="#inventory" class="px-3.5 py-1.5 rounded-full hover:text-slate-900 hover:bg-slate-100/80 transition">Inventory Sync</a>
                    <a href="#pricing" class="px-3.5 py-1.5 rounded-full hover:text-slate-900 hover:bg-slate-100/80 transition">Pricing</a>
                    <a href="#stores" class="px-3.5 py-1.5 rounded-full hover:text-slate-900 hover:bg-slate-100/80 transition">Live Stores</a>
                </nav>

                <!-- Auth / Action Buttons -->
                <div class="flex items-center gap-2">
                    @auth
                        @if (Auth::user()->isSuperAdmin())
                            <a href="{{ route('super-admin.dashboard') }}" class="px-3.5 py-1.5 rounded-full bg-rose-50 text-rose-700 text-xs font-bold transition flex items-center gap-1.5 hover:bg-rose-100">
                                <span>Super Admin</span>
                            </a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-md shadow-slate-900/10">
                            Dashboard &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-1.5 rounded-full text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                            <span>Get Started</span>
                            <span>&rarr;</span>
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button
                        type="button"
                        @click="mobileNav = !mobileNav"
                        class="md:hidden p-2 rounded-full text-slate-700 hover:bg-slate-100"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div
                x-show="mobileNav"
                x-transition
                class="md:hidden mt-2 bg-white rounded-3xl p-5 shadow-2xl space-y-3"
                style="display: none;"
            >
                <a @click="mobileNav = false" href="#pos-feature" class="block py-2 text-sm font-semibold text-slate-800">Walk-in POS</a>
                <a @click="mobileNav = false" href="#receipts" class="block py-2 text-sm font-semibold text-slate-800">Wireless Receipts</a>
                <a @click="mobileNav = false" href="#inventory" class="block py-2 text-sm font-semibold text-slate-800">Inventory Sync</a>
                <a @click="mobileNav = false" href="#pricing" class="block py-2 text-sm font-semibold text-slate-800">Pricing</a>
                <a @click="mobileNav = false" href="#stores" class="block py-2 text-sm font-semibold text-slate-800">Live Stores</a>
            </div>
        </header>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- HERO SECTION                                                -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <main class="flex-grow relative z-10 space-y-24 sm:space-y-32 pb-20">

            <section class="pt-12 sm:pt-20 px-4 sm:px-6 lg:px-8 text-center max-w-5xl mx-auto space-y-6">
                <!-- Social Proof Pill Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-800 text-xs font-bold shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Unified Retail, Inventory &amp; Walk-In POS Operating System</span>
                </div>

                <!-- Main Punchy Headline -->
                <h1 class="font-display font-extrabold text-4xl sm:text-6xl lg:text-7xl text-slate-950 tracking-tight leading-[1.08] max-w-4xl mx-auto">
                    Sell anywhere.<br>
                    <span class="bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 bg-clip-text text-transparent">Manage everywhere.</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-sm sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed font-normal">
                    Unified Retail, Inventory, and fast walk-in counter sales. Sync multi-channel stock in real time, print wireless 58mm receipts, and get paid with Paystack &mdash; all without clunky, expensive hardware.
                </p>

                <!-- Dual CTAs -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3.5 pt-2">
                    <a
                        href="{{ route('register') }}"
                        class="w-full sm:w-auto px-8 py-4 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider transition shadow-xl shadow-slate-900/15 flex items-center justify-center gap-2 cursor-pointer active:scale-[0.98]"
                    >
                        <span>Start Free Trial</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>

                    <a
                        href="{{ route('admin.sales.create') }}"
                        class="w-full sm:w-auto px-7 py-4 rounded-full bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs uppercase tracking-wider transition shadow-lg shadow-slate-900/5 flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Try POS Counter Demo</span>
                    </a>
                </div>

                <!-- Feature Highlights Badges Strip -->
                <div class="pt-6 flex flex-wrap items-center justify-center gap-4 text-xs font-semibold text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Moniepoint-Fast Checkout</span>
                    </span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>58mm Wireless Thermal Slips</span>
                    </span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Direct WhatsApp Receipts</span>
                    </span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Zero Extra Hardware</span>
                    </span>
                </div>

                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- INTERACTIVE LIVE HERO SHOWCASE (Terminal + Receipt)         -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                <div class="pt-10 max-w-4xl mx-auto">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl shadow-slate-900/10 text-left relative overflow-hidden">

                        <!-- Mockup Top Bar -->
                        <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                                <span class="text-xs font-bold text-slate-400 ml-2 font-mono">pos.shopmanager.local &bull; Live Register</span>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                                Shift Open
                            </span>
                        </div>

                        <!-- Split Mockup Content -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 pt-6 items-start">
                            <!-- Left: Catalog Item -->
                            <div class="md:col-span-7 space-y-4">
                                <div class="p-4 rounded-2xl bg-slate-50 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                            <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-sm text-slate-900" x-text="demoItem"></div>
                                            <div class="text-xs text-slate-400">ClassiQ Exotic Snake Leather &bull; Standard</div>
                                        </div>
                                    </div>
                                    <span class="font-display font-extrabold text-sm sm:text-base text-slate-900">
                                        ₦<span x-text="demoPrice.toLocaleString()"></span>
                                    </span>
                                </div>

                                <!-- Payment Selector Pills -->
                                <div>
                                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Select Payment Mode</div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <button
                                            type="button"
                                            @click="demoMethod = 'pos_card'"
                                            :class="demoMethod === 'pos_card' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'"
                                            class="p-2.5 rounded-xl text-center font-bold text-xs transition"
                                        >
                                            POS Card
                                        </button>
                                        <button
                                            type="button"
                                            @click="demoMethod = 'transfer'"
                                            :class="demoMethod === 'transfer' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'"
                                            class="p-2.5 rounded-xl text-center font-bold text-xs transition"
                                        >
                                            Transfer
                                        </button>
                                        <button
                                            type="button"
                                            @click="demoMethod = 'split'"
                                            :class="demoMethod === 'split' ? 'bg-indigo-900 text-white' : 'bg-indigo-50 text-indigo-900'"
                                            class="p-2.5 rounded-xl text-center font-bold text-xs transition"
                                        >
                                            Split Pay
                                        </button>
                                    </div>
                                </div>

                                <!-- Customer Autofill -->
                                <div class="p-3 rounded-2xl bg-slate-50 text-xs text-slate-600 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/></svg>
                                        <span>Customer: <strong>Chioma Ade</strong> (08012345678)</span>
                                    </div>
                                    <span class="text-emerald-700 font-bold text-[10px] uppercase">WhatsApp Linked</span>
                                </div>
                            </div>

                            <!-- Right: Charge Action & Receipt Preview -->
                            <div class="md:col-span-5 space-y-3">
                                <div class="p-4 rounded-2xl bg-slate-900 text-white space-y-2">
                                    <div class="text-[11px] font-mono text-slate-400">Total Charge Amount</div>
                                    <div class="font-display font-extrabold text-2xl text-emerald-400">
                                        ₦45,000<span class="text-xs text-slate-400 font-sans">.00</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400">Cashier Security: <span class="text-white font-mono">••••••••</span> (Verified)</div>
                                </div>

                                <button
                                    type="button"
                                    @click="triggerDemoCharge()"
                                    class="w-full py-3.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs uppercase tracking-wider transition shadow-lg shadow-emerald-600/25 flex items-center justify-center gap-2 cursor-pointer active:scale-95"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                    <span>TAP TO CHARGE &rarr;</span>
                                </button>

                                <div
                                    x-show="demoSuccess"
                                    x-transition
                                    class="p-3 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-bold flex items-center justify-between"
                                >
                                    <span>Receipt Printed &amp; Sent to WhatsApp!</span>
                                    <span class="text-emerald-600">&check;</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════ -->
            <!-- 4 BIG SUPERPOWERS (Feature Pillars Grid)                    -->
            <!-- ═══════════════════════════════════════════════════════════ -->
            <section id="pos-feature" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Built for Nigerian Retailers</span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">
                        Everything you need to run your boutique seamlessly.
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Say goodbye to missing paper ledger entries, inventory stockouts, and manual bank transfer headaches.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Card 1: Moniepoint-Fast Walk-In POS -->
                    <div class="bg-white rounded-3xl p-7 shadow-xl shadow-slate-900/5 space-y-4 hover:-translate-y-1 transition duration-200">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900">Moniepoint-Fast POS</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Turn any phone, tablet, or laptop into a fast cash register. Supports POS Card, Bank Transfer, Cash, and Split Payments with cashier password security.
                        </p>
                    </div>

                    <!-- Card 2: Wireless Receipts & WhatsApp -->
                    <div id="receipts" class="bg-white rounded-3xl p-7 shadow-xl shadow-slate-900/5 space-y-4 hover:-translate-y-1 transition duration-200">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900">58mm Wireless &amp; WhatsApp Receipts</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Print directly to portable Bluetooth mini thermal printers (RawBT &amp; ESC/POS) or send formatted digital receipt slips straight to customer WhatsApp in 1 tap.
                        </p>
                    </div>

                    <!-- Card 3: Multi-Location Inventory -->
                    <div id="inventory" class="bg-white rounded-3xl p-7 shadow-xl shadow-slate-900/5 space-y-4 hover:-translate-y-1 transition duration-200">
                        <div class="w-12 h-12 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900">Unified Inventory Sync</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            When an item is sold at your physical counter, stock levels instantly update across your storefront and warehouse. Never oversell or lose track of goods.
                        </p>
                    </div>

                    <!-- Card 4: End-of-Day Daily Takings (Z-Report) -->
                    <div class="bg-white rounded-3xl p-7 shadow-xl shadow-slate-900/5 space-y-4 hover:-translate-y-1 transition duration-200">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900">Daily Closeout &amp; Z-Reports</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            1-click shift closeout. Generate instant breakdown of total cash collected, bank transfers received, and card charges with a printable end-of-day register slip.
                        </p>
                    </div>

                    <!-- Card 5: Automated Paystack Integration -->
                    <div class="bg-white rounded-3xl p-7 shadow-xl shadow-slate-900/5 space-y-4 hover:-translate-y-1 transition duration-200">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900">Integrated Paystack Payments</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Receive cards, instant dedicated virtual accounts, and USSD payments. Automated webhook reconciliation ensures zero missing funds.
                        </p>
                    </div>

                    <!-- Card 6: Conversational Product Creator -->
                    <div class="bg-white rounded-3xl p-7 shadow-xl shadow-slate-900/5 space-y-4 hover:-translate-y-1 transition duration-200">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900">Simple 5-Step Product Creator</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            No overwhelming forms. Add items with a conversational step-by-step wizard: Name &rarr; Category &rarr; Price &rarr; Sizes &rarr; Photo &amp; Save.
                        </p>
                    </div>

                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════ -->
            <!-- PRICING TIERS SECTION                                       -->
            <!-- ═══════════════════════════════════════════════════════════ -->
            <section id="pricing" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                <div class="text-center space-y-3">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Simple, Transparent Pricing</span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">
                        Grow your boutique without breaking the bank.
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        No surprise fees. No expensive hardware contracts.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">

                    <!-- Plan 1: Starter -->
                    <div class="bg-white rounded-3xl p-7 shadow-xl shadow-slate-900/5 space-y-6 flex flex-col justify-between">
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">Starter Store</h3>
                                <p class="text-xs text-slate-400 mt-1">For single boutiques &amp; walk-in stores</p>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="font-display font-extrabold text-3xl text-slate-950">Free</span>
                                <span class="text-xs text-slate-400">/ 14 days</span>
                            </div>
                            <div class="border-b border-slate-100 pt-2"></div>
                            <ul class="space-y-2.5 text-xs text-slate-600">
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Full Walk-In POS Till</li>
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> WhatsApp Receipts</li>
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Up to 100 Products</li>
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> 2 Cashier Accounts</li>
                            </ul>
                        </div>
                        <a href="{{ route('register') }}" class="w-full py-3 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold text-center transition">
                            Get Started
                        </a>
                    </div>

                    <!-- Plan 2: Pro Retailer (Highlighted) -->
                    <div class="bg-slate-900 text-white rounded-3xl p-7 shadow-2xl shadow-slate-900/20 space-y-6 flex flex-col justify-between relative ring-2 ring-emerald-500">
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-wider">
                            Most Popular
                        </span>
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-bold text-base text-white">Pro Retailer</h3>
                                <p class="text-xs text-slate-400 mt-1">For busy retail stores &amp; fashion boutiques</p>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="font-display font-extrabold text-3xl text-emerald-400">₦15,000</span>
                                <span class="text-xs text-slate-400">/ month</span>
                            </div>
                            <div class="border-b border-slate-800 pt-2"></div>
                            <ul class="space-y-2.5 text-xs text-slate-300">
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Everything in Starter</li>
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> 58mm Wireless Thermal Slips</li>
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Split Payments &amp; Cashier Locks</li>
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Daily Closeout Z-Reports</li>
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Unlimited Products &amp; Staff</li>
                            </ul>
                        </div>
                        <a href="{{ route('register') }}" class="w-full py-3 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold text-center transition shadow-lg shadow-emerald-600/30">
                            Start Free Trial &rarr;
                        </a>
                    </div>

                    <!-- Plan 3: Multi-Store Enterprise -->
                    <div class="bg-white rounded-3xl p-7 shadow-xl shadow-slate-900/5 space-y-6 flex flex-col justify-between">
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">Multi-Location</h3>
                                <p class="text-xs text-slate-400 mt-1">For multi-branch retail brands</p>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="font-display font-extrabold text-3xl text-slate-950">₦35,000</span>
                                <span class="text-xs text-slate-400">/ month</span>
                            </div>
                            <div class="border-b border-slate-100 pt-2"></div>
                            <ul class="space-y-2.5 text-xs text-slate-600">
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Up to 5 Branch Locations</li>
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Centralized Warehouse Sync</li>
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Automated Staff Payroll</li>
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Dedicated Account Manager</li>
                            </ul>
                        </div>
                        <a href="{{ route('register') }}" class="w-full py-3 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold text-center transition">
                            Contact Sales
                        </a>
                    </div>

                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════ -->
            <!-- ACTIVE STORES DIRECTORY (Quick Launch)                      -->
            <!-- ═══════════════════════════════════════════════════════════ -->
            @php
                $activeStores = \App\Models\Tenant::where('status', 'active')->latest()->get();
            @endphp
            <section id="stores" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/60">
                    <div>
                        <h2 class="font-display font-extrabold text-xl text-slate-900">Your Connected Stores</h2>
                        <p class="text-xs text-slate-400">Launch a live boutique console or customer storefront in 1 click.</p>
                    </div>
                    <a href="{{ route('register') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition">
                        + Onboard New Store
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse ($activeStores as $store)
                        <div class="p-5 rounded-3xl bg-white shadow-xl shadow-slate-900/5 flex flex-col justify-between gap-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold text-sm shadow-sm">
                                        {{ strtoupper(substr($store->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $store->name }}</h3>
                                        <span class="text-xs text-slate-400 font-mono">{{ $store->slug }}</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700">
                                    Active
                                </span>
                            </div>

                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                @if ($store->hasFeature('storefront'))
                                    <a href="{{ url('/?tenant=' . $store->slug) }}" class="flex-1 py-2 px-3 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold text-center transition">
                                        Storefront &nearr;
                                    </a>
                                @endif
                                <a href="{{ url('/admin?tenant=' . $store->slug) }}" class="flex-1 py-2 px-3 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold text-center transition shadow-xs">
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
            </section>

            <!-- ═══════════════════════════════════════════════════════════ -->
            <!-- FINAL CTA BANNER                                            -->
            <!-- ═══════════════════════════════════════════════════════════ -->
            <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-r from-slate-900 via-slate-950 to-slate-900 text-white rounded-3xl p-8 sm:p-14 text-center space-y-6 shadow-2xl shadow-slate-900/20 relative overflow-hidden">
                    <div class="max-w-2xl mx-auto space-y-3">
                        <h2 class="font-display font-extrabold text-3xl sm:text-5xl tracking-tight leading-tight">
                            Upgrade your retail store today.
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                            Join modern fashion boutiques and walk-in retailers taking full control of their counter, cash drawer, and inventory.
                        </p>
                    </div>

                    <div class="pt-2">
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center gap-2 px-8 py-4 rounded-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs uppercase tracking-wider transition shadow-xl shadow-emerald-500/25 active:scale-95"
                        >
                            <span>Launch Your Store Now</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </section>

        </main>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- CLEAN MINIMAL FOOTER                                        -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <footer class="border-t border-slate-200/60 py-10 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
                <div class="flex items-center gap-2 font-display font-bold text-slate-900">
                    <div class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px]">SM</div>
                    <span>{{ config('app.platform_name', 'ShopManager') }}</span>
                </div>
                <div>
                    &copy; {{ date('Y') }} {{ config('app.platform_name', 'ShopManager') }}. Modern Retail &amp; POS Operating System.
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="hover:text-slate-600 transition">Sign In</a>
                    <a href="{{ route('register') }}" class="hover:text-slate-600 transition">Create Store</a>
                    <a href="{{ route('admin.sales.create') }}" class="hover:text-slate-600 transition">POS Counter</a>
                </div>
            </div>
        </footer>

    </body>
</html>
