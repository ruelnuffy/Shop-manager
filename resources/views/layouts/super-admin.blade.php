<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Super Admin Cockpit' }} &bull; {{ config('app.platform_name', 'ShopManager') }} Control Plane</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
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
<body class="font-sans antialiased bg-slate-50 text-slate-800 min-h-screen flex selection:bg-indigo-500 selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- ========================================================= -->
    <!-- LEFT SIDEBAR NAVIGATION (Desktop & Off-canvas Mobile)     -->
    <!-- ========================================================= -->
    <aside class="hidden lg:flex lg:flex-col w-72 bg-white border-r border-slate-200/90 shrink-0 sticky top-0 h-screen z-40 justify-between shadow-xs">
        <div class="flex flex-col h-full overflow-y-auto">
            <!-- Brand & Workspace Header -->
            <div class="p-6 pb-5 border-b border-slate-100">
                <a href="{{ route('super-admin.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-600 to-violet-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/20 group-hover:scale-105 transition duration-150">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-base text-slate-900 tracking-tight group-hover:text-indigo-600 transition">
                                {{ config('app.platform_name', 'ShopManager') }}
                            </span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase tracking-wider bg-rose-50 text-rose-600 border border-rose-200/60 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Super Admin
                        </span>
                    </div>
                </a>

                <!-- Store Switcher / Jump to Store Console -->
                @php
                    $allStoresList = \App\Models\Tenant::where('status', 'active')->orderBy('name')->get();
                @endphp
                <div class="mt-5 relative" x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        type="button"
                        class="w-full flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700 transition"
                    >
                        <span class="flex items-center gap-2 truncate">
                            <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="truncate">Masquerade Store</span>
                        </span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] bg-white border border-slate-200 font-mono text-slate-500">{{ $allStoresList->count() }}</span>
                    </button>

                    <div
                        x-show="open"
                        @click.outside="open = false"
                        class="absolute left-0 right-0 mt-2 rounded-2xl bg-white border border-slate-200 shadow-xl p-2 z-50 space-y-1 max-h-60 overflow-y-auto"
                        style="display: none;"
                    >
                        <div class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Active Stores ({{ $allStoresList->count() }})
                        </div>
                        @forelse ($allStoresList as $storeOption)
                            <a
                                href="{{ url('/admin?tenant=' . $storeOption->slug) }}"
                                class="flex items-center justify-between p-2 rounded-lg hover:bg-indigo-50 text-xs text-slate-700 hover:text-indigo-600 transition"
                            >
                                <span class="truncate font-medium">{{ $storeOption->name }}</span>
                                <span class="text-[10px] text-indigo-600 font-mono font-bold">&rarr;</span>
                            </a>
                        @empty
                            <div class="text-xs text-slate-400 p-2">No active stores found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-6 flex-1">
                <!-- Group: Platform -->
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3">
                        Platform Management
                    </span>

                    <a
                        href="{{ route('super-admin.dashboard') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('super-admin.dashboard') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                    >
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('super-admin.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>Platform Overview</span>
                        </div>
                    </a>

                    <a
                        href="{{ route('super-admin.tenants.index') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('super-admin.tenants.index') || request()->routeIs('super-admin.tenants.show') || request()->routeIs('super-admin.tenants.edit') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                    >
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('super-admin.tenants.index') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Tenant Stores</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono {{ request()->routeIs('super-admin.tenants.index') ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            {{ $allStoresList->count() }}
                        </span>
                    </a>

                    <a
                        href="{{ route('super-admin.tenants.create') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('super-admin.tenants.create') ? 'bg-indigo-600 text-white shadow-sm' : 'text-indigo-600 hover:bg-indigo-50' }}"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ Provision New Store</span>
                    </a>
                </div>

                <!-- Group: Operations -->
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3">
                        Operations &amp; Diagnostics
                    </span>

                    <a
                        href="{{ route('super-admin.export-report') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition"
                    >
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Export Global CSV</span>
                    </a>

                    <form method="POST" action="{{ route('super-admin.clear-cache') }}">
                        @csrf
                        <button
                            type="submit"
                            onclick="return confirm('Purge platform and template cache?')"
                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition"
                        >
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Purge System Cache</span>
                        </button>
                    </form>
                </div>

                <!-- Group: Live Links -->
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3">
                        External Endpoints
                    </span>
                    <a
                        href="{{ route('home') }}"
                        target="_blank"
                        class="flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-medium text-slate-500 hover:text-indigo-600 hover:bg-slate-50 transition"
                    >
                        <span>Platform Landing Page</span>
                        <span class="text-[10px] font-mono">&nearr;</span>
                    </a>
                </div>
            </nav>

            <!-- Bottom User Profile Card -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</div>
                            <div class="text-[11px] text-slate-400 truncate font-mono">Platform Admin</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            title="Sign Out"
                            class="p-2 rounded-xl hover:bg-slate-200/70 text-slate-500 hover:text-slate-900 transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- ========================================================= -->
    <!-- MAIN CONTENT AREA (Clean, Airy, White Canvas)              -->
    <!-- ========================================================= -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top App Bar for Mobile & Telemetry Status -->
        <header class="w-full bg-white border-b border-slate-200/90 px-4 sm:px-6 lg:px-8 py-3.5 sticky top-0 z-30 shadow-2xs">
            <div class="flex items-center justify-between gap-4">
                <!-- Mobile Menu Button & Brand -->
                <div class="flex items-center gap-3 lg:hidden">
                    <button
                        type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="p-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="font-extrabold text-slate-900 text-sm">ShopManager Cockpit</span>
                </div>

                <!-- Desktop Status Ribbon -->
                <div class="hidden lg:flex items-center gap-4 text-xs">
                    <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-700 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Tenancy Mesh Operational</span>
                    </div>

                    <div class="flex items-center gap-2 text-slate-500 font-mono text-[11px]">
                        <span>Paystack Webhooks: <strong class="text-slate-800">Active (600/min)</strong></span>
                        <span>&bull;</span>
                        <span>Node: <strong class="text-slate-800">127.0.0.1:8001</strong></span>
                    </div>
                </div>

                <!-- Right Quick CTA -->
                <div class="flex items-center gap-2.5">
                    <a
                        href="{{ route('super-admin.tenants.create') }}"
                        class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>New Store</span>
                    </a>
                </div>
            </div>

            <!-- Mobile Dropdown Navigation -->
            <div x-show="mobileMenuOpen" class="lg:hidden mt-3 pt-3 border-t border-slate-100 space-y-1" style="display: none;">
                <a href="{{ route('super-admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold text-slate-800 hover:bg-slate-100">Platform Overview</a>
                <a href="{{ route('super-admin.tenants.index') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold text-slate-800 hover:bg-slate-100">Tenant Stores Directory</a>
                <a href="{{ route('super-admin.tenants.create') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold text-indigo-600 hover:bg-indigo-50">+ Provision Store</a>
                <a href="{{ route('super-admin.export-report') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold text-slate-800 hover:bg-slate-100">Export Global CSV</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-2">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50">Sign Out</button>
                </form>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-6 sm:p-8 lg:p-10 space-y-8 max-w-7xl w-full mx-auto">
            @if (session('status'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>{{ session('status') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 text-base">&times;</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-xs">
                    <div class="font-bold">Please check the following errors:</div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>

        <!-- Clean Super Admin Footer -->
        <footer class="bg-white border-t border-slate-200/90 py-5 px-6 sm:px-8 text-xs text-slate-500 mt-auto">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-700">{{ config('app.platform_name', 'ShopManager') }} Control Plane</span>
                    <span>&bull;</span>
                    <span>B2B Multi-Tenant Retail Engine</span>
                </div>
                <div class="font-mono text-[11px] text-slate-400">
                    PHP {{ PHP_VERSION }} &bull; Laravel {{ app()->version() }} &bull; Single-Database Multi-Tenancy
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
