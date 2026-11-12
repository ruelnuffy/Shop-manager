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

    <!-- Impersonation / Masquerade Notification Banner -->
    @if (session()->has('impersonator_id'))
        <div class="bg-gradient-to-r from-indigo-950 via-slate-900 to-indigo-900 text-white border-b border-indigo-700/50 px-4 sm:px-6 py-2.5 text-xs sticky top-0 z-[60] shadow-md flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-400 text-amber-950 shadow-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-900 animate-pulse"></span>
                    {{ session('impersonator_role') === 'super_admin' ? 'Super Admin Masquerade' : 'Staff View Active' }}
                </span>
                <span class="text-slate-200">
                    Operating as <strong>{{ Auth::user()->name }}</strong> ({{ ucfirst(Auth::user()->role) }}) &bull; <span class="text-indigo-300 font-semibold">{{ $storeName }}</span>
                </span>
            </div>
            <form method="POST" action="{{ route('impersonate.leave') }}" class="inline">
                @csrf
                <button
                    type="submit"
                    class="px-3 py-1 rounded-full bg-white hover:bg-slate-100 text-indigo-950 font-extrabold text-[11px] uppercase tracking-wider transition shadow-xs flex items-center gap-1.5 cursor-pointer active:scale-95"
                >
                    <span>&larr;</span>
                    <span>Return to {{ session('impersonator_role') === 'super_admin' ? 'Super Admin Cockpit' : 'Admin Dashboard' }}</span>
                </button>
            </form>
        </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- FLOATING TOP PILL NAVIGATION (Desktop >= 768px)             -->
    <!-- Borderless with soft modern ambient shadow                  -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <header class="hidden md:block sticky top-3 z-50 px-4 sm:px-6 pt-1 pb-3">
        <div class="max-w-6xl mx-auto bg-white/95 backdrop-blur-md shadow-xl shadow-slate-900/5 rounded-full px-5 py-2.5 flex items-center justify-between gap-4 transition-all">

            <!-- Left: Store Logo & Brand Badge -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-extrabold text-xs shadow-sm group-hover:scale-105 transition-transform">
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
                    class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white font-bold shadow-md shadow-slate-900/10' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}"
                >
                    HOME
                </a>

                <a
                    href="{{ route('admin.sales.create') }}"
                    class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.sales.*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/15' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 font-bold' }}"
                >
                    POS SALE
                </a>

                <a
                    href="{{ route('admin.orders.index') }}"
                    class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.orders.*') ? 'bg-slate-900 text-white font-bold shadow-md shadow-slate-900/10' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}"
                >
                    ORDERS
                </a>

                <a
                    href="{{ route('admin.products.index') }}"
                    class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.products.*') ? 'bg-slate-900 text-white font-bold shadow-md shadow-slate-900/10' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}"
                >
                    PRODUCTS
                </a>

                <a
                    href="{{ route('admin.stock.index') }}"
                    class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.stock.*') ? 'bg-slate-900 text-white font-bold shadow-md shadow-slate-900/10' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}"
                >
                    STOCK
                </a>

                <a
                    href="{{ route('admin.ocr.index') }}"
                    class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.ocr.*') ? 'bg-slate-900 text-white font-bold shadow-md shadow-slate-900/10' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}"
                >
                    RECEIPTS
                </a>

                @if(Auth::user()->isAdmin())


                    <a
                        href="{{ route('admin.reports.index') }}"
                        class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.reports.*') ? 'bg-slate-900 text-white font-bold shadow-md shadow-slate-900/10' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}"
                    >
                        REPORTS
                    </a>

                    <a
                        href="{{ route('admin.staff.index') }}"
                        class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.staff.*') ? 'bg-slate-900 text-white font-bold shadow-md shadow-slate-900/10' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}"
                    >
                        STAFF
                    </a>

                    <a
                        href="{{ route('admin.payroll.index') }}"
                        class="px-3.5 py-1.5 rounded-full transition {{ request()->routeIs('admin.payroll.*') ? 'bg-slate-900 text-white font-bold shadow-md shadow-slate-900/10' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}"
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
                        href="{{ route('storefront') }}"
                        target="_blank"
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 hover:text-slate-900 transition shadow-xs"
                        title="View Live Storefront"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    </a>
                @endif

                @if (Auth::user()->isAdmin() && !session()->has('impersonator_id'))
                    <form method="POST" action="{{ route('admin.impersonate-staff') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="px-3 py-1.5 rounded-full bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 text-[11px] font-bold uppercase tracking-wider transition shadow-xs flex items-center gap-1 cursor-pointer"
                            title="Switch directly to Staff Till view"
                        >
                            <span>Staff Till &nearr;</span>
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="px-3.5 py-1.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-bold uppercase tracking-wider transition shadow-sm cursor-pointer"
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
    <div class="md:hidden sticky top-0 z-50 bg-white/95 backdrop-blur-md px-4 py-3 flex items-center justify-between shadow-md shadow-slate-900/5">
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
                <div class="w-7 h-7 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    {{ strtoupper(substr($storeName, 0, 2)) }}
                </div>
                <span class="text-xs font-bold text-slate-900 truncate max-w-[140px]">{{ $storeName }}</span>
            </a>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('admin.sales.create') }}"
                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-emerald-600 text-white text-[11px] font-bold uppercase tracking-wider shadow-sm"
            >
                POS
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-2.5 py-1.5 rounded-full bg-slate-100 text-[10px] font-bold text-slate-600 uppercase shadow-xs">
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
        class="md:hidden bg-white px-4 py-4 space-y-2 shadow-2xl rounded-b-3xl"
        style="display: none;"
    >
        <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
            <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900 shadow-xs">Dashboard</a>
            <a href="{{ route('admin.sales.create') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 shadow-xs">Make Sale (POS)</a>
            <a href="{{ route('admin.orders.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900 shadow-xs">Orders</a>
            <a href="{{ route('admin.products.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900 shadow-xs">Products</a>
            <a href="{{ route('admin.stock.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900 shadow-xs">Stock Inventory</a>
            <a href="{{ route('admin.ocr.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900 shadow-xs">Scan Receipts</a>
            @if(Auth::user()->isAdmin())

                <a href="{{ route('admin.reports.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900 shadow-xs">Sales Reports</a>
                <a href="{{ route('admin.staff.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900 shadow-xs">Staff &amp; Team</a>
                <a href="{{ route('admin.payroll.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900 shadow-xs">Salaries &amp; Payroll</a>
                @if (!session()->has('impersonator_id'))
                    <form method="POST" action="{{ route('admin.impersonate-staff') }}">
                        @csrf
                        <button type="submit" class="w-full text-left p-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-semibold shadow-xs">
                            Login as Staff &nearr;
                        </button>
                    </form>
                @endif
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
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-semibold flex items-center justify-between gap-3 shadow-md shadow-emerald-950/5">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold shrink-0">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-800 text-xs font-semibold flex items-center justify-between gap-3 shadow-md shadow-red-950/5">
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
