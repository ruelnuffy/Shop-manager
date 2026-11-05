<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Super Admin Cockpit' }} &bull; {{ config('app.platform_name', 'ShopManager') }}</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'system-ui', '-apple-system', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body, button, input, select, textarea, table, h1, h2, h3, h4, span, p, a {
            font-family: 'Poppins', system-ui, -apple-system, sans-serif !important;
        }
    </style>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 min-h-screen flex selection:bg-indigo-500 selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- ========================================================= -->
    <!-- LEFT SIDEBAR NAVBAR                                       -->
    <!-- ========================================================= -->
    <aside class="hidden lg:flex lg:flex-col w-64 bg-white border-r border-slate-200 shrink-0 sticky top-0 h-screen z-40 justify-between">
        <div class="flex flex-col h-full overflow-y-auto">
            <!-- Brand -->
            <div class="p-6 pb-4 border-b border-slate-100">
                <a href="{{ route('super-admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-xs shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-sm leading-tight">
                            {{ config('app.platform_name', 'ShopManager') }}
                        </div>
                        <span class="inline-block text-[10px] font-semibold text-rose-600 bg-rose-50 px-1.5 py-0.2 rounded mt-0.5">
                            Super Admin
                        </span>
                    </div>
                </a>

                <!-- Quick Store Jump -->
                @php
                    $allStoresList = \App\Models\Tenant::where('status', 'active')->orderBy('name')->get();
                @endphp
                <div class="mt-4 relative" x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        type="button"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-medium text-slate-700 transition"
                    >
                        <span class="truncate">Select Store</span>
                        <span class="text-[10px] text-slate-400 font-medium">({{ $allStoresList->count() }}) &darr;</span>
                    </button>

                    <div
                        x-show="open"
                        @click.outside="open = false"
                        class="absolute left-0 right-0 mt-1 rounded-xl bg-white border border-slate-200 shadow-lg p-1.5 z-50 space-y-0.5 max-h-56 overflow-y-auto"
                        style="display: none;"
                    >
                        @forelse ($allStoresList as $storeOption)
                            <a
                                href="{{ url('/admin?tenant=' . $storeOption->slug) }}"
                                class="flex items-center justify-between px-2.5 py-1.5 rounded-md hover:bg-indigo-50 text-xs text-slate-700 hover:text-indigo-600 transition"
                            >
                                <span class="truncate">{{ $storeOption->name }}</span>
                                <span class="text-[10px] text-indigo-600">&rarr;</span>
                            </a>
                        @empty
                            <div class="text-xs text-slate-400 p-2">No active stores</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-3 space-y-1 flex-1 text-xs font-medium">
                <a
                    href="{{ route('super-admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('super-admin.dashboard') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Platform Overview</span>
                </a>

                <a
                    href="{{ route('super-admin.tenants.index') }}"
                    class="flex items-center justify-between px-3 py-2.5 rounded-lg transition {{ request()->routeIs('super-admin.tenants.index') || request()->routeIs('super-admin.tenants.show') || request()->routeIs('super-admin.tenants.edit') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Tenant Stores</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[10px] {{ request()->routeIs('super-admin.tenants.index') ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $allStoresList->count() }}
                    </span>
                </a>

                <a
                    href="{{ route('super-admin.tenants.create') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('super-admin.tenants.create') ? 'bg-indigo-600 text-white font-semibold' : 'text-indigo-600 hover:bg-indigo-50' }}"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Provision Store</span>
                </a>

                <div class="pt-3 border-t border-slate-100 my-2"></div>

                <a
                    href="{{ route('super-admin.export-report') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition"
                >
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Export CSV</span>
                </a>

                <form method="POST" action="{{ route('super-admin.clear-cache') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition"
                    >
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Clear Cache</span>
                    </button>
                </form>

                <a
                    href="{{ route('home') }}"
                    target="_blank"
                    class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-50 transition"
                >
                    <span>Platform Site</span>
                    <span>&nearr;</span>
                </a>
            </nav>

            <!-- Bottom User -->
            <div class="p-3 border-t border-slate-100">
                <div class="flex items-center justify-between gap-2 p-2 rounded-lg bg-slate-50">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-semibold text-slate-900 truncate">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-slate-500 truncate">{{ Auth::user()->email }}</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            title="Sign Out"
                            class="p-1.5 rounded-md hover:bg-slate-200 text-slate-400 hover:text-slate-700 transition"
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
    <!-- MAIN CONTENT AREA                                         -->
    <!-- ========================================================= -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Mobile Header Bar -->
        <header class="lg:hidden w-full bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between">
            <button
                type="button"
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <span class="font-bold text-slate-900 text-sm">ShopManager</span>
            <div class="w-8"></div>
        </header>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenuOpen" class="lg:hidden p-4 bg-white border-b border-slate-200 space-y-2 text-xs" style="display: none;">
            <a href="{{ route('super-admin.dashboard') }}" class="block p-2 rounded text-slate-800 hover:bg-slate-100">Platform Overview</a>
            <a href="{{ route('super-admin.tenants.index') }}" class="block p-2 rounded text-slate-800 hover:bg-slate-100">Tenant Stores</a>
            <a href="{{ route('super-admin.tenants.create') }}" class="block p-2 rounded text-indigo-600 hover:bg-indigo-50">+ Provision Store</a>
            <a href="{{ route('super-admin.export-report') }}" class="block p-2 rounded text-slate-800 hover:bg-slate-100">Export CSV</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left p-2 rounded text-rose-600 hover:bg-rose-50">Sign Out</button>
            </form>
        </div>

        <!-- Main Body -->
        <main class="flex-1 p-6 sm:p-8 space-y-6 max-w-7xl w-full mx-auto">
            @if (session('status'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between">
                    <span>{{ session('status') }}</span>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 text-sm">&times;</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <div>&bull; {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>
</html>
