<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.platform_name', 'ShopManager') }} &mdash; Multi-Store Retail &amp; Inventory Operating System</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                            display: ['Syne', '"Plus Jakarta Sans"', 'sans-serif'],
                        }
                    }
                }
            }
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#0b0f19] text-slate-100 min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">

        <!-- Ambient Background Glow -->
        <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
            <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[800px] h-[450px] bg-gradient-to-b from-indigo-600/20 via-violet-600/10 to-transparent rounded-full blur-[100px]"></div>
        </div>

        <!-- Navigation Bar -->
        <header class="w-full bg-[#0b0f19]/80 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20 gap-4">
                    <!-- Brand -->
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/30">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <span class="font-display font-bold text-xl tracking-tight text-white group-hover:text-indigo-200 transition">
                            {{ config('app.platform_name', 'ShopManager') }}
                        </span>
                    </a>

                    <!-- Auth Actions -->
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider transition shadow-md shadow-indigo-600/20">
                                Dashboard &rarr;
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-semibold text-slate-300 hover:text-white transition">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider transition shadow-md shadow-indigo-600/25">
                                Launch Store
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow relative z-10">

            <!-- Hero Section -->
            <section class="pt-20 pb-16 sm:pt-28 sm:pb-20 text-center">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                    <!-- Pill Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-medium">
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                        <span>Multi-Tenant Retail Operating System</span>
                    </div>

                    <!-- Clean Headline -->
                    <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-[1.1]">
                        Unified Retail, Inventory
                        <span class="block text-indigo-400">&amp; Store Operations.</span>
                    </h1>

                    <!-- Concise Subtitle -->
                    <p class="text-base sm:text-lg text-slate-400 max-w-xl mx-auto leading-relaxed">
                        Point-of-sale checkout, multi-location stock control, automated staff payroll, and digitized paper ledgers in one isolated workspace.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3.5 pt-3">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider transition shadow-lg shadow-indigo-600/30">
                            Launch Your Store &rarr;
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 text-xs font-bold uppercase tracking-wider transition">
                            Platform Login
                        </a>
                    </div>
                </div>
            </section>

            <!-- Active Stores Directory -->
            @php
                $activeStores = \App\Models\Tenant::where('status', 'active')->latest()->get();
            @endphp
            <section class="py-14 bg-slate-900/40 border-y border-slate-800/80">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-800">
                        <div>
                            <h2 class="font-display font-bold text-lg text-white">Active Stores</h2>
                            <p class="text-xs text-slate-400">Launch a live store or log in to its management console.</p>
                        </div>
                        <a href="{{ route('register') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition">
                            + Onboard New Store
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($activeStores as $store)
                            <div class="p-5 rounded-xl bg-slate-900/90 border border-slate-800 flex flex-col justify-between gap-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-indigo-600/20 border border-indigo-500/30 text-indigo-300 flex items-center justify-center font-bold text-base">
                                            {{ strtoupper(substr($store->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-white text-base leading-tight">{{ $store->name }}</h3>
                                            <span class="text-xs text-slate-400 font-mono">{{ $store->slug }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Active
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 pt-2 border-t border-slate-800/80">
                                    @if ($store->hasFeature('storefront'))
                                        <a href="{{ url('/?tenant=' . $store->slug) }}" class="flex-1 py-2 px-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold text-center transition">
                                            Storefront &nearr;
                                        </a>
                                    @endif
                                    <a href="{{ url('/admin?tenant=' . $store->slug) }}" class="flex-1 py-2 px-3 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold text-center transition">
                                        Admin Console &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Three Core Pillars (Clean & Minimal) -->
            <section class="py-16 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Feature 1 -->
                    <div class="p-6 rounded-xl bg-slate-900/50 border border-slate-800 space-y-2">
                        <div class="w-9 h-9 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-white text-sm">Multi-Store &amp; Inventory</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Isolated databases with real-time stock sync across physical retail branches and web storefronts.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 rounded-xl bg-slate-900/50 border border-slate-800 space-y-2">
                        <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-white text-sm">Automated Payroll</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            1-click salary disbursements to Nigerian staff bank accounts via Paystack Transfers API.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 rounded-xl bg-slate-900/50 border border-slate-800 space-y-2">
                        <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-white text-sm">Paper Ledger Digitize</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            OCR engine scans handwritten sales receipts and ledger pages into verified digital entries.
                        </p>
                    </div>
                </div>
            </section>
        </main>

        <!-- Clean Footer -->
        <footer class="bg-slate-950 border-t border-slate-800/80 py-8 relative z-10 text-xs text-slate-500">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="font-medium text-slate-400">&copy; {{ date('Y') }} {{ config('app.platform_name', 'ShopManager') }}. All rights reserved.</span>
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="hover:text-slate-300 transition">Sign In</a>
                    <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold transition">Get Started</a>
                </div>
            </div>
        </footer>
    </body>
</html>
