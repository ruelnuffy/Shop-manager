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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="{ mobileMenuOpen: false }"
    class="font-sans antialiased bg-[#fbfcfe] text-slate-900 min-h-screen selection:bg-slate-900 selection:text-white flex flex-col"
>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- FLOATING TOP PILL NAVIGATION (Desktop >= 768px)             -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <header class="hidden md:block sticky top-3 z-50 px-4 sm:px-6 pt-1 pb-3">
        <div class="max-w-6xl mx-auto bg-white/90 backdrop-blur-md border border-slate-200/90 shadow-md shadow-slate-900/5 rounded-full px-5 py-2.5 flex items-center justify-between gap-4 transition-all">

            <!-- Left: Store Logo & Brand Badge -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-extrabold text-xs shadow-xs group-hover:scale-105 transition-transform">
                        {{ strtoupper(substr($storeName, 0, 2)) }}
                    </div>
                    <span class="text-xs font-extrabold text-slate-900 tracking-tight hidden lg:inline truncate max-w-[140px]">
                        {{ $storeName }}
                    </span>
                </a>
            </div>

            <!-- Center: Navigation Links as Modern Rounded Pills -->
            <nav class="flex items-center gap-1 text-xs font-semibold overflow-x-auto py-0.5">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                >
                    HOME
                </a>

                <a
                    href="{{ route('admin.sales.create') }}"
                    class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.sales.*') ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-emerald-700 bg-emerald-50/80 hover:bg-emerald-100 font-bold' }}"
                >
                    POS SALE
                </a>

                <a
                    href="{{ route('admin.orders.index') }}"
                    class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.orders.*') ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                >
                    ORDERS
                </a>

                <a
                    href="{{ route('admin.products.index') }}"
                    class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.products.*') ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                >
                    PRODUCTS
                </a>

                <a
                    href="{{ route('admin.stock.index') }}"
                    class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.stock.*') ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                >
                    STOCK
                </a>

                <a
                    href="{{ route('admin.ocr.index') }}"
                    class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.ocr.*') ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                >
                    RECEIPTS
                </a>

                @if(Auth::user()->isAdmin())
                    <a
                        href="{{ route('admin.reports.index') }}"
                        class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.reports.*') ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                    >
                        REPORTS
                    </a>

                    <a
                        href="{{ route('admin.staff.index') }}"
                        class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.staff.*') ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                    >
                        STAFF
                    </a>

                    <a
                        href="{{ route('admin.payroll.index') }}"
                        class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.payroll.*') ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                    >
                        PAYROLL
                    </a>
                @endif
            </nav>

            <!-- Right: Tenant Status Pill & Sign Out -->
            <div class="flex items-center gap-2.5 shrink-0">
                @if (Auth::user()?->isSuperAdmin())
                    <a href="{{ route('super-admin.dashboard') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition hidden xl:inline">
                        Super Admin &nearr;
                    </a>
                @endif

                @if ($currentTenant && $currentTenant->hasFeature('storefront'))
                    <a
                        href="{{ route('home') }}"
                        target="_blank"
                        class="w-8 h-8 rounded-full border border-slate-200/90 hover:border-slate-400 flex items-center justify-center text-slate-500 hover:text-slate-900 transition"
                        title="View Live Storefront"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    </a>
                @endif

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100/80 border border-slate-200/80 text-xs font-semibold text-slate-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span class="truncate max-w-[110px]">{{ Auth::user()->name }}</span>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="px-3.5 py-1.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-bold uppercase tracking-wider transition shadow-2xs cursor-pointer"
                    >
                        SIGN OUT
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- MOBILE TOP BAR & DRAWER (< 768px)                           -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="md:hidden sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-4 py-3 flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
            <button
                type="button"
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="p-2 -ml-1 rounded-xl text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                title="Navigation Menu"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                    {{ strtoupper(substr($storeName, 0, 2)) }}
                </div>
                <span class="text-xs font-bold text-slate-900 truncate max-w-[140px]">{{ $storeName }}</span>
            </a>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('admin.sales.create') }}"
                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-emerald-600 text-white text-[11px] font-bold uppercase tracking-wider shadow-xs"
            >
                POS
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-2.5 py-1.5 rounded-full border border-slate-200 text-[10px] font-bold text-slate-600 uppercase">
                    Exit
                </button>
            </form>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div
        x-show="mobileMenuOpen"
        x-transition
        @click.away="mobileMenuOpen = false"
        class="md:hidden bg-white border-b border-slate-200/90 px-4 py-4 space-y-2 shadow-lg"
        style="display: none;"
    >
        <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
            <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900">Dashboard</a>
            <a href="{{ route('admin.sales.create') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800">Make Sale (POS)</a>
            <a href="{{ route('admin.orders.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900">Orders</a>
            <a href="{{ route('admin.products.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900">Products</a>
            <a href="{{ route('admin.stock.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900">Stock Inventory</a>
            <a href="{{ route('admin.ocr.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900">Scan Receipts</a>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.reports.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900">Sales Reports</a>
                <a href="{{ route('admin.staff.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900">Staff &amp; Team</a>
                <a href="{{ route('admin.payroll.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900">Salaries &amp; Payroll</a>
            @endif
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- MAIN CANVAS WORKSPACE                                       -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 pt-3 pb-16">
        @isset($header)
            <div class="mb-6 pb-2">
                {{ $header }}
            </div>
        @endisset

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold shrink-0">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900 font-bold shrink-0">&times;</button>
            </div>
        @endif

        {{ $slot }}
    </main>

</body>
</html>
