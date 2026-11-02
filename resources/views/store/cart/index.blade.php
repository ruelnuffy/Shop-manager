<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-charcoal/50 block mb-1">
                    Boutique Bag
                </span>
                <h1 class="font-display font-extrabold text-3xl sm:text-4xl uppercase tracking-tight text-brand-charcoal">
                    YOUR SHOPPING BAG ({{ app(\App\Services\CartService::class)->getItemCount() }})
                </h1>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-bold uppercase tracking-wider text-brand-charcoal hover:text-brand-pink underline underline-offset-4">
                &larr; Continue Shopping
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900">✕</button>
                </div>
            @endif

            @if (!empty($items))
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                    <!-- Left: Bag Line Items -->
                    <div class="lg:col-span-8 space-y-4">
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-black/[0.06] shadow-sm divide-y divide-black/[0.06]">
                            @foreach ($items as $variantId => $item)
                                <div class="py-6 first:pt-0 last:pb-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                                    <!-- Product Info & Image -->
                                    <div class="flex items-center gap-4">
                                        @php
                                            $cartItemImg = $item['image'] ?? null;
                                            $cartItemImgSrc = !empty($cartItemImg)
                                                ? (str_starts_with($cartItemImg, 'http://') || str_starts_with($cartItemImg, 'https://')
                                                    ? $cartItemImg
                                                    : asset(ltrim($cartItemImg, '/')))
                                                : null;
                                        @endphp
                                        <div class="w-20 h-24 rounded-xl overflow-hidden bg-[#f3f3f3] shrink-0 border border-black/[0.06] relative flex items-center justify-center">
                                            @if ($cartItemImgSrc)
                                                <img
                                                    src="{{ $cartItemImgSrc }}"
                                                    alt="{{ $item['product_name'] }}"
                                                    class="w-full h-full object-cover"
                                                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                                                >
                                                <div class="hidden w-full h-full flex flex-col items-center justify-center bg-[#f3f3f3] p-2 text-center text-[10px] text-brand-charcoal/50 uppercase font-bold tracking-wider">
                                                    <svg class="w-5 h-5 mb-1 text-brand-charcoal/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                                    ClassiQ
                                                </div>
                                            @else
                                                <div class="w-full h-full flex flex-col items-center justify-center bg-[#f3f3f3] p-2 text-center text-[10px] text-brand-charcoal/50 uppercase font-bold tracking-wider">
                                                    <svg class="w-5 h-5 mb-1 text-brand-charcoal/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                                    ClassiQ
                                                </div>
                                            @endif
                                        </div>
                                        <div class="space-y-1">
                                            <a href="{{ route('products.show', $item['product_slug']) }}" class="font-display font-bold text-base text-brand-charcoal hover:underline">
                                                {{ $item['product_name'] }}
                                            </a>
                                            <p class="text-xs text-brand-charcoal/60">
                                                Size: <strong class="text-brand-charcoal">{{ $item['size'] }}</strong> &bull; Color: <strong class="text-brand-charcoal">{{ $item['color'] }}</strong>
                                            </p>
                                            <p class="text-[11px] text-brand-charcoal/40 font-mono">
                                                SKU: {{ $item['sku'] }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Price & Quantity Form -->
                                    <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-6">
                                        <form method="POST" action="{{ route('cart.update', $variantId) }}" class="flex items-center rounded-full border border-black/15 bg-[#f4f4f4] px-2 py-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs text-brand-charcoal hover:bg-white transition">
                                                -
                                            </button>
                                            <span class="w-8 text-center font-bold text-xs text-brand-charcoal">
                                                {{ $item['quantity'] }}
                                            </span>
                                            <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" {{ $item['quantity'] >= $item['stock_limit'] ? 'disabled' : '' }} class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs text-brand-charcoal hover:bg-white transition {{ $item['quantity'] >= $item['stock_limit'] ? 'opacity-30 cursor-not-allowed' : '' }}">
                                                +
                                            </button>
                                        </form>

                                        <div class="text-right">
                                            <div class="font-bold text-sm text-brand-charcoal">
                                                ₦{{ number_format($item['price'] * $item['quantity'], 0) }}
                                            </div>
                                            <div class="text-[10px] text-brand-charcoal/50">
                                                ₦{{ number_format($item['price'], 0) }} each
                                            </div>
                                        </div>

                                        <form method="POST" action="{{ route('cart.destroy', $variantId) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-brand-charcoal/40 hover:text-red-600 transition p-1.5" title="Remove item">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-between items-center px-4">
                            <form method="POST" action="{{ route('cart.clear') }}">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-brand-charcoal/50 hover:text-red-600 transition">
                                    Clear Bag
                                </button>
                            </form>
                            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-brand-charcoal hover:underline">
                                &larr; Continue shopping catalog
                            </a>
                        </div>
                    </div>

                    <!-- Right: Summary & Proceed to Checkout -->
                    <div class="lg:col-span-4">
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-black/[0.06] shadow-sm space-y-6 sticky top-24">
                            <h2 class="font-display font-extrabold text-xl uppercase tracking-tight text-brand-charcoal pb-4 border-b border-black/[0.06]">
                                Order Summary
                            </h2>

                            <div class="space-y-3 text-xs text-brand-charcoal/70">
                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span class="font-bold text-brand-charcoal">₦{{ number_format($subtotal, 0) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Estimated Nationwide Courier</span>
                                    <span class="font-bold text-brand-charcoal">₦{{ number_format($shippingFee, 0) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Taxes &amp; VAT</span>
                                    <span class="font-medium text-emerald-600">Included</span>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-black/[0.06] flex justify-between items-baseline">
                                <span class="font-display font-bold text-base uppercase text-brand-charcoal">Grand Total</span>
                                <span class="font-display font-extrabold text-2xl text-brand-charcoal">₦{{ number_format($total, 0) }}</span>
                            </div>

                            <a href="{{ route('checkout.index') }}" class="w-full py-4 px-6 rounded-full bg-black hover:bg-brand-pink text-white font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition duration-150 shadow-xl shadow-black/10">
                                <span>Proceed to Checkout</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>

                            <div class="pt-4 text-center text-[11px] text-brand-charcoal/50 space-y-1">
                                <p class="flex items-center justify-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-brand-charcoal shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Paystack 256-Bit SSL Checkout
                                </p>
                                <p>Bank cards, USSD, Bank Transfer accepted</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Bag State -->
                <div class="bg-white rounded-3xl p-16 text-center border border-black/[0.06] max-w-lg mx-auto">
                    <div class="w-16 h-16 rounded-full bg-[#f4f4f4] flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-brand-charcoal/50" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h3 class="font-display font-extrabold text-2xl uppercase tracking-tight text-brand-charcoal">
                        Your bag is empty
                    </h3>
                    <p class="text-xs text-brand-charcoal/60 mt-2 max-w-xs mx-auto leading-relaxed">
                        Discover the new boutique collection of silk evening gowns, quilted leather bags, and designer footwear.
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-8 px-8 py-3.5 rounded-full bg-black hover:bg-brand-pink text-white font-bold text-xs uppercase tracking-wider transition shadow-lg">
                        Explore Collection
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
