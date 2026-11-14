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
            html, body {
                font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
            .font-display {
                font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                letter-spacing: -0.025em;
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

            <!-- FLOATING iOS DYNAMIC CAPSULE NAVBAR -->
            <header class="sticky top-2 sm:top-4 z-50 px-3 sm:px-6 max-w-7xl mx-auto pt-2 sm:pt-3">
                <div class="relative bg-white/95 backdrop-blur-2xl border border-white/60 shadow-xl shadow-blue-950/20 rounded-full px-4 sm:px-6 h-14 sm:h-16 flex items-center justify-between ring-1 ring-white/30 transition-all">

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
                <nav class="hidden lg:flex items-center gap-1 p-1 bg-slate-100/70 rounded-full border border-slate-200/50 text-sm font-semibold text-slate-600">
                    <a href="#features" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">Features</a>
                    <a href="#pricing" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">Pricing</a>
                    <a href="#testimonials" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">Reviews</a>
                    <a href="#faq" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">FAQ</a>
                    <a href="{{ route('stores.index') }}" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">Stores</a>
                </nav>

                <!-- Auth & Dynamic Action Cluster -->
                <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                    <!-- Language Switcher (Always visible on Android, iOS & Desktop) -->
                    <select
                        id="langSwitcher"
                        onchange="changeSiteLanguage(this.value)"
                        aria-label="Translate page"
                        class="notranslate h-9 sm:h-10 pl-2.5 pr-6 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 cursor-pointer transition"
                    >
                        <option value="en">EN</option>
                        <option value="ha">Hausa</option>
                        <option value="yo">Yoruba</option>
                        <option value="ig">Igbo</option>
                        <option value="fr">Fran&ccedil;ais</option>
                        <option value="ar">العربية</option>
                        <option value="es">Espa&ntilde;ol</option>
                        <option value="pt">Portugu&ecirc;s</option>
                        <option value="zh-CN">中文</option>
                    </select>

                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold uppercase tracking-wider transition shadow-md shadow-blue-600/25 active:scale-95">
                            <span class="hidden sm:inline">Dashboard</span>
                            <span class="sm:hidden">Console</span>
                            <span>&rarr;</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-block px-3.5 py-2 text-sm font-bold text-slate-700 hover:text-blue-600 transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs sm:text-sm font-bold uppercase tracking-wider transition shadow-md shadow-blue-600/25 flex items-center gap-1 active:scale-95">
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
                        class="lg:hidden w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 active:scale-90 flex items-center justify-center transition border border-slate-200 text-slate-700 cursor-pointer select-none"
                    >
                        <svg id="navBurgerIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 7h16M4 17h16" />
                        </svg>
                        <svg id="navCloseIcon" class="w-4 h-4 text-slate-900 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>

            <!-- iOS Frosted Acrylic Control Center Sheet (Mobile Only) -->
            <div
                id="mobileNavSheet"
                class="hidden lg:hidden mt-2 p-3 sm:p-4 rounded-3xl bg-white/95 backdrop-blur-3xl border border-white/80 shadow-2xl shadow-slate-900/15 ring-1 ring-slate-900/10 space-y-2.5 transition-all text-slate-900"
            >
                <!-- Sheet Header with explicit Close / Done Button -->
                <div class="flex items-center justify-between pb-2 px-2 border-b border-slate-100">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Navigation</span>
                    <button
                        type="button"
                        onclick="toggleShopMobileMenu(event, false)"
                        class="px-3 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-bold transition cursor-pointer flex items-center gap-1 active:scale-95"
                    >
                        <span>Close</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-900">Features &amp; Hub</div>
                                <div class="text-xs text-slate-500">Unified retail, inventory &amp; POS</div>
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
                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-900">Pricing &amp; Plans</div>
                                <div class="text-xs text-slate-500">14-day free trial, cancel anytime</div>
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
                            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-900">Vendor Reviews</div>
                                <div class="text-xs text-slate-500">Stories from Nigerian retailers</div>
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
                            <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-900">FAQ &amp; Hardware</div>
                                <div class="text-xs text-slate-500">Bluetooth printer pairing &amp; setup</div>
                            </div>
                        </div>
                        <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
                    </a>

                    <a
                        onclick="toggleShopMobileMenu(event, false)"
                        href="{{ route('stores.index') }}"
                        class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-100/80 active:bg-slate-200/70 transition group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-900">Explore Stores</div>
                                <div class="text-xs text-slate-500">Registered shops &amp; boutiques</div>
                            </div>
                        </div>
                        <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
                    </a>
                </div>

                <!-- Mobile Action CTA Row -->
                <div class="pt-2 border-t border-slate-100/80 flex flex-col gap-2">
                    <a
                        href="{{ route('register') }}"
                        class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-extrabold text-xs sm:text-sm uppercase tracking-wider text-center transition shadow-lg shadow-blue-600/30 active:scale-95"
                    >
                        Start 14-Day Free Trial &rarr;
                    </a>
                    <a
                        href="{{ route('login') }}"
                        class="w-full py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs sm:text-sm text-center transition active:scale-95"
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
        <!-- SECTION 1: ROYAL BLUE HERO CONTENT                          -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-6 sm:pt-10 lg:pt-14 pb-12 sm:pb-16 lg:pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                <!-- Left: Hero Text & Call to Actions -->
                <div class="lg:col-span-6 space-y-7 sm:space-y-8 text-center lg:text-left">
                    <h1 class="font-display font-black text-4xl sm:text-5xl md:text-6xl lg:text-[60px] xl:text-[68px] tracking-tight leading-[1.06]">
                        Sell anywhere.<br class="hidden sm:inline">
                        It all lands in one place.
                    </h1>

                    <div class="flex items-center justify-center lg:justify-start pt-1">
                        <a
                            href="https://wa.me/2349046686176?text=I%20want%20to%20subscribe"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full sm:w-auto px-8 py-4 rounded-full bg-white hover:bg-slate-50 text-blue-800 font-extrabold text-sm uppercase tracking-wider transition shadow-xl shadow-blue-950/20 flex items-center justify-center gap-2.5 active:scale-95"
                        >
                            <span>Sign Up</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right: Live 3D Business Mascot Showcase -->
                <div class="lg:col-span-6 relative mt-4 lg:mt-0">
                    <style>
                        @keyframes heroMascotFloat {
                            0%, 100% { transform: translateY(0px) rotate(0deg); }
                            50% { transform: translateY(-10px) rotate(0.6deg); }
                        }
                        .hero-mascot-float {
                            animation: heroMascotFloat 5s ease-in-out infinite;
                        }
                    </style>
                    <div
                        x-data="{ tiltX: 0, tiltY: 0 }"
                        @mousemove="
                            const rect = $el.getBoundingClientRect();
                            const x = ($event.clientX - rect.left) / rect.width - 0.5;
                            const y = ($event.clientY - rect.top) / rect.height - 0.5;
                            tiltX = (-y * 10).toFixed(2);
                            tiltY = (x * 10).toFixed(2);
                        "
                        @mouseleave="tiltX = 0; tiltY = 0"
                        class="relative mx-auto max-w-md lg:max-w-lg hero-mascot-float"
                        style="perspective: 1000px;"
                    >
                        <!-- Background Ambient Aura -->
                        <div class="absolute -inset-6 bg-gradient-to-tr from-sky-400/30 via-blue-300/20 to-indigo-400/30 rounded-full blur-3xl opacity-70 pointer-events-none"></div>

                        <div
                            class="relative rounded-[2.25rem] overflow-hidden shadow-2xl shadow-blue-950/40 border border-white/15 transition-transform duration-200 ease-out"
                            :style="`transform: rotateX(${tiltX}deg) rotateY(${tiltY}deg);`"
                        >
                            <img
                                src="{{ asset('images/hero-3d-mascot.jpg') }}"
                                alt="ShopManager 3D Business Mascot holding POS terminal and shopping bags"
                                class="w-full h-auto object-cover block select-none"
                            />
                        </div>
                    </div>
                </div>

            </div>



        </div>

        <!-- Crisp Clean Wave Bottom Divider -->
        <div class="w-full overflow-hidden leading-none pointer-events-none mt-2">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-8 sm:h-12 lg:h-16 fill-white">
                <path d="M0,0 C150,90 350,-40 500,45 C650,130 900,10 1200,60 L1200,120 L0,120 Z"></path>
            </svg>
        </div>
    </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 2: "ONE SYSTEM, NOT EIGHT APPS" (Hub & Satellites)  -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section id="features" class="pt-14 sm:pt-20 pb-20 sm:pb-28 bg-white overflow-hidden">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-12">
                <!-- Section Header + Carousel Arrows -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
                    <div class="max-w-xl space-y-3 text-center sm:text-left mx-auto sm:mx-0">
                        <h2 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-950 tracking-tight">
                            Everything your shop needs
                        </h2>
                        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                            Swipe through what you can do from one simple place.
                        </p>
                    </div>

                    <div class="flex items-center justify-center sm:justify-end gap-2.5 shrink-0">
                        <button
                            type="button"
                            onclick="scrollFeatureCarousel(-330)"
                            aria-label="Previous feature card"
                            class="w-11 h-11 rounded-full bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 flex items-center justify-center transition shadow-xs cursor-pointer active:scale-95"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button
                            type="button"
                            onclick="scrollFeatureCarousel(330)"
                            aria-label="Next feature card"
                            class="w-11 h-11 rounded-full bg-slate-900 hover:bg-blue-600 text-white flex items-center justify-center transition shadow-sm cursor-pointer active:scale-95"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                @php
                    $featureCards = [
                        [
                            'title' => 'Record Sales Fast',
                            'desc' => 'Sell quickly to walk-in buyers on any phone.',
                            'image' => asset('images/features/record-sales.jpg'),
                            'bg' => 'from-blue-600 to-indigo-700',
                        ],
                        [
                            'title' => 'Print Paper Slips',
                            'desc' => 'Print receipts with a small Bluetooth printer.',
                            'image' => asset('images/features/print-receipt.jpg'),
                            'bg' => 'from-emerald-500 to-teal-700',
                        ],
                        [
                            'title' => 'WhatsApp Receipts',
                            'desc' => 'Send receipts straight to your buyer\'s WhatsApp.',
                            'image' => asset('images/features/whatsapp-receipt.jpg'),
                            'bg' => 'from-teal-500 to-emerald-700',
                        ],
                        [
                            'title' => 'Track Your Goods',
                            'desc' => 'Always know what is left in your shop.',
                            'image' => asset('images/features/track-goods.jpg'),
                            'bg' => 'from-indigo-500 to-purple-700',
                        ],
                        [
                            'title' => 'Cash & Transfer',
                            'desc' => 'Take half cash and half transfer on one sale.',
                            'image' => asset('images/features/cash-transfer.jpg'),
                            'bg' => 'from-amber-500 to-orange-600',
                        ],
                        [
                            'title' => 'Daily Sales Total',
                            'desc' => 'See all money made at the end of each day.',
                            'image' => asset('images/features/daily-sales.jpg'),
                            'bg' => 'from-rose-500 to-pink-600',
                        ],
                        [
                            'title' => 'Online Store Link',
                            'desc' => 'Let buyers pay online with card or transfer.',
                            'image' => asset('images/features/online-store.jpg'),
                            'bg' => 'from-cyan-500 to-blue-600',
                        ],
                        [
                            'title' => 'Multiple Shops',
                            'desc' => 'Check all your shop branches in one place.',
                            'image' => asset('images/features/multiple-shops.jpg'),
                            'bg' => 'from-purple-500 to-indigo-700',
                        ],
                    ];
                @endphp

                <!-- Swipeable Carousel Track -->
                <div
                    id="featureCarouselTrack"
                    class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-4 -mx-4 px-4 sm:mx-0 sm:px-0 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                >
                    @foreach ($featureCards as $card)
                        <div class="snap-start shrink-0 w-[270px] sm:w-[300px] rounded-3xl bg-slate-50/90 hover:bg-white border border-slate-200/70 shadow-sm hover:shadow-xl hover:shadow-slate-900/5 transition duration-300 overflow-hidden group flex flex-col">
                            <div class="relative h-48 overflow-hidden bg-gradient-to-br {{ $card['bg'] }}">
                                <img
                                    src="{{ $card['image'] }}"
                                    alt="{{ $card['title'] }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy"
                                />
                            </div>
                            <div class="p-6 space-y-2">
                                <h3 class="font-bold text-base sm:text-lg text-slate-900">{{ $card['title'] }}</h3>
                                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">{{ $card['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 3: "SET UP IN THREE STEPS"                          -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section class="py-16 sm:py-24 bg-slate-50/60 border-y border-slate-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 sm:space-y-16">
                <!-- Section Header -->
                <div class="text-center max-w-2xl mx-auto space-y-3 sm:space-y-4">
                    <span class="text-xs sm:text-sm font-bold text-blue-600 uppercase tracking-wider">Fast Onboarding</span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl text-slate-950 tracking-tight">
                        Set up in three steps
                    </h2>
                    <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                        From signing up to ringing up your first customer sale in under 10 minutes.
                    </p>
                </div>

                <!-- 3 Steps Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                    <!-- Step 1 -->
                    <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-900/5 space-y-6 flex flex-col justify-between border border-slate-100">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white font-display font-black text-xl flex items-center justify-center">
                                1
                            </div>
                            <h3 class="font-display font-bold text-xl sm:text-2xl text-slate-900">Add your products</h3>
                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                                Use our conversational 5-question wizard to enter items, sizes, colors, and prices in seconds.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 text-xs sm:text-sm font-bold text-blue-600 flex items-center gap-1">
                            <span>Step 1: Inventory catalog</span>
                            <span>&rarr;</span>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-900/5 space-y-6 flex flex-col justify-between border border-slate-100">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white font-display font-black text-xl flex items-center justify-center">
                                2
                            </div>
                            <h3 class="font-display font-bold text-xl sm:text-2xl text-slate-900">Connect your till &amp; staff</h3>
                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                                Pair your mobile phone with any 58mm portable Bluetooth thermal printer and set cashier security passwords.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 text-xs sm:text-sm font-bold text-blue-600 flex items-center gap-1">
                            <span>Step 2: Terminal setup</span>
                            <span>&rarr;</span>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-900/5 space-y-6 flex flex-col justify-between border border-slate-100">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white font-display font-black text-xl flex items-center justify-center">
                                3
                            </div>
                            <h3 class="font-display font-bold text-xl sm:text-2xl text-slate-900">Start selling &amp; balancing</h3>
                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                                Ring up walk-ins, split payments, print slips, send WhatsApp receipts, and balance your daily drawer with 1 click.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 text-xs sm:text-sm font-bold text-blue-600 flex items-center gap-1">
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
        <section id="pricing" class="py-20 sm:py-28 bg-warm-sand">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-14 sm:space-y-16">
                <!-- Section Header -->
                <div class="text-center max-w-xl mx-auto space-y-4">
                    <h2 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-950 tracking-tight">
                        Start free for 14 days
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600">
                        Pick a plan that fits your shop. Cancel anytime.
                    </p>

                    <!-- Billing Toggle Pills -->
                    <div class="mt-3 inline-flex items-center p-1.5 rounded-full bg-white shadow-sm border border-slate-200">
                        <button
                            type="button"
                            id="billingMonthlyBtn"
                            onclick="setBillingCycle('monthly')"
                            class="px-6 py-2 rounded-full text-xs sm:text-sm transition cursor-pointer bg-blue-600 text-white font-bold"
                        >
                            Monthly
                        </button>
                        <button
                            type="button"
                            id="billingAnnualBtn"
                            onclick="setBillingCycle('annual')"
                            class="px-6 py-2 rounded-full text-xs sm:text-sm transition flex items-center gap-1.5 cursor-pointer text-slate-600 font-semibold"
                        >
                            <span>Annually</span>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">-20%</span>
                        </button>
                    </div>
                </div>

                <!-- 3 Pricing Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">

                    <!-- Plan 1: Starter -->
                    <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-900/5 space-y-8 flex flex-col justify-between border border-slate-200/80">
                        <div class="space-y-6">
                            <div>
                                <h3 class="font-display font-bold text-xl text-slate-900">Starter</h3>
                                <p class="text-xs sm:text-sm text-slate-500 mt-1">For small shops</p>
                            </div>

                            <div class="flex flex-wrap items-baseline gap-1.5">
                                <span class="billing-price font-display font-extrabold text-3xl sm:text-4xl text-slate-950 whitespace-nowrap" data-monthly="₦8,000" data-annual="₦6,400">₦8,000</span>
                                <span class="text-xs sm:text-sm text-slate-400 font-medium whitespace-nowrap">/ month</span>
                            </div>

                            <ul class="space-y-4 text-xs sm:text-sm text-slate-600 pt-2">
                                <li class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>POS &amp; online storefront</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Up to 150 products</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>2 staff logins</span>
                                </li>
                            </ul>
                        </div>

                        <a href="https://wa.me/2349046686176?text=I%20want%20to%20subscribe" target="_blank" rel="noopener noreferrer" class="w-full py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-xs sm:text-sm text-center transition">
                            Start Free
                        </a>
                    </div>

                    <!-- Plan 2: Pro (Highlighted) -->
                    <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-2xl shadow-blue-600/10 space-y-8 flex flex-col justify-between relative border-2 border-blue-600">
                        <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-blue-600 text-white text-[11px] font-extrabold uppercase tracking-wider shadow-sm">
                            Popular
                        </span>

                        <div class="space-y-6">
                            <div>
                                <h3 class="font-display font-bold text-xl text-slate-900">Pro</h3>
                                <p class="text-xs sm:text-sm text-slate-500 mt-1">For growing shops</p>
                            </div>

                            <div class="flex flex-wrap items-baseline gap-1.5">
                                <span class="billing-price font-display font-extrabold text-3xl sm:text-4xl text-blue-600 whitespace-nowrap" data-monthly="₦18,000" data-annual="₦14,400">₦18,000</span>
                                <span class="text-xs sm:text-sm text-slate-400 font-medium whitespace-nowrap">/ month</span>
                            </div>

                            <ul class="space-y-4 text-xs sm:text-sm text-slate-600 pt-2">
                                <li class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Unlimited products &amp; staff</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>58mm receipt printing</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Cash &amp; transfer split pay</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Daily sales reports</span>
                                </li>
                            </ul>
                        </div>

                        <a href="https://wa.me/2349046686176?text=I%20want%20to%20subscribe" target="_blank" rel="noopener noreferrer" class="w-full py-3.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm text-center transition shadow-lg shadow-blue-600/25 active:scale-95">
                            Start Free Trial &rarr;
                        </a>
                    </div>

                    <!-- Plan 3: Multi-Store -->
                    <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-900/5 space-y-8 flex flex-col justify-between border border-slate-200/80">
                        <div class="space-y-6">
                            <div>
                                <h3 class="font-display font-bold text-xl text-slate-900">Multi-Store</h3>
                                <p class="text-xs sm:text-sm text-slate-500 mt-1">For multiple branches</p>
                            </div>

                            <div class="flex flex-wrap items-baseline gap-1.5">
                                <span class="billing-price font-display font-extrabold text-3xl sm:text-4xl text-slate-950 whitespace-nowrap" data-monthly="₦45,000" data-annual="₦36,000">₦45,000</span>
                                <span class="text-xs sm:text-sm text-slate-400 font-medium whitespace-nowrap">/ month</span>
                            </div>

                            <ul class="space-y-4 text-xs sm:text-sm text-slate-600 pt-2">
                                <li class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Up to 5 shop branches</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Shared stock &amp; staff payroll</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Custom domain &amp; support</span>
                                </li>
                            </ul>
                        </div>

                        <a href="https://wa.me/2349046686176?text=I%20want%20to%20subscribe" target="_blank" rel="noopener noreferrer" class="w-full py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-xs sm:text-sm text-center transition">
                            Get Started
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 5: "A REAL SHOP. FROM BROWSING TO PAID."            -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section class="py-16 sm:py-24 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 sm:space-y-16">
                <div class="text-center max-w-2xl mx-auto space-y-3 sm:space-y-4">
                    <span class="text-xs sm:text-sm font-bold text-blue-600 uppercase tracking-wider">Fast Checkout Experience</span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl text-slate-950 tracking-tight">
                        A real shop. From browsing to paid.
                    </h2>
                    <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                        Whether walk-in customers tap their cards, transfer to your bank, or pay cash, transactions are confirmed and recorded in under 5 seconds.
                    </p>
                </div>

                <!-- Showcase Media Card -->
                <div class="bg-gradient-to-tr from-slate-900 via-slate-950 to-slate-900 text-white rounded-3xl p-8 sm:p-14 shadow-2xl overflow-hidden relative">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div class="space-y-6">
                            <span class="px-3.5 py-1.5 rounded-full bg-blue-600/30 border border-blue-500/40 text-blue-300 text-xs sm:text-sm font-bold">
                                Mobile POS &bull; Wireless Ready
                            </span>
                            <h3 class="font-display font-bold text-2xl sm:text-3xl lg:text-4xl text-white">
                                Print receipts directly from your smartphone.
                            </h3>
                            <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                                Pair your Android or iOS phone with any 58mm portable Bluetooth thermal printer (Goojprt, MPT-II, Netum). Tap "Print Slip" and watch the receipt feed cleanly past the tear bar. Or share formatted receipts straight to customer WhatsApp chats.
                            </p>
                            <div class="flex items-center gap-3 pt-2">
                                <a href="{{ route('admin.sales.create') }}" class="px-7 py-3.5 rounded-full bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-bold transition">
                                    Open POS Terminal &rarr;
                                </a>
                            </div>
                        </div>

                        <!-- Thermal Receipt Code Sample / Visual Ticket -->
                        <div class="bg-white rounded-2xl p-6 sm:p-7 font-mono text-xs sm:text-sm text-slate-900 shadow-xl max-w-sm mx-auto space-y-3.5 border-t-4 border-slate-900">
                            <div class="text-center space-y-1">
                                <div class="font-extrabold text-sm sm:text-base uppercase tracking-tight">JESSI'S CLASSIQ STORE</div>
                                <div class="text-xs text-slate-500 font-sans">Flagship Boutique &bull; Lagos</div>
                                <div class="text-[10px] sm:text-xs text-slate-400">*** OFFICIAL STORE RECEIPT ***</div>
                            </div>
                            <div class="border-b border-dashed border-slate-300 my-2"></div>
                            <div class="space-y-1 text-xs">
                                <div class="flex justify-between"><span>Receipt:</span><strong>SR-1024</strong></div>
                                <div class="flex justify-between"><span>Date:</span><span>Today, 02:45 PM</span></div>
                                <div class="flex justify-between"><span>Cashier:</span><span>Blessing</span></div>
                            </div>
                            <div class="border-b border-dashed border-slate-300 my-2"></div>
                            <div class="space-y-1">
                                <div class="font-bold text-xs sm:text-sm">Snake-Accent Handbag &amp; Wallet Set</div>
                                <div class="flex justify-between text-xs text-slate-600">
                                    <span>1 x ₦45,000</span>
                                    <strong class="text-black">₦45,000</strong>
                                </div>
                            </div>
                            <div class="border-b border-dashed border-slate-300 my-2"></div>
                            <div class="flex justify-between items-baseline pt-1">
                                <span class="font-bold text-xs sm:text-sm">TOTAL PAID</span>
                                <span class="font-extrabold text-base sm:text-lg text-black font-sans">₦45,000</span>
                            </div>
                            <div class="text-xs text-slate-500">PAYMENT METHOD: SPLIT (Cash + Transfer)</div>
                            <div class="border-b border-dashed border-slate-300 my-2"></div>
                            <div class="text-center text-xs text-slate-400 font-sans">
                                Thank you for your patronage!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 6: "WHAT VENDORS ARE SAYING" (Testimonials)         -->
        <!-- Warm Sand / Beige background matching myshoplet screenshot   -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section id="testimonials" class="py-16 sm:py-24 bg-warm-sand">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                <div class="text-center max-w-xl mx-auto space-y-3 sm:space-y-4">
                    <span class="text-xs sm:text-sm font-bold text-blue-700 uppercase tracking-wider">Vendor Reviews</span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl text-slate-950 tracking-tight">
                        What vendors are saying
                    </h2>
                </div>

                <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-xl shadow-slate-900/5 border border-slate-200/60 text-center space-y-5 max-w-2xl mx-auto">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50 text-blue-700 text-xs font-extrabold uppercase tracking-wider">
                        Coming Soon
                    </span>
                    <h3 class="font-display font-bold text-xl sm:text-2xl text-slate-900">
                        Real shop owner stories are on the way
                    </h3>
                    <p class="text-sm sm:text-base text-slate-500 max-w-md mx-auto leading-relaxed">
                        We are collecting real reviews from shops using ShopManager. Want to be one of our first featured stores?
                    </p>
                    <div class="pt-2">
                        <a
                            href="https://wa.me/2349046686176?text=I%20want%20to%20subscribe"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm transition shadow-lg shadow-blue-600/20"
                        >
                            Get Started on WhatsApp &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 7: "QUESTIONS? ANSWERED." (Interactive FAQ)         -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section id="faq" class="py-16 sm:py-24 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center space-y-3 sm:space-y-4">
                    <span class="text-xs sm:text-sm font-bold text-blue-600 uppercase tracking-wider">Frequently Asked Questions</span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl text-slate-950 tracking-tight">
                        Questions? Answered.
                    </h2>
                    <p class="text-base sm:text-lg text-slate-600">
                        Everything you need to know about setting up and running your boutique.
                    </p>
                </div>

                <div class="divide-y divide-slate-100 border-y border-slate-100">

                    <!-- FAQ 1 -->
                    <div class="py-6">
                        <button
                            type="button"
                            @click="toggleFaq(1)"
                            class="w-full text-left flex items-center justify-between text-base sm:text-lg font-bold text-slate-900 hover:text-blue-600 transition cursor-pointer"
                        >
                            <span>Do I need to buy expensive POS hardware?</span>
                            <span class="text-slate-400 text-xl font-bold" x-text="openFaq === 1 ? '−' : '+'"></span>
                        </button>
                        <div x-show="openFaq === 1" x-transition class="pt-3.5 text-sm sm:text-base text-slate-600 leading-relaxed">
                            No! {{ config('app.platform_name', 'ShopManager') }} runs in the browser on any smartphone, iPad, tablet, or laptop you already own. You don't need dedicated bank terminals or POS hardware contracts.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="py-6">
                        <button
                            type="button"
                            @click="toggleFaq(2)"
                            class="w-full text-left flex items-center justify-between text-base sm:text-lg font-bold text-slate-900 hover:text-blue-600 transition cursor-pointer"
                        >
                            <span>How do wireless thermal printers work with the system?</span>
                            <span class="text-slate-400 text-xl font-bold" x-text="openFaq === 2 ? '−' : '+'"></span>
                        </button>
                        <div x-show="openFaq === 2" x-transition class="pt-3.5 text-sm sm:text-base text-slate-600 leading-relaxed">
                            Our POS receipt slip is formatted strictly for 58mm &amp; 80mm wireless portable Bluetooth thermal printers (like Goojprt PT-210, MPT-II, Netum). It supports direct Android Bluetooth printing via RawBT as well as iOS AirPrint.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="py-6">
                        <button
                            type="button"
                            @click="toggleFaq(3)"
                            class="w-full text-left flex items-center justify-between text-base sm:text-lg font-bold text-slate-900 hover:text-blue-600 transition cursor-pointer"
                        >
                            <span>Can customers pay with split payments (Cash + Bank Transfer)?</span>
                            <span class="text-slate-400 text-xl font-bold" x-text="openFaq === 3 ? '−' : '+'"></span>
                        </button>
                        <div x-show="openFaq === 3" x-transition class="pt-3.5 text-sm sm:text-base text-slate-600 leading-relaxed">
                            Yes! Our terminal has a built-in Split Pay mode. If a customer buys a ₦50,000 dress and pays ₦20,000 cash and ₦30,000 by transfer, both amounts are recorded and clearly shown on the customer's receipt.
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="py-6">
                        <button
                            type="button"
                            @click="toggleFaq(4)"
                            class="w-full text-left flex items-center justify-between text-base sm:text-lg font-bold text-slate-900 hover:text-blue-600 transition cursor-pointer"
                        >
                            <span>How do customer WhatsApp receipts work?</span>
                            <span class="text-slate-400 text-xl font-bold" x-text="openFaq === 4 ? '−' : '+'"></span>
                        </button>
                        <div x-show="openFaq === 4" x-transition class="pt-3.5 text-sm sm:text-base text-slate-600 leading-relaxed">
                            When entering the customer's phone number at checkout, clicking "Share on WhatsApp" directly opens the chat with that customer on your phone or web WhatsApp with their itemized receipt pre-filled and ready to send.
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="py-6">
                        <button
                            type="button"
                            @click="toggleFaq(5)"
                            class="w-full text-left flex items-center justify-between text-base sm:text-lg font-bold text-slate-900 hover:text-blue-600 transition cursor-pointer"
                        >
                            <span>Can I manage multiple branch stores under one account?</span>
                            <span class="text-slate-400 text-xl font-bold" x-text="openFaq === 5 ? '−' : '+'"></span>
                        </button>
                        <div x-show="openFaq === 5" x-transition class="pt-3.5 text-sm sm:text-base text-slate-600 leading-relaxed">
                            Yes! Our multi-tenant architecture allows you to run multiple stores or retail branches with separate inventories, staff accounts, and daily takings reports from one master login.
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 11: FINAL DARK CTA BANNER                           -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section class="py-16 sm:py-24 bg-[#0b0f19] text-white relative overflow-hidden">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8 relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-white text-xs sm:text-sm font-bold">
                    <span>GET STARTED IN 2 MINUTES</span>
                </div>

                <h2 class="font-display font-extrabold text-3xl sm:text-5xl lg:text-6xl tracking-tight leading-tight max-w-3xl mx-auto">
                    Ready to grow your retail business?
                </h2>

                <p class="text-base sm:text-lg text-slate-300 max-w-xl mx-auto leading-relaxed font-normal">
                    Join modern fashion boutiques and retail merchants taking full control of their counter, inventory, and revenue.
                </p>

                <div class="pt-2">
                    <a
                        href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 px-10 py-4 rounded-full bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs sm:text-sm uppercase tracking-wider transition shadow-2xl shadow-blue-600/40 active:scale-95"
                    >
                        <span>Start your free trial</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SECTION 12: CLEAN COMPREHENSIVE FOOTER                      -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <footer class="bg-white border-t border-slate-200/80 py-12 text-slate-500 text-xs sm:text-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
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
                        <p class="text-xs sm:text-sm text-slate-500 max-w-sm leading-relaxed">
                            The modern retail POS and multi-store inventory operating system for African boutiques, fashion merchants, and walk-in retailers.
                        </p>
                    </div>

                    <!-- Col 2: Product -->
                    <div class="space-y-3">
                        <div class="font-bold text-slate-900 uppercase tracking-wider text-xs">Product</div>
                        <ul class="space-y-2 text-xs sm:text-sm">
                            <li><a href="#pricing" class="hover:text-blue-600 transition">Pricing Plans</a></li>
                        </ul>
                    </div>

                    <!-- Col 3: Resources -->
                    <div class="space-y-3">
                        <div class="font-bold text-slate-900 uppercase tracking-wider text-xs">Resources</div>
                        <ul class="space-y-2 text-xs sm:text-sm">
                            <li><a href="{{ route('stores.index') }}" class="hover:text-blue-600 transition">Stores Directory</a></li>
                            <li><a href="#faq" class="hover:text-blue-600 transition">Support &amp; FAQ</a></li>
                        </ul>
                    </div>

                </div>

                <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-slate-400 text-xs">
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
        <!-- FLOATING BOTTOM CONVERSION PILL                             -->
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
                        <div class="font-bold text-xs sm:text-sm text-white truncate">Open your free store in 2 mins</div>
                        <div class="text-xs text-slate-400 flex items-center gap-1.5">
                            <span class="text-amber-400">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                            <span>Rated 4.9/5 by 300+ merchants</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a
                        href="{{ route('register') }}"
                        class="px-4 py-2 rounded-full bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs uppercase tracking-wider transition active:scale-95"
                    >
                        Try free &rarr;
                    </a>
                    <button
                        type="button"
                        @click="dismissed = true; showStickyPill = false"
                        class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer"
                        title="Dismiss"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- HOVERING WHATSAPP SUPPORT ICON                              -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <a
            href="https://wa.me/2349046686176?text=Hello%2C%20I%20need%20support"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="WhatsApp Support"
            class="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full bg-[#25D366] hover:bg-[#1ebe5d] text-white shadow-2xl shadow-emerald-950/30 flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95 group"
            title="Chat on WhatsApp"
        >
            <svg class="w-7 h-7 fill-current" viewBox="0 0 32 32" aria-hidden="true">
                <path d="M16.004 3C8.834 3 3 8.834 3 16.004c0 2.295.603 4.534 1.747 6.506L3 29l6.654-1.744A12.956 12.956 0 0016.004 29C23.174 29 29 23.166 29 16.004 29 8.834 23.174 3 16.004 3zm0 23.62c-2.01 0-3.984-.54-5.71-1.56l-.41-.243-4.248 1.113 1.135-4.14-.267-.426a10.562 10.562 0 01-1.622-5.36c0-5.858 4.765-10.623 10.622-10.623 5.857 0 10.623 4.765 10.623 10.623 0 5.857-4.766 10.622-10.623 10.622zm5.826-7.957c-.32-.16-1.89-.932-2.183-1.038-.293-.107-.506-.16-.72.16-.213.32-.826 1.038-1.013 1.25-.186.214-.373.24-.692.08-.32-.16-1.348-.497-2.568-1.585-.949-.846-1.59-1.89-1.776-2.21-.186-.32-.02-.493.14-.652.144-.143.32-.373.48-.56.16-.186.213-.32.32-.533.106-.213.053-.4-.027-.56-.08-.16-.72-1.732-.986-2.37-.26-.624-.524-.54-.72-.55l-.613-.01c-.213 0-.56.08-.853.4-.293.32-1.12 1.093-1.12 2.665 0 1.573 1.146 3.092 1.306 3.305.16.214 2.256 3.445 5.465 4.83.763.33 1.358.527 1.823.675.766.243 1.463.209 2.014.127.614-.092 1.89-.773 2.156-1.519.267-.746.267-1.386.187-1.519-.08-.133-.293-.213-.613-.373z"/>
            </svg>
        </a>

        <script>
            function scrollFeatureCarousel(offset) {
                var track = document.getElementById('featureCarouselTrack');
                if (track && track.scrollBy) {
                    track.scrollBy({ left: offset, behavior: 'smooth' });
                } else if (track) {
                    track.scrollLeft += offset;
                }
            }

            function setBillingCycle(cycle) {
                var isAnnual = cycle === 'annual';
                var monthlyBtn = document.getElementById('billingMonthlyBtn');
                var annualBtn = document.getElementById('billingAnnualBtn');

                if (monthlyBtn && annualBtn) {
                    monthlyBtn.className = 'px-6 py-2 rounded-full text-xs sm:text-sm transition cursor-pointer ' +
                        (isAnnual ? 'text-slate-600 font-semibold' : 'bg-blue-600 text-white font-bold');
                    annualBtn.className = 'px-6 py-2 rounded-full text-xs sm:text-sm transition flex items-center gap-1.5 cursor-pointer ' +
                        (isAnnual ? 'bg-blue-600 text-white font-bold' : 'text-slate-600 font-semibold');
                }

                var prices = document.querySelectorAll('.billing-price');
                for (var i = 0; i < prices.length; i++) {
                    prices[i].textContent = isAnnual ? prices[i].getAttribute('data-annual') : prices[i].getAttribute('data-monthly');
                }
            }

            function getActiveTranslateLang() {
                var match = document.cookie.match(/(?:^|;\s*)googtrans=\/en\/([^;]+)/);
                return match && match[1] ? decodeURIComponent(match[1]) : 'en';
            }

            function setGoogTransCookie(lang) {
                var val = lang === 'en' ? '' : '/en/' + lang;
                var expires = lang === 'en' ? 'Thu, 01 Jan 1970 00:00:00 GMT' : '';
                var cookieBase = 'googtrans=' + val + '; path=/;' + (expires ? ' expires=' + expires + ';' : '');
                document.cookie = cookieBase;
                document.cookie = cookieBase + ' domain=.' + window.location.hostname + ';';
            }

            function applyGoogleTranslation(lang) {
                var combo = document.querySelector('.goog-te-combo');
                if (combo) {
                    combo.value = lang === 'en' ? '' : lang;
                    combo.dispatchEvent(new Event('change'));
                    return true;
                }
                return false;
            }

            function changeSiteLanguage(lang) {
                setGoogTransCookie(lang);
                if (!applyGoogleTranslation(lang)) {
                    window.location.reload();
                } else if (lang === 'en') {
                    window.location.reload();
                }
            }

            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'en',
                    includedLanguages: 'en,ha,yo,ig,fr,ar,es,pt,zh-CN',
                    autoDisplay: false
                }, 'google_translate_element');

                var currentLang = getActiveTranslateLang();
                var switcher = document.getElementById('langSwitcher');
                if (switcher) {
                    switcher.value = currentLang;
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                var switcher = document.getElementById('langSwitcher');
                if (switcher) {
                    switcher.value = getActiveTranslateLang();
                }
            });
        </script>
        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SAMSUNG-STYLE EDGE HANDLE + SLIDE-OUT STUDY PANEL (SIA 2027)-->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- Right-Edge Handle Bar (highlights for 4s on load, then settles into slim edge tab) -->
        <button
            type="button"
            id="siaEdgeHandle"
            onclick="openSiaEdgePanel()"
            aria-label="Open SIA 2027 Linear Budgeting System study"
            class="fixed right-0 top-1/2 -translate-y-1/2 z-[90] flex items-center gap-2.5 pl-2.5 pr-2 py-3 rounded-l-2xl bg-blue-600/95 hover:bg-blue-700 text-white shadow-2xl shadow-blue-950/30 border border-r-0 border-white/30 backdrop-blur-md transition-all duration-500 cursor-pointer ring-4 ring-blue-400/60"
        >
            <!-- Samsung-style vertical grip indicator -->
            <span class="w-1 h-10 rounded-full bg-white/80 shrink-0"></span>

            <!-- Highlight label (shown for 4s on load, then collapses) -->
            <span id="siaEdgePeek" class="flex items-center gap-2 overflow-hidden transition-all duration-500 max-w-[190px] opacity-100">
                <img
                    src="{{ asset('images/sia-2027.jpg') }}"
                    alt="SIA 2027"
                    class="w-7 h-7 rounded-full object-cover border border-white/60 shrink-0"
                />
                <span class="text-left whitespace-nowrap pr-1">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-blue-100 leading-none">SIA 2027</span>
                    <span class="block text-xs font-bold text-white leading-tight mt-0.5">Linear Budgeting</span>
                </span>
            </span>
        </button>

        <!-- Backdrop -->
        <div
            id="siaEdgeBackdrop"
            onclick="closeSiaEdgePanel()"
            class="fixed inset-0 z-[95] bg-slate-950/60 backdrop-blur-xs opacity-0 pointer-events-none transition-opacity duration-300"
        ></div>

        <!-- Slide-Out Right Edge Drawer -->
        <aside
            id="siaEdgePanel"
            role="dialog"
            aria-modal="true"
            aria-labelledby="siaPopupTitle"
            class="fixed top-0 right-0 z-[100] h-full w-full max-w-md sm:max-w-lg bg-white shadow-2xl border-l border-slate-200 flex flex-col translate-x-full transition-transform duration-300 ease-out"
        >
            <!-- Sticky Header -->
            <div class="flex items-center justify-between gap-4 px-5 sm:px-6 py-4 border-b border-slate-100 bg-slate-50/95 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <img
                        src="{{ asset('images/sia-2027.jpg') }}"
                        alt="Senator Ishaku Abbo - SIA"
                        class="w-10 h-10 rounded-full object-cover border border-blue-200 shrink-0"
                    />
                    <div class="min-w-0">
                        <span class="inline-block text-[10px] font-extrabold uppercase tracking-wider text-blue-700">
                            Adamawa State 2027 &bull; Policy Study
                        </span>
                        <h2 id="siaPopupTitle" class="font-display font-extrabold text-sm sm:text-base text-slate-950 truncate">
                            The Linear Budgeting System
                        </h2>
                    </div>
                </div>

                <button
                    type="button"
                    onclick="closeSiaEdgePanel()"
                    aria-label="Close panel"
                    class="w-9 h-9 rounded-full bg-white hover:bg-slate-200 text-slate-600 hover:text-slate-950 border border-slate-200 flex items-center justify-center transition shrink-0 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto p-5 sm:p-6 space-y-5 text-slate-700 flex-1">
                <!-- Portrait + Opening Statement -->
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 bg-blue-50/60 border border-blue-100 rounded-2xl p-4 sm:p-5">
                    <img
                        src="{{ asset('images/sia-2027.jpg') }}"
                        alt="Senator Ishaku Abbo - SIA"
                        class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover shadow-md shrink-0"
                    />
                    <div class="space-y-2 text-center sm:text-left">
                        <p class="font-display font-extrabold text-base sm:text-lg text-slate-950 leading-snug">
                            &ldquo;By 2027, when I am elected Governor of Adamawa State, I will change how we do budgeting in this country.&rdquo;
                        </p>
                        <p class="text-xs sm:text-sm text-blue-800 font-semibold">
                            Each of the 226 wards in Adamawa State will be budgeted &#8358;1,000,000,000.00 (One Billion Naira) for capital projects in each ward.
                        </p>
                    </div>
                </div>

                <!-- Clean Visual Budget Breakdown Cards -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">2026 Budget</div>
                        <div class="font-display font-extrabold text-lg text-slate-950">&#8358;753B</div>
                        <div class="text-[11px] text-slate-500">Incl. &#8358;169B supplementary</div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Recurrent</div>
                        <div class="font-display font-extrabold text-lg text-slate-950">&#8358;209B</div>
                        <div class="text-[11px] text-slate-500">Personnel &amp; overhead</div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 space-y-1">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-blue-700">226 Wards (&times;&#8358;1B)</div>
                        <div class="font-display font-extrabold text-lg text-blue-700">&#8358;226B</div>
                        <div class="text-[11px] text-blue-700/80">Out of &#8358;544B capital</div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-1">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Excess Left</div>
                        <div class="font-display font-extrabold text-lg text-emerald-700">&#8358;318B</div>
                        <div class="text-[11px] text-emerald-700/80">Legacy &amp; industry</div>
                    </div>
                </div>

                <!-- Full Message Body -->
                <div class="space-y-3 text-sm leading-relaxed text-slate-700">
                    <p>
                        This year, 2026, the Adamawa State budget is seven hundred and fifty-three billion Naira (&#8358;753,000,000,000.00), including the &#8358;169,000,000,000.00 billion supplementary budget.
                    </p>
                    <p>
                        Out of this, &#8358;209,000,000,000.00 billion is recurrent expenditure (personnel, overhead and others), and &#8358;544,000,000,000.00 billion (including the supplementary budget) was budgeted for capital projects.
                    </p>
                    <p>
                        Imagine if we budget one billion Naira (&#8358;1,000,000,000.00) to each ward. That is &#8358;226,000,000,000.00 billion only out of the &#8358;544,000,000,000.00 billion capital budget for development in our 226 wards. We still have an excess of &#8358;318,000,000,000.00 billion to finance legacy projects and industrialization in Adamawa State.
                    </p>
                    <p class="font-medium text-slate-900 bg-slate-50 border-l-4 border-blue-600 pl-4 py-2.5 rounded-r-xl">
                        Now, this is what I call the <strong>Linear Budgeting System</strong>, which will be implemented by the Ward Development Committee of each ward, supervised by the Ministry of Rural Development and Urban Renewal.
                    </p>
                    <p>
                        I know that when someone is very intelligent and operating on a very high frequency, it is difficult to understand him. If you are against giving money back to the villages for development, you need help.
                    </p>
                    <p>
                        This is the 21st century. Vote for an intelligent and bold leader. Vote for SIA as Governor of Adamawa State. It is a blessing to have a practical leader as Governor of Adamawa State. I am very practical.
                    </p>
                </div>

                <!-- Signature & Close CTA -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4">
                    <div>
                        <div class="font-display font-extrabold text-sm text-slate-950">
                            Senator Ishaku Abbo &mdash; SIA
                        </div>
                        <div class="text-xs text-slate-500">
                            Governor, Adamawa State 2027
                        </div>
                    </div>

                    <button
                        type="button"
                        onclick="closeSiaEdgePanel()"
                        class="px-5 py-2.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-md cursor-pointer active:scale-95"
                    >
                        Close
                    </button>
                </div>
            </div>
        </aside>

        <div id="gt-wrapper" style="position:fixed;left:-9999px;top:-9999px;visibility:hidden;pointer-events:none;" aria-hidden="true">
            <div id="google_translate_element"></div>
        </div>
        <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>
        <script>
            function openSiaEdgePanel() {
                var panel = document.getElementById('siaEdgePanel');
                var backdrop = document.getElementById('siaEdgeBackdrop');
                if (panel) {
                    panel.classList.remove('translate-x-full');
                }
                if (backdrop) {
                    backdrop.classList.remove('opacity-0', 'pointer-events-none');
                }
            }

            function closeSiaEdgePanel() {
                var panel = document.getElementById('siaEdgePanel');
                var backdrop = document.getElementById('siaEdgeBackdrop');
                if (panel) {
                    panel.classList.add('translate-x-full');
                }
                if (backdrop) {
                    backdrop.classList.add('opacity-0', 'pointer-events-none');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Highlight the Samsung-style edge bar for 4 seconds, then collapse to slim edge tab
                setTimeout(function() {
                    var handle = document.getElementById('siaEdgeHandle');
                    var peek = document.getElementById('siaEdgePeek');
                    if (handle) {
                        handle.classList.remove('ring-4', 'ring-blue-400/60', 'pl-2.5', 'pr-2');
                        handle.classList.add('px-2', 'opacity-85', 'hover:opacity-100');
                    }
                    if (peek) {
                        peek.classList.remove('max-w-[190px]', 'opacity-100');
                        peek.classList.add('max-w-0', 'opacity-0');
                    }
                }, 4000);

                // Support swipe-left on edge handle to slide panel open
                var handleEl = document.getElementById('siaEdgeHandle');
                var touchStartX = null;
                if (handleEl) {
                    handleEl.addEventListener('touchstart', function(e) {
                        if (e.touches && e.touches[0]) {
                            touchStartX = e.touches[0].clientX;
                        }
                    }, { passive: true });
                    handleEl.addEventListener('touchend', function(e) {
                        if (touchStartX !== null && e.changedTouches && e.changedTouches[0]) {
                            if (touchStartX - e.changedTouches[0].clientX > 20) {
                                openSiaEdgePanel();
                            }
                        }
                        touchStartX = null;
                    }, { passive: true });
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSiaEdgePanel();
                }
            });
        </script>
    </body>
</html>
