<x-app-layout>
    @php
        $tenant = app(\App\Services\CurrentTenant::class)->get() ?: $product->tenant;
        $tenantWhatsapp = $tenant ? $tenant->whatsappNumber() : '2349069498376';
        $tenantPhone = $tenant ? $tenant->customerCarePhone() : '+234 906 949 8376';
        $tenantBrand = $tenant ? $tenant->setting('branding.name', $tenant->name) : 'Shop';
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-charcoal/50">
                <a href="/" class="hover:text-black">Home</a>
                <span>&bull;</span>
                <a href="{{ route('products.index', ['category' => $product->category->slug ?? '']) }}" class="hover:text-black">
                    {{ $product->category->name ?? 'Collection' }}
                </a>
                <span>&bull;</span>
                <span class="text-brand-charcoal">{{ $product->name }}</span>
            </nav>
            <a href="{{ route('cart.index') }}" class="inline-flex items-center px-4 py-2 rounded-full bg-black text-white hover:bg-brand-pink text-xs font-bold uppercase tracking-wider transition duration-150 shadow-sm">
                Shopping Bag ({{ app(\App\Services\CartService::class)->getItemCount() }})
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        selectedVariantId: {{ $product->activeVariants->first()->id ?? 'null' }},
        selectedPrice: {{ $product->activeVariants->first()->effective_price ?? $product->base_price }},
        selectedSku: '{{ $product->activeVariants->first()->sku ?? 'JC-DEFAULT' }}',
        selectedStock: {{ $product->activeVariants->first()->stock_quantity ?? 0 }},
        selectedImage: '{{ asset($product->featured_image) }}',
        activeMedia: '{{ $product->video_url ? 'video' : 'photo' }}',
        isVideoMuted: true,
        quantity: 1,
        variants: {{ $product->activeVariants->toJson() }},
        selectVariant(v) {
            this.selectedVariantId = v.id;
            this.selectedPrice = v.price_override ? v.price_override : {{ $product->base_price }};
            this.selectedSku = v.sku;
            this.selectedStock = v.stock_quantity;
            if (this.quantity > this.selectedStock && this.selectedStock > 0) {
                this.quantity = this.selectedStock;
            }
        },
        toggleSound() {
            const vid = this.$refs.prodVideo;
            if (vid) {
                vid.muted = !vid.muted;
                this.isVideoMuted = vid.muted;
            }
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Product Details Grid -->
            <div class="bg-white rounded-3xl p-6 sm:p-12 border border-black/[0.06] shadow-sm mb-16">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    <!-- Left: Product Media Gallery & Video Reel -->
                    <div class="lg:col-span-6 space-y-4">
                        <!-- Media View Switcher -->
                        @if ($product->video_url)
                            <div class="flex items-center gap-2 p-1 bg-[#f0f0f0] rounded-xl w-fit">
                                <button
                                    type="button"
                                    @click="activeMedia = 'video'"
                                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition duration-150 flex items-center gap-1.5"
                                    :class="activeMedia === 'video' ? 'bg-black text-white shadow-sm' : 'text-brand-charcoal/70 hover:text-black'"
                                >
                                    <svg class="w-3.5 h-3.5 text-brand-pink" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                    Video Reel (Hands-On)
                                </button>
                                <button
                                    type="button"
                                    @click="activeMedia = 'photo'"
                                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition duration-150 flex items-center gap-1.5"
                                    :class="activeMedia === 'photo' ? 'bg-black text-white shadow-sm' : 'text-brand-charcoal/70 hover:text-black'"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Photo Gallery
                                </button>
                            </div>
                        @endif

                        <!-- Video Container -->
                        @if ($product->video_url)
                            <div x-show="activeMedia === 'video'" class="aspect-[4/5] rounded-2xl overflow-hidden bg-black border border-black/[0.08] relative shadow-lg">
                                <video
                                    x-ref="prodVideo"
                                    src="{{ asset($product->video_url) }}"
                                    poster="{{ asset($product->featured_image) }}"
                                    class="w-full h-full object-cover object-center"
                                    controls
                                    autoplay
                                    muted
                                    loop
                                    playsinline
                                ></video>

                                <!-- Top badge -->
                                <div class="absolute top-4 left-4 flex items-center gap-2 pointer-events-none">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black/70 backdrop-blur-md text-white border border-white/20">
                                        Live Video Inspection
                                    </span>
                                </div>

                                <!-- Sound Button Overlay -->
                                <div class="absolute top-4 right-4">
                                    <button
                                        type="button"
                                        @click="toggleSound()"
                                        class="px-3 py-1.5 rounded-full bg-black/70 hover:bg-black text-white backdrop-blur-md border border-white/20 text-xs transition duration-150 flex items-center gap-1.5"
                                        :title="isVideoMuted ? 'Unmute Audio' : 'Mute Audio'"
                                    >
                                        <svg x-show="isVideoMuted" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                                        </svg>
                                        <svg x-show="!isVideoMuted" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                        </svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider" x-text="isVideoMuted ? 'Unmute' : 'Sound On'"></span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Photo Container -->
                        <div x-show="activeMedia === 'photo' || !'{{ $product->video_url }}'" class="aspect-[4/5] rounded-2xl overflow-hidden bg-[#f3f3f3] border border-black/[0.06] relative">
                            <img :src="selectedImage" alt="{{ $product->name }}" class="w-full h-full object-cover object-center transition duration-500">
                            <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-white/95 text-brand-charcoal border border-black/5 shadow-sm">
                                {{ $product->category->name ?? 'Boutique' }}
                            </span>
                        </div>

                        <!-- Thumbnails -->
                        @if ($product->images && count($product->images) > 1)
                            <div class="flex items-center gap-3 overflow-x-auto pb-2">
                                @foreach ($product->images as $img)
                                    <button type="button" @click="selectedImage = '{{ asset($img) }}'; activeMedia = 'photo'" class="w-20 h-20 rounded-xl overflow-hidden border-2 transition duration-150 shrink-0" :class="selectedImage === '{{ asset($img) }}' && activeMedia === 'photo' ? 'border-black' : 'border-transparent opacity-70 hover:opacity-100'">
                                        <img src="{{ asset($img) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Right: Product Information & Purchase Form -->
                    <div class="lg:col-span-6 flex flex-col justify-between">
                        <div class="space-y-6">
                            <!-- Category & SKU Line -->
                            <div class="flex items-center justify-between text-xs text-brand-charcoal/50 font-semibold tracking-wider uppercase">
                                <span>{{ $product->category->name ?? 'Collection' }}</span>
                                <span>SKU: <span x-text="selectedSku" class="text-brand-charcoal"></span></span>
                            </div>

                            <!-- Title -->
                            <h1 class="font-display font-extrabold text-3xl sm:text-5xl uppercase tracking-tight text-brand-charcoal leading-tight">
                                {{ $product->name }}
                            </h1>

                            <!-- Price Display -->
                            <div class="flex items-baseline gap-3">
                                <span class="font-display font-extrabold text-3xl text-brand-charcoal">
                                    ₦<span x-text="Number(selectedPrice).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })"></span>
                                </span>
                                <span class="text-xs text-brand-charcoal/50 font-medium">VAT inclusive &bull; In-Store &amp; Online</span>
                            </div>

                            <!-- Description -->
                            <div class="text-xs sm:text-sm text-brand-charcoal/70 leading-relaxed pt-2 border-t border-black/[0.06]">
                                {{ $product->description }}
                            </div>

                            <!-- Tags -->
                            @if ($product->brand_tags)
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    @foreach ($product->brand_tags as $tag)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#f4f4f4] text-brand-charcoal/70 border border-black/5">
                                            #{{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Variant Selector -->
                            <div class="pt-4 border-t border-black/[0.06] space-y-3">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold uppercase tracking-wider text-brand-charcoal">Select Variant:</span>
                                    <span class="font-semibold" :class="selectedStock > 0 ? 'text-emerald-600' : 'text-red-500'">
                                        <span x-show="selectedStock > 0">● In Stock (<span x-text="selectedStock"></span> available)</span>
                                        <span x-show="selectedStock <= 0">✕ Sold Out</span>
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                    @foreach ($product->activeVariants as $variant)
                                        <button type="button" @click="selectVariant({{ $variant->toJson() }})" class="p-3 rounded-xl border text-left transition duration-150 relative" :class="selectedVariantId === {{ $variant->id }} ? 'border-black bg-black text-white' : 'border-black/10 bg-[#fafafa] hover:border-black/30 text-brand-charcoal'">
                                            <div class="text-xs font-bold">{{ $variant->size }}</div>
                                            <div class="text-[10px] opacity-70">{{ $variant->color }}</div>
                                            @if ($variant->stock_quantity <= 0)
                                                <span class="absolute top-2 right-2 text-[9px] font-bold uppercase text-red-500">Out</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Add to Bag Form -->
                            <form method="POST" action="{{ route('cart.store') }}" class="pt-4 space-y-3">
                                @csrf
                                <input type="hidden" name="variant_id" :value="selectedVariantId">

                                <div class="flex items-center gap-3">
                                    <!-- Quantity Input -->
                                    <div class="flex items-center rounded-full border border-black/15 bg-[#f4f4f4] px-2 py-1">
                                        <button type="button" @click="if (quantity > 1) quantity--" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm text-brand-charcoal hover:bg-white transition cursor-pointer">
                                            -
                                        </button>
                                        <input type="number" name="quantity" x-model="quantity" min="1" :max="selectedStock" class="w-12 text-center bg-transparent border-0 font-bold text-xs focus:ring-0 text-brand-charcoal">
                                        <button type="button" @click="if (quantity < selectedStock) quantity++" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm text-brand-charcoal hover:bg-white transition cursor-pointer">
                                            +
                                        </button>
                                    </div>

                                    <!-- Add to Bag Button -->
                                    <button type="submit" :disabled="selectedStock <= 0" class="flex-1 py-3.5 px-6 rounded-full font-bold text-xs uppercase tracking-wider transition duration-150 flex items-center justify-center gap-2 border border-black/15 hover:border-black text-brand-charcoal hover:bg-black hover:text-white" :class="selectedStock > 0 ? 'cursor-pointer' : 'opacity-40 cursor-not-allowed'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <span x-show="selectedStock > 0">Add to Shopping Bag</span>
                                        <span x-show="selectedStock <= 0">Out of Stock</span>
                                    </button>
                                </div>

                                <!-- Instant Buy Now Button with Icon -->
                                <button type="submit" name="buy_now" value="1" :disabled="selectedStock <= 0" class="w-full py-3.5 px-6 rounded-full font-bold text-xs uppercase tracking-wider transition duration-150 flex items-center justify-center gap-2 shadow-lg" :class="selectedStock > 0 ? 'bg-brand-pink hover:bg-brand-pink-600 text-white shadow-brand-pink/25 cursor-pointer' : 'bg-black/20 text-white/50 cursor-not-allowed'">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span x-show="selectedStock > 0">Buy Now &bull; Instant Checkout</span>
                                    <span x-show="selectedStock <= 0">Out of Stock</span>
                                </button>

                                <!-- WhatsApp Order / Inquire Direct -->
                                <a
                                    :href="'https://wa.me/{{ $tenantWhatsapp }}?text=' + encodeURIComponent('Hello {{ addslashes($tenantBrand) }}, I would like to order {{ addslashes($product->name) }} (₦{{ number_format($product->base_price, 0) }}).')"
                                    target="_blank"
                                    class="w-full py-2.5 px-4 rounded-full border border-emerald-500/20 bg-emerald-50/70 hover:bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 transition"
                                >
                                    <svg class="w-3.5 h-3.5 fill-current text-emerald-600 shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.301-.15-1.78-.879-2.056-.98-.276-.1-.476-.15-.677.15-.2.301-.777.98-.953 1.18-.175.201-.351.226-.652.075-.301-.15-1.272-.469-2.423-1.496-.896-.799-1.501-1.786-1.677-2.087-.175-.301-.019-.464.132-.614.136-.135.301-.351.451-.527.15-.175.201-.301.301-.502.1-.2.05-.376-.025-.526-.075-.15-.677-1.631-.928-2.238-.244-.59-.492-.51-.677-.52l-.577-.01c-.2 0-.526.075-.802.376s-1.053 1.028-1.053 2.507c0 1.48 1.078 2.908 1.229 3.109.15.2 2.121 3.238 5.138 4.542.718.31 1.278.496 1.715.635.722.23 1.378.197 1.898.12.579-.087 1.78-.727 2.03-1.43.251-.703.251-1.304.176-1.43-.076-.126-.276-.201-.577-.351zM12.04 2C6.527 2 2.05 6.477 2.05 11.99c0 1.82.49 3.528 1.344 5.003L2 22l5.143-1.348A9.94 9.94 0 0012.04 22c5.513 0 9.99-4.477 9.99-9.99A9.99 9.99 0 0012.04 2z"/></svg>
                                    <span>WhatsApp Support: {{ $tenantPhone }}</span>
                                </a>
                            </form>

                            <!-- Guarantee Badges -->
                            <div class="grid grid-cols-2 gap-3 pt-6 border-t border-black/[0.06] text-xs text-brand-charcoal/70">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-brand-charcoal shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2" />
                                    </svg>
                                    <span>Jos &amp; Nationwide Delivery</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-brand-charcoal shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <span>Paystack Verified Payment</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products Section -->
            @if ($relatedProducts->count() > 0)
                <div class="pt-8 border-t border-black/[0.08]">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="font-display font-extrabold text-2xl uppercase tracking-tight text-brand-charcoal">
                            More from this Collection
                        </h2>
                        <a href="{{ route('products.index', ['category' => $product->category->slug ?? '']) }}" class="text-xs font-bold uppercase tracking-wider text-brand-charcoal hover:text-brand-pink underline underline-offset-4">
                            View Category &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        @foreach ($relatedProducts as $related)
                            <a href="{{ route('products.show', $related->slug) }}" class="group bg-white rounded-2xl overflow-hidden border border-black/[0.06] hover:border-black/20 hover:shadow-lg transition p-4 block">
                                <div class="aspect-[4/5] rounded-xl overflow-hidden bg-[#f4f4f4] mb-3">
                                    <img src="{{ asset($related->featured_image) }}" alt="{{ $related->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                </div>
                                <h3 class="font-display font-bold text-sm uppercase text-brand-charcoal group-hover:underline">
                                    {{ $related->name }}
                                </h3>
                                <div class="font-bold text-xs text-brand-charcoal mt-1">
                                    ₦{{ number_format($related->base_price, 0) }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
