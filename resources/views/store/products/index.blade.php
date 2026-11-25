@php
    $storeTenant = app(\App\Services\CurrentTenant::class)->get();
    $storeBrandName = $storeTenant ? $storeTenant->setting('branding.name', $storeTenant->name) : 'Store';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 py-2">
            <div class="space-y-2">
                <div class="flex items-center gap-2.5">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-brand-primary text-white">
                        {{ $storeBrandName }}
                    </span>
                    <span class="text-xs text-slate-500 font-medium">
                        {{ $products->total() }} {{ Str::plural('item', $products->total()) }} available
                    </span>
                </div>
                <h1 class="font-display font-extrabold text-3xl sm:text-5xl tracking-tight text-slate-900">
                    {{ request('category') ? ucwords(str_replace('-', ' ', request('category'))) : 'Store Catalog' }}
                </h1>
            </div>

            <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-900 text-white hover:bg-slate-800 text-xs font-bold uppercase tracking-wider transition shadow-sm self-start sm:self-auto">
                <span>Shopping Bag</span>
                <span class="px-2 py-0.5 rounded-full bg-white/15 text-[11px]">{{ app(\App\Services\CartService::class)->getItemCount() }}</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <!-- Category Pills + Clean Search/Sort Bar -->
            <div class="space-y-5">
                @if ($categories->count() > 0)
                    <div class="flex items-center gap-2 flex-wrap">
                        <a
                            href="{{ route('products.index', request()->except('category', 'page')) }}"
                            class="px-4 py-2 rounded-full text-xs font-bold transition {{ !request('category') ? 'bg-brand-primary text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:border-slate-300' }}"
                        >
                            All Items
                        </a>
                        @foreach ($categories->where('products_count', '>', 0) as $cat)
                            <a
                                href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                                class="px-4 py-2 rounded-full text-xs font-bold transition {{ request('category') === $cat->slug ? 'bg-brand-primary text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:border-slate-300' }}"
                            >
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <form method="GET" action="{{ route('products.index') }}" class="bg-white rounded-2xl p-3 sm:p-4 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search products..."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-transparent focus:border-slate-300 focus:bg-white focus:ring-0 rounded-xl text-xs font-medium text-slate-900"
                        >
                    </div>

                    <select name="sort" onchange="this.form.submit()" class="sm:w-48 py-2.5 px-3.5 bg-slate-50 border-transparent focus:border-slate-300 focus:bg-white focus:ring-0 rounded-xl text-xs font-medium text-slate-700">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest Arrivals</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name: A &ndash; Z</option>
                    </select>

                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider transition cursor-pointer">
                        Search
                    </button>

                    @if (request()->hasAny(['q', 'category', 'sort', 'in_stock']))
                        <a href="{{ route('products.index') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold text-center transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Spacious Product Grid -->
            @if ($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
                    @foreach ($products as $prod)
                        <div class="group bg-white rounded-3xl overflow-hidden border border-slate-200/80 hover:border-slate-300 hover:shadow-xl hover:shadow-slate-900/5 transition-all duration-300 flex flex-col">
                            <a href="{{ route('products.show', $prod->slug) }}" class="aspect-square relative overflow-hidden bg-slate-50 block">
                                @if ($prod->featured_image)
                                    <img
                                        src="{{ str_starts_with($prod->featured_image, 'http') ? $prod->featured_image : asset($prod->featured_image) }}"
                                        alt="{{ $prod->name }}"
                                        class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
                                    >
                                @endif

                                @if ($prod->category)
                                    <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/95 text-slate-700 shadow-xs">
                                        {{ $prod->category->name }}
                                    </span>
                                @endif

                                @if (! $prod->inStock())
                                    <span class="absolute top-4 right-4 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-600 text-white shadow-xs">
                                        Sold Out
                                    </span>
                                @endif
                            </a>

                            <div class="p-6 flex flex-col flex-grow space-y-4">
                                <div class="flex items-start justify-between gap-3">
                                    <a href="{{ route('products.show', $prod->slug) }}" class="flex-1 min-w-0">
                                        <h3 class="font-display font-bold text-base sm:text-lg text-slate-900 group-hover:text-blue-600 transition leading-snug line-clamp-2">
                                            {{ $prod->name }}
                                        </h3>
                                    </a>
                                    <span class="font-display font-extrabold text-base text-slate-900 shrink-0">
                                        ₦{{ number_format($prod->base_price, 0) }}
                                    </span>
                                </div>

                                @if ($prod->description)
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed flex-grow">
                                        {{ $prod->description }}
                                    </p>
                                @endif

                                @php
                                    $productUrl = route('products.show', $prod->slug);
                                    $productPriceLabel = '₦'.number_format($prod->base_price, 0);
                                    $productImageUrl = $prod->featured_image
                                        ? (str_starts_with($prod->featured_image, 'http') ? $prod->featured_image : asset($prod->featured_image))
                                        : '';
                                    $whatsappShareText = $prod->name.' — '.$productPriceLabel."\nCheck website for more details:\n".$productUrl;
                                @endphp
                                <div class="pt-4 border-t border-slate-100 space-y-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <button
                                            type="button"
                                            onclick="event.preventDefault(); navigator.clipboard?.writeText('{{ $productUrl }}'); this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy Link', 1500);"
                                            class="px-3 py-1.5 rounded-full border border-slate-200 hover:border-slate-400 text-slate-700 text-[11px] font-bold transition cursor-pointer"
                                        >
                                            Copy Link
                                        </button>
                                        <a
                                            href="https://wa.me/?text={{ rawurlencode($whatsappShareText) }}"
                                            target="_blank"
                                            rel="noopener"
                                            onclick="if (window.shareProductToWhatsApp) { event.preventDefault(); window.shareProductToWhatsApp({{ \Illuminate\Support\Js::from($prod->name) }}, {{ \Illuminate\Support\Js::from($productPriceLabel) }}, {{ \Illuminate\Support\Js::from($productUrl) }}, {{ \Illuminate\Support\Js::from($productImageUrl) }}); }"
                                            class="px-3 py-1.5 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-[11px] font-bold transition inline-flex items-center gap-1"
                                        >
                                            <span>Post to WhatsApp</span>
                                        </a>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-medium text-slate-400">
                                            {{ $prod->inStock() ? 'In Stock' : 'Out of Stock' }}
                                        </span>
                                        <a href="{{ $productUrl }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-brand-secondary hover:bg-brand-pink text-white text-xs font-bold transition">
                                            <span>View Item</span>
                                            <span>&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($products->hasPages())
                    <div class="pt-4">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white rounded-3xl p-16 text-center border border-slate-200/80 space-y-4">
                    <h3 class="font-display font-bold text-xl text-slate-900">
                        No products found
                    </h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        Try clearing your search or selecting another category.
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-block px-6 py-2.5 rounded-full bg-slate-900 text-white text-xs font-bold uppercase tracking-wider hover:bg-slate-800 transition">
                        View All Products
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
