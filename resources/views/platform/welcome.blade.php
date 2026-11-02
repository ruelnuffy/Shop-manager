<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.platform_name', 'ShopManager') }} &mdash; Multi-Store Retail &amp; Inventory Operating System</title>

        <!-- Modern Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Tailwind CSS & Engine Config -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
                            display: ['Syne', '"Plus Jakarta Sans"', 'sans-serif'],
                            mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                        },
                        colors: {
                            brand: {
                                50: '#eef2ff',
                                100: '#e0e7ff',
                                200: '#c7d2fe',
                                400: '#818cf8',
                                500: '#6366f1',
                                600: '#4f46e5',
                                700: '#4338ca',
                                800: '#3730a3',
                                900: '#312e81',
                            }
                        },
                        boxShadow: {
                            'glow-indigo': '0 0 50px -10px rgba(99, 102, 241, 0.35)',
                            'glow-violet': '0 0 60px -15px rgba(139, 92, 246, 0.3)',
                        }
                    }
                }
            }
        </script>

        <!-- Vite Assets (App bundle & styles) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#0b0f19] text-slate-100 selection:bg-indigo-500 selection:text-white min-h-screen flex flex-col relative overflow-x-hidden">

        <!-- Subtle Ambient Background Glows & Grid Mesh -->
        <div class="fixed inset-0 pointer-events-none z-0">
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b12_1px,transparent_1px),linear-gradient(to_bottom,#1e293b12_1px,transparent_1px)] bg-[size:32px_32px]"></div>
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[550px] bg-gradient-to-b from-indigo-600/15 via-violet-600/10 to-transparent rounded-full blur-[120px]"></div>
            <div class="absolute top-[40%] right-[-10%] w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[140px]"></div>
            <div class="absolute bottom-[20%] left-[-10%] w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[150px]"></div>
        </div>

        <!-- ========================================== -->
        <!-- Platform Top Navigation Header              -->
        <!-- ========================================== -->
        <header class="w-full bg-[#0b0f19]/80 backdrop-blur-xl border-b border-slate-800/80 sticky top-0 z-50 transition-all">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20 gap-4">

                    <!-- Brand Identity Mark -->
                    <a href="{{ route('home') }}" class="flex items-center gap-3.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-violet-500 text-white flex items-center justify-center shadow-lg shadow-indigo-600/25 border border-indigo-400/30 group-hover:scale-105 transition duration-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-display font-bold text-xl tracking-tight text-white group-hover:text-indigo-200 transition">
                                    {{ config('app.platform_name', 'ShopManager') }}
                                </span>
                                <span class="hidden sm:inline-block px-1.5 py-0.5 text-[10px] font-mono font-semibold uppercase tracking-wider rounded bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                                    v2.4
                                </span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium tracking-wide -mt-0.5">Retail Operating System</span>
                        </div>
                    </a>

                    <!-- Navigation Links -->
                    <nav class="hidden md:flex items-center gap-1 lg:gap-2 text-xs font-semibold text-slate-300">
                        <a href="#features" class="px-3.5 py-2 rounded-lg hover:text-white hover:bg-slate-800/60 transition">
                            Capabilities
                        </a>
                        <a href="#multi-tenant" class="px-3.5 py-2 rounded-lg hover:text-white hover:bg-slate-800/60 transition">
                            Active Stores
                        </a>
                        <a href="#payroll" class="px-3.5 py-2 rounded-lg hover:text-white hover:bg-slate-800/60 transition">
                            Automated Payroll
                        </a>
                        <a href="#ledger" class="px-3.5 py-2 rounded-lg hover:text-white hover:bg-slate-800/60 transition">
                            OCR Ledger Digitize
                        </a>
                        <a href="#pricing" class="px-3.5 py-2 rounded-lg hover:text-white hover:bg-slate-800/60 transition">
                            Pricing
                        </a>
                    </nav>

                    <!-- System Status Indicator & Auth Actions -->
                    <div class="flex items-center gap-3">
                        <!-- Uptime Pill -->
                        <div class="hidden xl:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900/90 border border-slate-800 text-[11px] font-medium text-slate-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>System Operational</span>
                        </div>

                        @auth
                            <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold uppercase tracking-wider transition shadow-lg shadow-indigo-600/30 flex items-center gap-2">
                                <span>Go To Dashboard</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800/50 rounded-lg transition">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" class="px-4.5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold uppercase tracking-wider transition shadow-lg shadow-indigo-600/25 border border-indigo-400/20 flex items-center gap-2">
                                <span>Launch Store</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- ========================================== -->
        <!-- Main Hero Section                           -->
        <!-- ========================================== -->
        <main class="flex-grow relative z-10">

            <!-- Hero Intro -->
            <section class="pt-16 sm:pt-24 pb-16 lg:pb-24">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">

                    <!-- Announcement Pill -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900/90 border border-indigo-500/30 text-slate-300 text-xs font-medium shadow-md hover:border-indigo-500/60 transition group cursor-pointer">
                        <span class="px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 text-[10px] font-bold uppercase tracking-wider">New</span>
                        <span>Automated Paystack Transfers &amp; OCR Ledger Digitize</span>
                        <svg class="w-3.5 h-3.5 text-indigo-400 group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- Main Hero Headline -->
                    <div class="space-y-4 max-w-4xl mx-auto">
                        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-white tracking-tight leading-[1.08]">
                            Unified Retail, Inventory
                            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-violet-300 to-sky-300">
                                &amp; Store Operations.
                            </span>
                        </h1>
                        <p class="text-base sm:text-xl text-slate-400 max-w-2xl mx-auto font-normal leading-relaxed">
                            The enterprise-grade operating system for modern boutique merchants and multi-branch retail outlets. Manage real-time stock, omnichannel POS checkout, automated staff payroll, and digitized paper ledgers in one isolated tenant workspace.
                        </p>
                    </div>

                    <!-- Hero Call to Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-500 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-sm font-bold tracking-wide transition shadow-glow-indigo border border-indigo-400/30 flex items-center justify-center gap-2.5">
                            <span>Get Started With ShopManager</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="#multi-tenant" class="w-full sm:w-auto px-7 py-4 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-slate-200 hover:text-white border border-slate-700/80 hover:border-slate-600 text-sm font-semibold tracking-wide transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Explore Active Boutiques</span>
                        </a>
                    </div>

                    <!-- Micro-Metric Badges -->
                    <div class="pt-8 border-t border-slate-800/80 max-w-3xl mx-auto grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
                        <div>
                            <div class="text-2xl font-extrabold text-white font-mono">100%</div>
                            <div class="text-xs text-slate-400 font-medium">Tenant Isolation</div>
                        </div>
                        <div>
                            <div class="text-2xl font-extrabold text-white font-mono">₦0</div>
                            <div class="text-xs text-slate-400 font-medium">Reconcile Discrepancy</div>
                        </div>
                        <div>
                            <div class="text-2xl font-extrabold text-white font-mono">Instant</div>
                            <div class="text-xs text-slate-400 font-medium">NUBAN Transfers</div>
                        </div>
                        <div>
                            <div class="text-2xl font-extrabold text-white font-mono">Omnichannel</div>
                            <div class="text-xs text-slate-400 font-medium">In-Store &amp; Online</div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- Hero Product Frame / UI Showcase Mockup    -->
                <!-- ========================================== -->
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 sm:pt-16">
                    <div class="relative rounded-2xl bg-gradient-to-b from-slate-800/70 to-slate-900/90 p-2 sm:p-3 border border-slate-700/60 shadow-2xl shadow-indigo-950/50 backdrop-blur-xl">

                        <!-- Mockup Top Window Chrome -->
                        <div class="flex items-center justify-between px-4 py-3 bg-slate-950/90 rounded-t-xl border-b border-slate-800/90">
                            <!-- macOS Window Dots -->
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
                            </div>

                            <!-- Fake Browser URL / Workspace Pill -->
                            <div class="hidden sm:flex items-center gap-2 px-4 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-xs font-mono text-slate-300">
                                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>app.shopmanager.test / atelier / <strong>jessis-classiq-store</strong></span>
                            </div>

                            <!-- Active Store Badge -->
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-300">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span class="hidden md:inline">Jos Boutique &bull; Live POS</span>
                            </div>
                        </div>

                        <!-- Mockup Inner Dashboard UI -->
                        <div class="p-4 sm:p-6 bg-slate-950/80 rounded-b-xl space-y-6">

                            <!-- Mockup KPI Row -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 space-y-1">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Today's Revenue</span>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white font-mono">₦1,425,800</div>
                                    <div class="text-[11px] text-emerald-400 font-semibold flex items-center gap-1">
                                        <span>&uarr; 18.4%</span>
                                        <span class="text-slate-500 font-normal">vs last week</span>
                                    </div>
                                </div>
                                <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 space-y-1">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Active Outlets</span>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white font-mono">2 Branches</div>
                                    <div class="text-[11px] text-indigo-400 font-medium">Jos Flagship &amp; Lagos</div>
                                </div>
                                <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 space-y-1">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Payroll Disbursement</span>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white font-mono">₦600,000</div>
                                    <div class="text-[11px] text-emerald-400 font-semibold flex items-center gap-1">
                                        <span>&check; 3 Staff Paid</span>
                                        <span class="text-slate-500 font-normal">via Paystack</span>
                                    </div>
                                </div>
                                <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 space-y-1">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">OCR Ledgers</span>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white font-mono">4 Verified</div>
                                    <div class="text-[11px] text-amber-400 font-medium">Zero Variance</div>
                                </div>
                            </div>

                            <!-- Mockup Two-Column Canvas: POS Stream & OCR Scanner Pipeline -->
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 text-left">

                                <!-- Left Column: Real-Time POS Sales Feed (7 Cols) -->
                                <div class="lg:col-span-7 p-5 rounded-xl bg-slate-900/90 border border-slate-800 space-y-4">
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></div>
                                            <h4 class="text-xs font-bold uppercase tracking-wider text-white">Live Retail Sales &amp; POS Stream</h4>
                                        </div>
                                        <span class="text-[11px] font-mono text-slate-400">Omnichannel Sync</span>
                                    </div>

                                    <div class="space-y-2.5">
                                        <!-- Sale Item 1 -->
                                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-950/70 border border-slate-800/80 hover:border-slate-700 transition">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-500/15 border border-indigo-500/30 text-indigo-400 flex items-center justify-center font-bold text-xs">
                                                    01
                                                </div>
                                                <div>
                                                    <div class="text-xs font-bold text-white">Gold-Tone Sovereign Evening Dress</div>
                                                    <div class="text-[11px] text-slate-400">Jos Boutique &bull; SKU: GSD-001 &bull; Qty: 1</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs font-bold font-mono text-white">₦185,000</div>
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">
                                                    Paid POS
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Sale Item 2 -->
                                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-950/70 border border-slate-800/80 hover:border-slate-700 transition">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-violet-500/15 border border-violet-500/30 text-violet-400 flex items-center justify-center font-bold text-xs">
                                                    02
                                                </div>
                                                <div>
                                                    <div class="text-xs font-bold text-white">Monogram Leather Crossbody Clutch</div>
                                                    <div class="text-[11px] text-slate-400">Lagos Showroom &bull; SKU: MCC-004 &bull; Qty: 1</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs font-bold font-mono text-white">₦95,000</div>
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-indigo-500/15 text-indigo-400 border border-indigo-500/20">
                                                    Paystack Card
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: OCR Ledger Reconciliation Pipeline (5 Cols) -->
                                <div class="lg:col-span-5 p-5 rounded-xl bg-slate-900/90 border border-slate-800 space-y-4">
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <h4 class="text-xs font-bold uppercase tracking-wider text-white">Paper Ledger OCR Ingestion</h4>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">
                                            Matched
                                        </span>
                                    </div>

                                    <div class="p-3.5 rounded-lg bg-slate-950/80 border border-slate-800 space-y-3 font-mono text-[11px]">
                                        <div class="text-slate-400 flex items-center justify-between">
                                            <span>Document: Daily_Book_Oct18.png</span>
                                            <span class="text-indigo-400">99.8% Conf.</span>
                                        </div>
                                        <div class="p-2.5 rounded bg-slate-900 border border-slate-800/80 text-slate-300 space-y-1">
                                            <div class="text-[10px] text-slate-500 uppercase tracking-wider font-sans font-bold">Extracted Ledger Rows</div>
                                            <div class="flex justify-between">
                                                <span>&bull; Silk Scarf (Jos Boutique)</span>
                                                <span class="text-white font-bold">₦35,000</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>&bull; Velvet Tailored Blazer</span>
                                                <span class="text-white font-bold">₦140,000</span>
                                            </div>
                                        </div>
                                        <div class="text-emerald-400 text-[10px] font-sans font-semibold flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>Synced to Central Sales Ledger with zero variance</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- Active Tenant Stores Directory             -->
            <!-- ========================================== -->
            @php
                $activeStores = \App\Models\Tenant::where('status', 'active')->latest()->get();
            @endphp
            <section id="multi-tenant" class="py-20 bg-slate-900/40 border-y border-slate-800/80">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

                    <!-- Section Header -->
                    <div class="text-center max-w-3xl mx-auto space-y-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-semibold">
                            <span>Isolated Workspaces</span>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                            Active Stores Powered by {{ config('app.platform_name', 'ShopManager') }}
                        </h2>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Each merchant gets complete data isolation, customized branding, localized outlets, independent staff rosters, and their own Paystack gateway keys.
                        </p>
                    </div>

                    <!-- Store Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($activeStores as $store)
                            <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-indigo-500/40 transition duration-200 flex flex-col justify-between gap-6 group hover:shadow-xl hover:shadow-indigo-950/30">
                                <div class="space-y-4">
                                    <!-- Store Card Header -->
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white flex items-center justify-center font-bold text-lg shadow-md">
                                            {{ strtoupper(substr($store->name, 0, 1)) }}
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                Active Tenant
                                            </span>
                                            <div class="text-[11px] font-mono text-slate-500 mt-1">{{ $store->slug }}</div>
                                        </div>
                                    </div>

                                    <!-- Store Meta -->
                                    <div class="space-y-1.5">
                                        <h3 class="font-display font-bold text-xl text-white tracking-tight group-hover:text-indigo-200 transition">
                                            {{ $store->name }}
                                        </h3>
                                        <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">
                                            {{ $store->setting('branding.tagline', 'Modern boutique retail and multi-outlet catalog.') }}
                                        </p>
                                    </div>

                                    <!-- Store Outlets Snapshot -->
                                    <div class="pt-3 border-t border-slate-800/80 flex items-center gap-2 text-xs text-slate-400">
                                        <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>
                                            {{ $store->slug === 'jessis-classiq-store' ? '2 Outlets (Jos Flagship & Lagos)' : 'Multi-Outlet Workspace' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2.5 pt-4 border-t border-slate-800">
                                    @if ($store->hasFeature('storefront'))
                                        <a href="{{ url('/?tenant=' . $store->slug) }}" class="flex-1 py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-semibold text-center transition border border-slate-700/60">
                                            Visit Storefront &nearr;
                                        </a>
                                    @endif
                                    <a href="{{ url('/admin?tenant=' . $store->slug) }}" class="flex-1 py-2.5 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold text-center transition shadow-md shadow-indigo-600/20 flex items-center justify-center gap-1.5">
                                        <span>Console</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach

                        <!-- Launch New Store Workspace Card -->
                        <div class="p-6 rounded-2xl bg-indigo-950/20 border-2 border-dashed border-indigo-500/30 hover:border-indigo-500/70 transition flex flex-col justify-between gap-6 group">
                            <div class="space-y-3">
                                <div class="w-12 h-12 rounded-xl bg-indigo-500/15 text-indigo-400 flex items-center justify-center border border-indigo-500/30 group-hover:scale-105 group-hover:bg-indigo-600 group-hover:text-white transition">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <h3 class="font-display font-bold text-xl text-white tracking-tight">
                                    Onboard a New Store
                                </h3>
                                <p class="text-xs text-slate-400 leading-relaxed">
                                    Provision an isolated store domain, configure products and staff roles, and activate direct Paystack settlement within 60 seconds.
                                </p>
                            </div>

                            <a href="{{ route('register') }}" class="w-full py-3 rounded-xl bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/40 text-xs font-bold uppercase tracking-wider text-center transition flex items-center justify-center gap-2">
                                <span>Create New Workspace</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- Core Capabilities Bento Grid               -->
            <!-- ========================================== -->
            <section id="features" class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
                <!-- Section Intro -->
                <div class="text-center max-w-3xl mx-auto space-y-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-indigo-400">Engineered for High-Volume Retail</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                        Everything You Need to Run Modern Stores
                    </h2>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Say goodbye to fragmented point-of-sale systems, lost paper books, and manual salary bank transfers.
                    </p>
                </div>

                <!-- Bento Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Feature Card 1: Multi-Tenancy & Security -->
                    <div class="p-8 rounded-2xl bg-slate-900/70 border border-slate-800 hover:border-slate-700 transition space-y-5">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="space-y-2">
                            <h3 class="font-display font-bold text-xl text-white tracking-tight">
                                True Multi-Tenant Isolation
                            </h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Seamless subdomain and custom domain resolution. Each boutique or enterprise retail chain operates with completely partitioned databases, distinct customer bases, and private audit trails.
                            </p>
                        </div>
                        <ul class="text-xs text-slate-400 space-y-2 pt-2 border-t border-slate-800">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                <span>Custom store subdomains (e.g. jessis-classiq.com)</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                <span>Multi-outlet inventory synchronization</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Feature Card 2: Automated Paystack Payroll -->
                    <div id="payroll" class="p-8 rounded-2xl bg-slate-900/70 border border-slate-800 hover:border-slate-700 transition space-y-5">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="space-y-2">
                            <h3 class="font-display font-bold text-xl text-white tracking-tight">
                                Automated Paystack Payroll
                            </h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Pay your floor attendants, inventory managers, and boutique staff in a single click via Paystack Transfers API with automatic NUBAN account validation and idempotent reference protection.
                            </p>
                        </div>
                        <ul class="text-xs text-slate-400 space-y-2 pt-2 border-t border-slate-800">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span>Batch disbursement state machine with retry safety</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span>Private accounting ledger ledger generation</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Feature Card 3: OCR Ledger Reader -->
                    <div id="ledger" class="p-8 rounded-2xl bg-slate-900/70 border border-slate-800 hover:border-slate-700 transition space-y-5">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="space-y-2">
                            <h3 class="font-display font-bold text-xl text-white tracking-tight">
                                Paper Ledger OCR Digitize
                            </h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Bridge legacy hand-written daily sales notebooks with modern cloud accounting. Photograph paper receipts or physical ledgers; our AI extracts line items with side-by-side reconciliation.
                            </p>
                        </div>
                        <ul class="text-xs text-slate-400 space-y-2 pt-2 border-t border-slate-800">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                <span>Zero-error reconciliation canvas</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                <span>Queued background extraction pipeline</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- Transparent Pricing Plans Section          -->
            <!-- ========================================== -->
            <section id="pricing" class="py-20 bg-slate-900/50 border-t border-slate-800/80">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                    <div class="text-center max-w-2xl mx-auto space-y-3">
                        <span class="text-xs font-bold uppercase tracking-widest text-indigo-400">Fair &amp; Predictable</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                            Plans Built for Boutiques of All Sizes
                        </h2>
                        <p class="text-slate-400 text-sm">
                            Upgrade or switch plans as your retail footprint expands across new cities.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                        <!-- Plan 1 -->
                        <div class="p-7 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-6">
                            <div class="space-y-2">
                                <h3 class="text-lg font-bold text-white">Starter Boutique</h3>
                                <p class="text-xs text-slate-400">For single boutique merchants launching their retail brand.</p>
                                <div class="pt-2 text-3xl font-extrabold text-white font-mono">₦25,000<span class="text-xs text-slate-400 font-sans font-normal"> / mo</span></div>
                            </div>
                            <ul class="text-xs text-slate-300 space-y-2.5">
                                <li class="flex items-center gap-2">&check; 1 Physical Outlet / Boutique</li>
                                <li class="flex items-center gap-2">&check; In-Store POS &amp; Web Storefront</li>
                                <li class="flex items-center gap-2">&check; Up to 500 Product SKUs</li>
                                <li class="flex items-center gap-2">&check; Paystack Payment Integration</li>
                            </ul>
                            <a href="{{ route('register') }}" class="block w-full py-2.5 text-center rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition">
                                Start 14-Day Trial
                            </a>
                        </div>

                        <!-- Plan 2 (Highlighted) -->
                        <div class="p-7 rounded-2xl bg-gradient-to-b from-indigo-950/40 via-slate-900 to-slate-900 border-2 border-indigo-500/60 shadow-xl shadow-indigo-950/40 space-y-6 relative">
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-indigo-600 text-[10px] font-bold uppercase tracking-widest text-white shadow-md">
                                Most Popular
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-lg font-bold text-white">Multi-Store Pro</h3>
                                <p class="text-xs text-slate-400">For expanding fashion brands and multi-branch retail outlets.</p>
                                <div class="pt-2 text-3xl font-extrabold text-white font-mono">₦65,000<span class="text-xs text-slate-400 font-sans font-normal"> / mo</span></div>
                            </div>
                            <ul class="text-xs text-slate-300 space-y-2.5">
                                <li class="flex items-center gap-2">&check; Up to 5 Physical Outlets</li>
                                <li class="flex items-center gap-2">&check; Real-time Multi-Location Inventory</li>
                                <li class="flex items-center gap-2">&check; <strong>Automated Paystack Payroll</strong></li>
                                <li class="flex items-center gap-2">&check; Unlimited Staff Accounts</li>
                            </ul>
                            <a href="{{ route('register') }}" class="block w-full py-2.5 text-center rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-lg shadow-indigo-600/30">
                                Launch Pro Store
                            </a>
                        </div>

                        <!-- Plan 3 -->
                        <div class="p-7 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-6">
                            <div class="space-y-2">
                                <h3 class="text-lg font-bold text-white">Enterprise Chain</h3>
                                <p class="text-xs text-slate-400">For retail chains requiring custom domains and paper digitizing.</p>
                                <div class="pt-2 text-3xl font-extrabold text-white font-mono">₦150,000<span class="text-xs text-slate-400 font-sans font-normal"> / mo</span></div>
                            </div>
                            <ul class="text-xs text-slate-300 space-y-2.5">
                                <li class="flex items-center gap-2">&check; Unlimited Physical Outlets</li>
                                <li class="flex items-center gap-2">&check; <strong>OCR Paper Ledger Digitize Pipeline</strong></li>
                                <li class="flex items-center gap-2">&check; Custom Domains &amp; Dedicated SSL</li>
                                <li class="flex items-center gap-2">&check; 24/7 Dedicated Account Manager</li>
                            </ul>
                            <a href="{{ route('register') }}" class="block w-full py-2.5 text-center rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition">
                                Contact Enterprise
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- ========================================== -->
        <!-- Platform Footer                            -->
        <!-- ========================================== -->
        <footer class="bg-slate-950 border-t border-slate-800/90 py-12 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-slate-900 text-xs text-slate-400">
                    <!-- Brand summary -->
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                            SM
                        </div>
                        <div>
                            <span class="font-bold text-white">{{ config('app.platform_name', 'ShopManager') }}</span>
                            <span class="block text-[11px] text-slate-500">Retail, Multi-Store &amp; Inventory Operating System</span>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="flex items-center gap-6">
                        <a href="#features" class="hover:text-white transition">Capabilities</a>
                        <a href="#multi-tenant" class="hover:text-white transition">Store Directory</a>
                        <a href="{{ route('login') }}" class="hover:text-white transition">Sign In</a>
                        <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold transition">Get Started</a>
                    </div>
                </div>

                <!-- Bottom copyright -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                    <p>&copy; {{ date('Y') }} {{ config('app.platform_name', 'ShopManager') }} Platform Technologies Inc. All rights reserved.</p>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span class="text-slate-400">All Systems Operational</span>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
