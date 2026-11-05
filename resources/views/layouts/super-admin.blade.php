<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Super Admin Cockpit' }} &bull; {{ config('app.platform_name', 'ShopManager') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

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
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                    }
                }
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#0b0f19] text-slate-100 min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">

    <!-- Ambient Glow Background -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[900px] h-[400px] bg-gradient-to-b from-indigo-600/15 via-violet-600/10 to-transparent rounded-full blur-[120px]"></div>
    </div>

    <!-- Top Navigation Bar -->
    <header class="w-full bg-[#0b0f19]/90 backdrop-blur-md border-b border-slate-800/90 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18 gap-4">
                <!-- Brand + Badge -->
                <div class="flex items-center gap-3.5">
                    <a href="{{ route('super-admin.dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/30">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-display font-bold text-lg text-white group-hover:text-indigo-200 transition">
                                {{ config('app.platform_name', 'ShopManager') }}
                            </span>
                        </div>
                    </a>

                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-500/15 text-rose-300 border border-rose-500/30">
                        Super Admin
                    </span>
                </div>

                <!-- Center Store Jumper -->
                @php
                    $allStoresList = \App\Models\Tenant::where('status', 'active')->orderBy('name')->get();
                @endphp
                <div class="hidden md:flex items-center gap-2">
                    <div class="relative" x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            type="button"
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs text-slate-300 hover:text-white transition"
                        >
                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Jump to Store Console</span>
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            @click.outside="open = false"
                            class="absolute left-0 mt-2 w-64 rounded-xl bg-slate-900 border border-slate-800 shadow-2xl p-2 z-50 space-y-1"
                            style="display: none;"
                        >
                            <div class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                Active Stores ({{ $allStoresList->count() }})
                            </div>
                            @foreach ($allStoresList as $storeOption)
                                <a
                                    href="{{ url('/admin?tenant=' . $storeOption->slug) }}"
                                    class="flex items-center justify-between px-2.5 py-1.5 rounded-lg hover:bg-slate-800 text-xs text-slate-200 transition"
                                >
                                    <span class="truncate font-medium">{{ $storeOption->name }}</span>
                                    <span class="text-[10px] text-indigo-400 font-mono">&rarr;</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('home') }}"
                        class="text-xs text-slate-400 hover:text-white transition hidden sm:inline-block"
                        target="_blank"
                    >
                        View Platform Site &nearr;
                    </a>

                    <div class="flex items-center gap-2 pl-3 border-l border-slate-800">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <div class="text-xs font-bold text-white leading-tight">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-rose-400 font-mono">Platform Owner</div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="ml-2">
                            @csrf
                            <button
                                type="submit"
                                class="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition"
                                title="Sign Out"
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

    <!-- Subnavigation Header Tabs -->
    <div class="w-full bg-slate-900/60 border-b border-slate-800/80 sticky top-18 z-40 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-1 sm:gap-2 overflow-x-auto py-2 text-xs font-semibold">
                <a
                    href="{{ route('super-admin.dashboard') }}"
                    class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('super-admin.dashboard') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}"
                >
                    Platform Overview
                </a>
                <a
                    href="{{ route('super-admin.tenants.index') }}"
                    class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('super-admin.tenants.index') || request()->routeIs('super-admin.tenants.show') || request()->routeIs('super-admin.tenants.edit') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}"
                >
                    Store Tenants Directory
                </a>
                <a
                    href="{{ route('super-admin.tenants.create') }}"
                    class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('super-admin.tenants.create') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-indigo-400 hover:text-white hover:bg-indigo-600/20' }}"
                >
                    + Provision New Store
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10 space-y-6">
        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-medium flex items-center justify-between">
                <span>{{ session('status') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs font-medium space-y-1">
                <div class="font-bold">Please check the following errors:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Super Admin Footer -->
    <footer class="bg-slate-950 border-t border-slate-900 py-6 text-xs text-slate-500 text-center relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span>{{ config('app.platform_name', 'ShopManager') }} &bull; Platform Super Admin Console</span>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span class="text-slate-400">Master Tenancy Engine Online</span>
            </div>
        </div>
    </footer>
</body>
</html>
