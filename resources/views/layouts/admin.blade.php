<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $currentTenant = app(\App\Services\CurrentTenant::class)->get();
        $platformName = config('app.platform_name', 'ShopManager');
        $storeName = $currentTenant ? $currentTenant->setting('branding.name', $currentTenant->name) : $platformName;
    @endphp

    <title>{{ $storeName }} &bull; {{ $platformName }} Console</title>

    <!-- Modern Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="{ mobileSidebarOpen: false }"
    class="font-sans antialiased bg-slate-50/80 text-slate-900 min-h-screen selection:bg-indigo-600 selection:text-white flex flex-col md:flex-row"
>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- MOBILE TOP BAR (Phone / Tablet < 768px)                     -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="md:hidden sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-4 py-3 flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
            <button
                type="button"
                @click="mobileSidebarOpen = true"
                class="p-2 -ml-1 rounded-xl text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                title="Open Navigation Menu"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                    {{ strtoupper(substr($storeName, 0, 1)) }}
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-slate-900 leading-tight truncate max-w-[140px]">{{ $storeName }}</span>
                    <span class="text-[9px] tracking-wider font-semibold text-slate-400 uppercase">Store Admin</span>
                </div>
            </a>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('admin.sales.create') }}"
                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold uppercase tracking-wider shadow-xs transition"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>POS</span>
            </a>

            @if ($currentTenant && $currentTenant->hasFeature('storefront'))
                <a
                    href="{{ route('home') }}"
                    class="w-8 h-8 rounded-full border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-900 hover:text-white transition"
                    title="View Storefront"
                    target="_blank"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            @endif
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- MOBILE OFF-CANVAS SIDEBAR DRAWER (< 768px)                  -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div
        x-show="mobileSidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileSidebarOpen = false"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 md:hidden"
        style="display: none;"
    ></div>

    <div
        x-show="mobileSidebarOpen"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 w-72 max-w-[85vw] bg-white z-50 p-5 flex flex-col justify-between shadow-2xl md:hidden overflow-y-auto"
        style="display: none;"
    >
        <div class="space-y-6">
            <!-- Drawer Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-extrabold text-xs shadow-sm">
                        {{ strtoupper(substr($storeName, 0, 2)) }}
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-900 leading-tight truncate max-w-[150px]">{{ $storeName }}</div>
                        <div class="text-[10px] text-indigo-600 font-semibold">{{ $platformName }} Console</div>
                    </div>
                </div>
                <button
                    type="button"
                    @click="mobileSidebarOpen = false"
                    class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold transition"
                >
                    &times;
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-5 text-xs font-medium">
                <!-- Group: Operations -->
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3">Operations</span>
                    <a href="{{ route('admin.dashboard') }}" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.products.index') }}" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.products.*') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                        <span>Products &amp; Stock</span>
                    </a>
                    <a href="{{ route('admin.stock.index') }}" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.stock.*') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        <span>Stock Adjustments</span>
                    </a>
                    <a href="{{ route('admin.orders.index') }}" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.orders.*') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        <span>Orders</span>
                    </a>
                </div>

                <!-- Group: Sales & Register -->
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3">Commerce</span>
                    <a href="{{ route('admin.sales.create') }}" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.sales.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-700 bg-emerald-50/70 hover:bg-emerald-100' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                        <span class="font-semibold">Walk-In POS</span>
                    </a>
                    <a href="{{ route('admin.ocr.index') }}" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.ocr.*') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span>OCR Daybook Import</span>
                    </a>
                </div>

                <!-- Group: Management (Admin only) -->
                @if(Auth::user()->isAdmin())
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3">Finance &amp; Team</span>
                        <a href="{{ route('admin.reports.index') }}" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.reports.*') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span>Reports &amp; Analytics</span>
                        </a>
                        <a href="{{ route('admin.staff.index') }}" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.staff.*') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <span>Staff Directory</span>
                        </a>
                        <a href="{{ route('admin.payroll.index') }}" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.payroll.*') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            <span>Payroll Transfers</span>
                        </a>
                    </div>
                @endif

                <!-- Platform Switcher -->
                <div class="pt-4 border-t border-slate-100 space-y-1">
                    @if ($currentTenant && $currentTenant->hasFeature('storefront'))
                        <a href="/" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl text-slate-500 hover:bg-slate-100 transition text-[11px]">
                            <span>View Storefront</span>
                            <span>&nearr;</span>
                        </a>
                    @endif
                    <a href="{{ url('/?platform=1') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-indigo-600 bg-indigo-50/60 hover:bg-indigo-100 transition text-[11px] font-semibold">
                        <span>{{ $platformName }} Platform</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- User Profile & Sign Out (Drawer bottom) -->
        <div class="pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 pr-1">
                        <div class="font-bold text-xs text-slate-900 truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-400 capitalize font-medium">{{ Auth::user()->role }}</div>
                    </div>
                </div>
                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Active session"></span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-2 px-3 rounded-xl border border-slate-200 hover:bg-slate-900 hover:text-white text-[11px] font-bold uppercase tracking-wider text-slate-600 transition text-center cursor-pointer">
                    Sign Out
                </button>
            </form>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- DESKTOP PERMANENT SIDEBAR (>= 768px)                        -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <aside class="hidden md:flex md:w-68 bg-white border-r border-slate-200/80 shrink-0 p-5 flex-col justify-between sticky top-0 h-screen overflow-y-auto">
        <div class="space-y-6">
            <!-- Store Identity Card & Switcher -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-indigo-600/20 shrink-0">
                        {{ strtoupper(substr($storeName, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-xs text-slate-900 truncate leading-tight" title="{{ $storeName }}">
                            {{ $storeName }}
                        </div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-[10px] font-mono text-slate-500 truncate">
                                {{ $currentTenant ? $currentTenant->slug : 'platform' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Switch Link -->
                <div class="mt-3 pt-2.5 border-t border-slate-200/70 flex items-center justify-between text-[10px] font-semibold">
                    <span class="text-slate-400 uppercase tracking-wider">Workspace</span>
                    <a href="{{ url('/?platform=1') }}" class="text-indigo-600 hover:text-indigo-800 transition inline-flex items-center gap-1">
                        <span>{{ $platformName }}</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Grouped Navigation Links -->
            <nav class="space-y-5 text-xs font-medium">
                <!-- Group 1: Operations -->
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 block mb-1">
                        Operations
                    </span>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition duration-150 {{ request()->routeIs('admin.products.*') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                        <span>Products &amp; Catalog</span>
                    </a>

                    <a href="{{ route('admin.stock.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition duration-150 {{ request()->routeIs('admin.stock.*') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        <span>Stock Adjustments</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition duration-150 {{ request()->routeIs('admin.orders.*') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        <span>Orders</span>
                    </a>
                </div>

                <!-- Group 2: Register & Sales -->
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 block mb-1">
                        Register &amp; Commerce
                    </span>
                    <a href="{{ route('admin.sales.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition duration-150 {{ request()->routeIs('admin.sales.*') ? 'bg-emerald-600 text-white font-semibold shadow-xs' : 'text-emerald-700 bg-emerald-50/70 hover:bg-emerald-100' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                        <span class="font-semibold">Walk-In POS</span>
                    </a>

                    <a href="{{ route('admin.ocr.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition duration-150 {{ request()->routeIs('admin.ocr.*') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span>OCR Daybook Import</span>
                    </a>
                </div>

                <!-- Group 3: Finance & People (Admin Only) -->
                @if(Auth::user()->isAdmin())
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 block mb-1">
                            Finance &amp; Team
                        </span>
                        <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition duration-150 {{ request()->routeIs('admin.reports.*') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span>Reports &amp; Analytics</span>
                        </a>

                        <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition duration-150 {{ request()->routeIs('admin.staff.*') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <span>Staff Directory</span>
                        </a>

                        <a href="{{ route('admin.payroll.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition duration-150 {{ request()->routeIs('admin.payroll.*') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            <span>Payroll Transfers</span>
                        </a>
                    </div>
                @endif

                <!-- Storefront Link -->
                @if ($currentTenant && $currentTenant->hasFeature('storefront'))
                    <div class="pt-3 border-t border-slate-100">
                        <a href="/" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl text-slate-500 hover:bg-slate-100 transition text-[11px]">
                            <span>View Storefront</span>
                            <span>&nearr;</span>
                        </a>
                    </div>
                @endif
            </nav>
        </div>

        <!-- User Profile & Sign Out -->
        <div class="pt-4 border-t border-slate-200/80">
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 pr-1">
                        <div class="font-bold text-xs text-slate-900 truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-400 capitalize font-medium">{{ Auth::user()->role }}</div>
                    </div>
                </div>
                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Active"></span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-2 px-3 rounded-xl border border-slate-200 hover:bg-slate-900 hover:text-white text-[11px] font-bold uppercase tracking-wider text-slate-600 transition text-center cursor-pointer shadow-xs">
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- MAIN WORKSPACE                                              -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <main class="flex-1 flex flex-col min-w-0 min-h-screen overflow-x-hidden">
        <!-- Top Desktop App Header (>= 768px) -->
        <header class="hidden md:flex bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-6 sm:px-10 py-3.5 items-center justify-between shadow-xs">
            <!-- Breadcrumbs / Section Title -->
            <div class="flex items-center gap-2 text-xs">
                <span class="text-slate-400 font-medium">{{ $platformName }}</span>
                <span class="text-slate-300">/</span>
                <span class="font-bold text-slate-900">{{ $storeName }}</span>
                <span class="text-slate-300">/</span>
                <div class="font-semibold text-indigo-600">
                    @isset($header)
                        {{ $header }}
                    @else
                        <span>Administration</span>
                    @endisset
                </div>
            </div>

            <!-- Quick Action Toolbar -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-200 hover:border-slate-300 text-slate-700 hover:text-slate-900 text-xs font-semibold transition">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>Product</span>
                </a>

                <a href="{{ route('admin.sales.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>Walk-In Sale</span>
                </a>

                @if ($currentTenant && $currentTenant->hasFeature('storefront'))
                    <a href="/" target="_blank" class="w-8 h-8 rounded-full border border-slate-200 hover:border-slate-300 flex items-center justify-center text-slate-600 hover:text-slate-900 transition" title="View Storefront">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    </a>
                @endif
            </div>
        </header>

        <!-- Content Body -->
        <div class="p-4 sm:p-8 lg:p-10 flex-1">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between gap-3 shadow-xs">
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold shrink-0">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center justify-between gap-3 shadow-xs">
                    <span>{{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900 font-bold shrink-0">✕</button>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</body>
</html>
