@php
    $navTenant = app(\App\Services\CurrentTenant::class)->get();
    $navStoreName = $navTenant ? $navTenant->setting('branding.name', $navTenant->name) : config('app.platform_name', 'ShopManager');
    $navHomeUrl = $navTenant ? route('store.share', $navTenant->slug) : route('home');
    $navCartCount = array_sum(array_column(session('cart', []), 'quantity'));
    $navCategories = $navTenant
        ? \App\Models\Category::withCount('products')->orderBy('sort_order')->get()->filter(fn ($c) => $c->products_count > 0)
        : collect();
@endphp

<header x-data="{ mobileOpen: false }" class="w-full bg-white/95 backdrop-blur-md border-b border-black/[0.06] sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20 gap-4">
            <!-- Left: Logo & Micro Links -->
            <div class="flex items-center gap-6 sm:gap-8">
                <a href="{{ $navHomeUrl }}" class="group" title="{{ $navStoreName }} — Return to Storefront">
                    <x-application-logo />
                </a>

                <nav class="hidden md:flex items-center space-x-6 text-xs font-semibold tracking-wider uppercase text-brand-charcoal/70">
                    <a href="{{ route('products.index') }}" class="hover:text-black transition duration-150 {{ request()->routeIs('products.*') ? 'text-black font-bold' : '' }}">
                        Catalog
                    </a>

                    @if ($navCategories->count() > 0)
                        <div class="relative group">
                            <button type="button" class="flex items-center gap-1 hover:text-black transition duration-150 py-2">
                                <span>Collections</span>
                                <svg class="w-3 h-3 text-brand-charcoal/40 group-hover:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="absolute left-0 top-full pt-1 w-48 hidden group-hover:block z-50">
                                <div class="bg-white rounded-xl shadow-xl border border-black/10 py-2">
                                    @foreach ($navCategories as $navCat)
                                        <a href="{{ route('products.index', ['category' => $navCat->slug]) }}" class="block px-4 py-2 text-xs font-medium text-brand-charcoal hover:bg-black/5 hover:text-black transition">
                                            {{ $navCat->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('store.location') }}" class="hover:text-black transition duration-150 {{ request()->routeIs('store.location') ? 'text-black font-bold' : '' }}">
                        Store Location
                    </a>
                    <a href="{{ $navHomeUrl }}#boutique" class="hover:text-black transition duration-150">
                        About
                    </a>
                </nav>
            </div>

            <!-- Center: Sleek Search Pill -->
            <div class="hidden sm:flex flex-1 max-w-md mx-4">
                <form action="{{ route('products.index') }}" method="GET" class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-charcoal/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search products..."
                        class="w-full pl-9 pr-4 py-2 bg-[#f0f0f0] hover:bg-[#eaeaea] focus:bg-white border-transparent focus:border-black/15 focus:ring-0 rounded-full text-xs font-medium placeholder-brand-charcoal/45 text-brand-charcoal transition duration-150"
                    >
                </form>
            </div>

            <!-- Right: Cart & Shop Now -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('cart.index') }}" class="relative p-2 rounded-full hover:bg-black/5 text-brand-charcoal transition duration-150" title="Shopping Bag">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    @if ($navCartCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-brand-pink text-white text-[10px] font-bold flex items-center justify-center">
                            {{ $navCartCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('products.index') }}" class="hidden sm:inline-flex items-center px-4 py-2 rounded-full bg-black text-white hover:bg-brand-pink hover:text-white text-xs font-bold tracking-wider uppercase transition duration-150 shadow-sm">
                    Shop Now
                </a>

                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    class="md:hidden p-2 rounded-lg text-brand-charcoal hover:bg-black/5 transition"
                    aria-label="Toggle Menu"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileOpen" x-transition class="md:hidden bg-white border-t border-black/[0.06] px-4 py-3 space-y-2 text-xs font-semibold uppercase tracking-wider" style="display: none;">
        <a href="{{ $navHomeUrl }}" class="block py-2 text-brand-charcoal hover:text-black">Home</a>
        <a href="{{ route('products.index') }}" class="block py-2 text-brand-charcoal hover:text-black">Catalog</a>
        <a href="{{ route('store.location') }}" class="block py-2 text-brand-charcoal hover:text-black">Store Location</a>
        <a href="{{ route('cart.index') }}" class="block py-2 text-brand-charcoal hover:text-black">Shopping Bag ({{ $navCartCount }})</a>
    </div>
</header>
