<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Super Admin Cockpit' }} &bull; {{ config('app.platform_name', 'ShopManager') }} Control Plane</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        dark: {
                            950: '#07090e',
                            900: '#0c0f17',
                            850: '#111522',
                            800: '#171c2c',
                            700: '#232a3e',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        display: ['Syne', '"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- Chart.js for real-time analytics graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#07090e] text-slate-100 min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white relative">

    <!-- Ambient Grid & High-End Glow Background -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:24px_24px] opacity-25"></div>
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[1100px] h-[500px] bg-gradient-to-b from-indigo-600/15 via-violet-600/10 to-transparent rounded-full blur-[140px]"></div>
        <div class="absolute top-1/3 -left-48 w-[400px] h-[400px] bg-rose-600/5 rounded-full blur-[120px]"></div>
        <div class="absolute top-2/3 -right-48 w-[400px] h-[400px] bg-emerald-600/5 rounded-full blur-[120px]"></div>
    </div>

    <!-- Top Live System Health Bar -->
    <div class="w-full bg-[#05070a] border-b border-slate-800/80 text-[11px] py-1.5 px-4 sm:px-6 relative z-50">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3 text-slate-400">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 font-mono font-medium text-[10px] border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    ALL SYSTEMS NOMINAL
                </span>
                <span class="hidden md:inline text-slate-600">&bull;</span>
                <span class="hidden md:inline font-mono text-[11px] text-slate-400">
                    Master Tenancy Engine <span class="text-indigo-400 font-semibold">v2.4-enterprise</span>
                </span>
                <span class="hidden lg:inline text-slate-600">&bull;</span>
                <span class="hidden lg:inline font-mono text-[11px] text-slate-400">
                    Paystack Webhook Mesh: <span class="text-emerald-400">Listening (600/min)</span>
                </span>
            </div>

            <div class="flex items-center gap-4 text-[11px] font-mono">
                <span class="text-slate-400 hidden sm:inline">
                    PHP <span class="text-slate-200">{{ PHP_VERSION }}</span> &bull; Laravel <span class="text-slate-200">{{ app()->version() }}</span>
                </span>
                <span class="text-indigo-400 font-semibold">
                    Local Node &bull; 127.0.0.1:8001
                </span>
            </div>
        </div>
    </div>

    <!-- Top Navigation Header -->
    <header class="w-full bg-[#0c0f17]/90 backdrop-blur-xl border-b border-slate-800/90 sticky top-0 z-50 shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4">
                <!-- Brand + Super Admin Badge -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('super-admin.dashboard') }}" class="flex items-center gap-3.5 group">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 via-violet-600 to-indigo-500 text-white flex items-center justify-center shadow-lg shadow-indigo-600/30 group-hover:scale-105 transition duration-200">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-display font-black text-xl tracking-tight text-white group-hover:text-indigo-200 transition">
                                    {{ config('app.platform_name', 'ShopManager') }}
                                </span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-extrabold uppercase tracking-widest bg-gradient-to-r from-rose-500/20 to-red-500/20 text-rose-300 border border-rose-500/30">
                                    SUPER ADMIN
                                </span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono tracking-wide">Multi-Tenant Cloud Operations</span>
                        </div>
                    </a>
                </div>

                <!-- Center Store Jumper & Global Operations -->
                @php
                    $allStoresList = \App\Models\Tenant::where('status', 'active')->orderBy('name')->get();
                @endphp
                <div class="hidden lg:flex items-center gap-3" x-data="{ open: false, search: '' }">
                    <div class="relative">
                        <button
                            @click="open = !open"
                            type="button"
                            class="inline-flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-slate-900/90 border border-slate-800 hover:border-indigo-500/50 hover:bg-slate-850 text-xs text-slate-300 hover:text-white transition shadow-sm"
                        >
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="font-medium">Switch Active Merchant</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] bg-slate-800 text-slate-400 font-mono">{{ $allStoresList->count() }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            @click.outside="open = false"
                            class="absolute left-0 mt-2 w-80 rounded-2xl bg-slate-900 border border-slate-800 shadow-2xl p-3 z-50 space-y-2 backdrop-blur-xl"
                            style="display: none;"
                        >
                            <div class="relative">
                                <input
                                    type="text"
                                    x-model="search"
                                    placeholder="Filter merchants by name..."
                                    class="w-full px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                                />
                            </div>

                            <div class="max-h-56 overflow-y-auto space-y-1 divide-y divide-slate-800/40">
                                @forelse ($allStoresList as $storeOption)
                                    <div
                                        x-show="!search || '{{ strtolower($storeOption->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($storeOption->slug) }}'.includes(search.toLowerCase())"
                                        class="pt-1.5 first:pt-0"
                                    >
                                        <a
                                            href="{{ url('/admin?tenant=' . $storeOption->slug) }}"
                                            class="flex items-center justify-between p-2 rounded-xl hover:bg-indigo-600/10 hover:border-indigo-500/30 border border-transparent text-xs text-slate-200 transition group"
                                        >
                                            <div class="min-w-0 pr-2">
                                                <div class="truncate font-semibold text-white group-hover:text-indigo-300">{{ $storeOption->name }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $storeOption->slug }} &bull; {{ strtoupper($storeOption->plan) }}</div>
                                            </div>
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-indigo-400 font-mono group-hover:bg-indigo-600 group-hover:text-white transition">Masquerade &rarr;</span>
                                        </a>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-xs text-slate-500">No active stores found.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Quick Operations & Profile -->
                <div class="flex items-center gap-3">
                    <!-- Export Report Action -->
                    <a
                        href="{{ route('super-admin.export-report') }}"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition shadow-sm"
                        title="Download platform merchant audit data"
                    >
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Export CSV</span>
                    </a>

                    <!-- Purge Cache Action -->
                    <form method="POST" action="{{ route('super-admin.clear-cache') }}" class="hidden sm:inline-block">
                        @csrf
                        <button
                            type="submit"
                            onclick="return confirm('Purge platform cache and view buffers?')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition shadow-sm"
                            title="Clear Redis / File cache"
                        >
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Purge Cache</span>
                        </button>
                    </form>

                    <!-- Super Admin Profile & Sign Out -->
                    <div class="flex items-center gap-3 pl-3 border-l border-slate-800/90">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-rose-600 to-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-md shadow-rose-600/20">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <div class="text-xs font-bold text-white leading-tight flex items-center gap-1.5">
                                <span>{{ Auth::user()->name }}</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                            </div>
                            <div class="text-[10px] text-rose-400 font-mono">Platform Super Admin</div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="ml-1">
                            @csrf
                            <button
                                type="submit"
                                class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-white transition"
                                title="Sign Out from Cockpit"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Subnavigation Header Ribbon -->
    <div class="w-full bg-[#0c0f17]/60 border-b border-slate-800/80 sticky top-20 z-40 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center justify-between overflow-x-auto py-2.5 text-xs font-semibold gap-4">
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <a
                        href="{{ route('super-admin.dashboard') }}"
                        class="px-4 py-2 rounded-xl transition flex items-center gap-2 {{ request()->routeIs('super-admin.dashboard') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Platform Overview</span>
                    </a>
                    <a
                        href="{{ route('super-admin.tenants.index') }}"
                        class="px-4 py-2 rounded-xl transition flex items-center gap-2 {{ request()->routeIs('super-admin.tenants.index') || request()->routeIs('super-admin.tenants.show') || request()->routeIs('super-admin.tenants.edit') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Tenant Stores Directory</span>
                    </a>
                </div>

                <div>
                    <a
                        href="{{ route('super-admin.tenants.create') }}"
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold transition shadow-md shadow-indigo-600/30 flex items-center gap-2 whitespace-nowrap"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ Provision New Store</span>
                    </a>
                </div>
            </nav>
        </div>
    </div>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10 space-y-8">
        @if (session('status'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-semibold flex items-center justify-between shadow-lg shadow-emerald-950/20">
                <div class="flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>{{ session('status') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-400 hover:text-white text-base">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs font-semibold space-y-1 shadow-lg shadow-rose-950/20">
                <div class="font-bold flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                    <span>Action Failed — Please check the following inputs:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Super Admin Footer -->
    <footer class="bg-[#05070a] border-t border-slate-900 py-6 text-xs text-slate-500 relative z-10 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-400">{{ config('app.platform_name', 'ShopManager') }} Control Plane</span>
                <span>&bull;</span>
                <span class="text-slate-500 font-mono">B2B SaaS Multi-Tenant Engine</span>
            </div>

            <div class="flex items-center gap-4 text-[11px] font-mono">
                <span class="flex items-center gap-1.5 text-emerald-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Zero Data Leaks Active
                </span>
                <span>&bull;</span>
                <a href="{{ route('home') }}" class="hover:text-white transition" target="_blank">Platform Storefront &nearr;</a>
            </div>
        </div>
    </footer>
</body>
</html>
