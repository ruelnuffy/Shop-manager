<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Stores Registered on {{ config('app.platform_name', 'ShopManager') }} &bull; Retail &amp; Boutique Directory</title>
    <meta name="description" content="Explore registered fashion boutiques, retail merchants, and storefronts operating on ShopManager.">

    <!-- Modern Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#fbfcfe] text-slate-900 min-h-screen selection:bg-blue-600 selection:text-white flex flex-col">

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- FLOATING TOP PILL NAVIGATION (Matching platform landing)    -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <header class="sticky top-3 z-50 px-4 sm:px-6 pt-1 pb-3">
        <div class="max-w-6xl mx-auto bg-white/95 backdrop-blur-md shadow-xl shadow-slate-900/5 rounded-full px-4 sm:px-6 py-2.5 sm:py-3 flex items-center justify-between gap-4 transition-all border border-slate-200/60">

            <!-- Logo -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <img
                        src="{{ asset('images/shopmanager-logo.png') }}"
                        alt="{{ config('app.platform_name', 'ShopManager') }}"
                        class="h-7 sm:h-8 md:h-9 w-auto object-contain max-w-[170px] sm:max-w-[210px] drop-shadow-xs group-hover:opacity-95 transition"
                    />
                    <span class="sr-only">{{ config('app.platform_name', 'ShopManager') }}</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-1 p-1 bg-slate-100/70 rounded-full border border-slate-200/50 text-xs sm:text-sm font-semibold text-slate-600">
                <a href="{{ route('home') }}" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">Home</a>
                <a href="{{ route('home') }}#features" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">Features</a>
                <a href="{{ route('home') }}#pricing" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">Pricing</a>
                <a href="{{ route('home') }}#faq" class="px-3.5 py-1.5 rounded-full hover:text-slate-950 hover:bg-white transition">FAQ</a>
                <a href="{{ route('stores.index') }}" class="px-3.5 py-1.5 rounded-full bg-white text-slate-950 font-bold shadow-xs">Stores</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold uppercase tracking-wider transition shadow-md shadow-blue-600/25 active:scale-95">
                        <span>Dashboard</span>
                        <span>&rarr;</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs sm:text-sm font-bold text-slate-700 hover:text-slate-950 transition px-2 sm:px-3 py-1.5">
                        Sign In
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold uppercase tracking-wider transition shadow-md shadow-blue-600/25 active:scale-95"
                    >
                        <span>Start Free Trial</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- HERO HEADER & DIRECTORY SEARCH                              -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <main class="flex-1 pb-20">
        <section class="pt-8 sm:pt-14 pb-12 sm:pb-16 bg-gradient-to-b from-blue-50/50 via-white to-transparent border-b border-slate-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">

                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-bold uppercase tracking-wider shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    <span>Live Merchant Directory</span>
                </div>

                <h1 class="font-display font-extrabold text-3xl sm:text-5xl lg:text-6xl text-slate-950 tracking-tight leading-tight max-w-4xl mx-auto">
                    Stores Registered on <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">{{ config('app.platform_name', 'ShopManager') }}</span>
                </h1>

                <p class="text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                    Discover verified fashion boutiques, apparel ateliers, and retail merchants running their in-store counters and online storefronts on ShopManager.
                </p>

                <!-- Search Input Bar -->
                <div class="max-w-2xl mx-auto pt-2">
                    <form method="GET" action="{{ route('stores.index') }}" class="relative flex items-center">
                        <div class="absolute inset-y-0 left-0 pl-4 sm:pl-5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Search by store name, city, or category..."
                            class="w-full pl-12 sm:pl-14 pr-28 sm:pr-32 py-3.5 sm:py-4 rounded-full bg-white border border-slate-200 shadow-lg shadow-slate-900/5 text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition"
                        />
                        <button
                            type="submit"
                            class="absolute right-2 sm:right-2.5 px-4 sm:px-6 py-2 sm:py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold transition shadow-xs cursor-pointer"
                        >
                            Search
                        </button>
                    </form>

                    @if ($search !== '')
                        <div class="mt-3 flex items-center justify-center gap-2 text-xs text-slate-500">
                            <span>Filtering for "<strong>{{ $search }}</strong>"</span>
                            <span>&bull;</span>
                            <a href="{{ route('stores.index') }}" class="text-blue-600 font-bold hover:underline">
                                Clear search &times;
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Live Counter Stats -->
                <div class="flex items-center justify-center gap-6 sm:gap-8 pt-4 text-xs font-semibold text-slate-500">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span><strong>{{ $stores->count() }}</strong> @if($search !== '') matching @else registered @endif {{ \Illuminate\Support\Str::plural('store', $stores->count()) }}</span>
                    </div>
                    <span>&bull;</span>
                    <div>
                        <span><strong>{{ $totalStoresCount }}</strong> active on platform</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- STORES GRID                                                 -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 sm:pt-14">
            @if ($stores->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($stores as $store)
                        @php
                            $primaryColor = data_get($store->settings, 'branding.palette.primary', '#2563eb');
                            $logoUrl = data_get($store->settings, 'branding.logo_url');
                            $tagline = data_get($store->settings, 'branding.tagline', 'Curated Boutique & Ready-to-Wear');
                            $locationBadge = $store->locationBadge();
                            $leadName = $store->leadName();
                            $leadRole = $store->leadRole();
                            $leadStatus = $store->leadStatus();
                        @endphp
                        <div class="rounded-3xl bg-white border border-slate-200/80 p-6 shadow-sm hover:shadow-xl hover:shadow-slate-900/5 transition duration-300 flex flex-col justify-between gap-5 group">
                            <!-- Top Brand Info -->
                            <div class="space-y-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        @if ($logoUrl)
                                            <div class="w-12 h-12 rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 p-1 flex items-center justify-center shrink-0">
                                                <img src="{{ $logoUrl }}" alt="{{ $store->name }}" class="w-full h-full object-contain">
                                            </div>
                                        @else
                                            <div
                                                class="w-12 h-12 rounded-2xl flex items-center justify-center font-extrabold text-sm text-white shadow-xs shrink-0 group-hover:scale-105 transition-transform"
                                                style="background-color: {{ $primaryColor }};"
                                            >
                                                {{ strtoupper(substr($store->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <h2 class="font-bold text-slate-900 text-base leading-tight truncate group-hover:text-blue-600 transition">
                                                {{ $store->name }}
                                            </h2>
                                            <span class="text-xs text-slate-400 font-mono block truncate">
                                                @if ($store->subdomain) {{ $store->subdomain }}.shopmanager @else {{ $store->slug }} @endif
                                            </span>
                                        </div>
                                    </div>

                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100 shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Live</span>
                                    </span>
                                </div>

                                <p class="text-xs sm:text-sm text-slate-600 line-clamp-2 leading-relaxed">
                                    {{ $tagline }}
                                </p>

                                <!-- Meta Badges -->
                                <div class="space-y-2 pt-1 border-t border-slate-100 text-xs text-slate-600">
                                    <div class="flex items-center gap-2 truncate">
                                        <span class="text-slate-400 text-sm">📍</span>
                                        <span class="truncate font-medium">{{ $locationBadge }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2 text-[11px] text-slate-500">
                                        <div class="flex items-center gap-1.5 truncate">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                            <span class="truncate">{{ $leadName }} &bull; {{ $leadRole }}</span>
                                        </div>
                                        <div class="font-bold text-slate-700 shrink-0">
                                            {{ $store->products_count }} {{ \Illuminate\Support\Str::plural('item', $store->products_count) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Action Buttons -->
                            <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                                <a
                                    href="{{ route('storefront', ['tenant' => $store->slug]) }}"
                                    class="flex-1 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold text-center transition flex items-center justify-center gap-1.5 shadow-xs"
                                >
                                    <span>Storefront</span>
                                    <span class="text-slate-400">&nearr;</span>
                                </a>
                                <a
                                    href="{{ route('login') }}"
                                    class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold text-center transition"
                                    title="Store Admin Login"
                                >
                                    <span>Admin &rarr;</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="max-w-md mx-auto text-center py-16 px-4 bg-white rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                        🔍
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold text-slate-900 text-lg">No stores found</h3>
                        <p class="text-xs text-slate-500">
                            @if ($search !== '')
                                We couldn't find any stores matching "{{ $search }}". Try searching with a different name or city.
                            @else
                                No active stores are currently registered.
                            @endif
                        </p>
                    </div>
                    @if ($search !== '')
                        <div>
                            <a href="{{ route('stores.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold transition">
                                <span>View all registered stores</span>
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- ONBOARD YOUR STORE CTA                                      -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 sm:mt-24">
            <div class="p-8 sm:p-12 rounded-3xl bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-700 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl">
                <div class="space-y-2 text-center md:text-left">
                    <span class="px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider">
                        Start Today
                    </span>
                    <h2 class="font-display font-extrabold text-2xl sm:text-4xl text-white tracking-tight">
                        Ready to open your own store?
                    </h2>
                    <p class="text-xs sm:text-sm text-blue-100 max-w-lg">
                        Get your in-person walk-in counter, thermal Bluetooth receipt printing, and online boutique storefront ready in under 2 minutes.
                    </p>
                </div>

                <div class="shrink-0">
                    <a
                        href="{{ route('register') }}"
                        class="px-8 py-3.5 rounded-full bg-white hover:bg-slate-100 text-blue-800 font-extrabold text-xs sm:text-sm uppercase tracking-wider transition shadow-lg active:scale-95 inline-flex items-center gap-2"
                    >
                        <span>Register Your Store</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- FOOTER                                                      -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <footer class="bg-white border-t border-slate-100 py-12 text-slate-600 text-xs sm:text-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img
                    src="{{ asset('images/shopmanager-logo.png') }}"
                    alt="{{ config('app.platform_name', 'ShopManager') }}"
                    class="h-7 w-auto object-contain"
                />
                <span class="text-xs text-slate-400">&bull; &copy; {{ date('Y') }} All rights reserved.</span>
            </div>

            <div class="flex items-center gap-4 text-xs font-semibold text-slate-600">
                <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Platform Home</a>
                <a href="{{ route('stores.index') }}" class="hover:text-blue-600 transition">Stores Directory</a>
                <a href="{{ route('login') }}" class="hover:text-blue-600 transition">Sign In</a>
                <a href="{{ route('register') }}" class="hover:text-blue-600 transition">Start Free Trial</a>
            </div>
        </div>
    </footer>

</body>
</html>
