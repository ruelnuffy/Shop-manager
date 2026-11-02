<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-black text-white">
                        SS / 2026
                    </span>
                    <span class="text-xs text-brand-charcoal/50 font-medium">
                        {{ $products->total() }} {{ Str::plural('piece', $products->total()) }} available
                    </span>
                </div>
                <h1 class="font-display font-extrabold text-3xl sm:text-5xl uppercase tracking-tight text-brand-charcoal">
                    Curated Atelier Catalog &bull; {{ request('category') ? strtoupper(request('category')) : 'ALL PIECES' }}
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('cart.index') }}" class="inline-flex items-center px-4 py-2 rounded-full bg-black text-white hover:bg-brand-pink text-xs font-bold uppercase tracking-wider transition duration-150 shadow-sm">
                    Shopping Bag ({{ app(\App\Services\CartService::class)->getItemCount() }})
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter & Search Toolbar -->
            <div class="bg-white rounded-2xl p-6 border border-black/[0.06] mb-8 shadow-sm">
                <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                        <!-- Search input -->
                        <div class="md:col-span-4 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-charcoal/40">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, tags, description..." class="w-full pl-10 pr-4 py-2 bg-[#f4f4f4] border-transparent focus:border-black/20 focus:bg-white focus:ring-0 rounded-xl text-xs font-medium text-brand-charcoal">
                        </div>

                        <!-- Category select -->
                        <div class="md:col-span-3">
                            <select name="category" class="w-full py-2 px-3 bg-[#f4f4f4] border-transparent focus:border-black/20 focus:bg-white focus:ring-0 rounded-xl text-xs font-medium text-brand-charcoal">
                                <option value="">Filter by Category (All)</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ $cat->products_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort select -->
                        <div class="md:col-span-3">
                            <select name="sort" class="w-full py-2 px-3 bg-[#f4f4f4] border-transparent focus:border-black/20 focus:bg-white focus:ring-0 rounded-xl text-xs font-medium text-brand-charcoal">
                                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest Drops</option>
                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Alphabetical (A - Z)</option>
                            </select>
                        </div>

                        <!-- Filter trigger buttons -->
                        <div class="md:col-span-2 flex items-center gap-2">
                            <button type="submit" class="w-full py-2 px-4 rounded-xl bg-black hover:bg-brand-pink text-white font-bold text-xs uppercase tracking-wider transition duration-150">
                                Filter
                            </button>
                            @if(request()->hasAny(['q', 'category', 'sort', 'min_price', 'max_price', 'size', 'in_stock']))
                                <a href="{{ route('products.index') }}" class="p-2 rounded-xl bg-[#f4f4f4] text-brand-charcoal/60 hover:text-black hover:bg-black/10 transition" title="Clear Filters">
                                    ✕
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Secondary Refinements (Price & In-Stock) -->
                    <div class="pt-3 border-t border-black/[0.05] flex flex-wrap items-center justify-between gap-4 text-xs">
                        <div class="flex items-center gap-4 flex-wrap">
                            <span class="font-bold text-brand-charcoal/60 uppercase text-[10px] tracking-wider">Refine:</span>

                            <!-- Sizes -->
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @foreach ($availableSizes as $size)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="size" value="{{ $size }}" {{ request('size') === $size ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold border border-black/10 peer-checked:bg-black peer-checked:text-white peer-checked:border-black hover:bg-black/5 transition">
                                            {{ $size }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-brand-charcoal/80">
                                <input type="checkbox" name="has_video" value="1" {{ request('has_video') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-black/20 text-brand-pink focus:ring-brand-pink">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3 h-3 text-brand-pink fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    Video Reel Only
                                </span>
                            </label>

                            <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-brand-charcoal/80">
                                <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-black/20 text-black focus:ring-black/20">
                                In-Stock Only
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Product Grid -->
            @if ($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($products as $index => $prod)
                        <div class="group bg-white rounded-2xl overflow-hidden border border-black/[0.06] hover:border-black/20 hover:shadow-xl hover:shadow-black/5 transition-all duration-300 flex flex-col" x-data="{ isHovered: false }" @mouseenter="isHovered = true" @mouseleave="isHovered = false">
                            <!-- Image / Video Frame -->
                            <a href="{{ route('products.show', $prod->slug) }}" class="aspect-[4/5] relative overflow-hidden bg-[#f2f2f2] block">
                                <img src="{{ asset($prod->featured_image) }}" alt="{{ $prod->name }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500" :class="isHovered && '{{ $prod->video_url }}' ? 'opacity-0' : 'opacity-100'">

                                @if ($prod->video_url)
                                    <!-- Hover Video Preview -->
                                    <video
                                        src="{{ asset($prod->video_url) }}"
                                        class="w-full h-full object-cover object-center absolute inset-0 transition duration-300"
                                        x-show="isHovered"
                                        x-cloak
                                        autoplay
                                        muted
                                        loop
                                        playsinline
                                    ></video>
                                @endif

                                <div class="absolute top-3 left-3 z-10">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/95 text-brand-charcoal border border-black/5 shadow-sm">
                                        {{ $prod->category->name ?? 'ClassiQ' }}
                                    </span>
                                </div>

                                <div class="absolute top-3 right-3 z-10 flex items-center gap-1.5">
                                    @if ($prod->video_url)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black/80 backdrop-blur-md text-brand-pink border border-white/20 shadow-sm flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            Video Reel
                                        </span>
                                    @endif
                                    @if ($prod->inStock())
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black text-white shadow-sm">
                                            Stock: {{ $prod->totalStock() }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-brand-pink text-white shadow-sm">
                                            Sold Out
                                        </span>
                                    @endif
                                </div>
                            </a>

                            <!-- Metadata -->
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <a href="{{ route('products.show', $prod->slug) }}">
                                        <h3 class="font-display font-bold text-lg text-brand-charcoal group-hover:underline underline-offset-4 leading-snug">
                                            {{ $prod->name }}
                                        </h3>
                                    </a>
                                    <span class="font-bold text-base text-brand-charcoal shrink-0">
                                        ₦{{ number_format($prod->base_price, 0) }}
                                    </span>
                                </div>

                                <p class="text-xs text-brand-charcoal/60 line-clamp-2 leading-relaxed mb-4 flex-grow">
                                    {{ $prod->description }}
                                </p>

                                <!-- Variants preview -->
                                <div class="flex flex-wrap gap-1 mb-5">
                                    @foreach ($prod->variants as $variant)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-[#f5f5f5] text-brand-charcoal/70 border border-black/[0.04]">
                                            {{ $variant->size }} &bull; {{ $variant->color }}
                                        </span>
                                    @endforeach
                                </div>

                                <div class="pt-4 border-t border-black/[0.06] flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-brand-charcoal/50 uppercase tracking-wider">
                                        Jos Atelier
                                    </span>
                                    <a href="{{ route('products.show', $prod->slug) }}" class="px-4 py-2 rounded-full bg-black hover:bg-brand-pink text-white text-xs font-bold uppercase tracking-wider transition duration-150">
                                        View Piece &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-white rounded-3xl p-16 text-center border border-black/[0.06]">
                    <div class="w-14 h-14 rounded-full bg-black/5 text-brand-charcoal flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-brand-charcoal/60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-xl uppercase tracking-tight text-brand-charcoal">
                        No pieces found matching your criteria
                    </h3>
                    <p class="text-xs text-brand-charcoal/60 mt-1 max-w-sm mx-auto">
                        Try resetting your search query or selecting a different category from our boutique collection.
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-6 px-6 py-2.5 rounded-full bg-black text-white text-xs font-bold uppercase tracking-wider hover:bg-brand-pink transition">
                        Reset All Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
