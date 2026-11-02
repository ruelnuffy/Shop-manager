<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-brand-charcoal">
                ORDER CONFIRMED &bull; {{ $order->order_number }}
            </h1>
            <button onclick="window.print()" class="px-4 py-2 rounded-full border border-black/15 bg-white hover:bg-black/5 text-xs font-bold uppercase tracking-wider text-brand-charcoal transition">
                Print Invoice Receipt
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Success Banner -->
            <div class="bg-white rounded-3xl p-8 sm:p-12 border border-black/[0.06] shadow-sm text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center mx-auto shadow-sm">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Payment Verified via Paystack
                </span>
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl uppercase tracking-tight text-brand-charcoal">
                    Thank you for your order
                </h2>
                @php
                    $tenant = app(\App\Services\CurrentTenant::class)->get();
                    $storeName = $tenant ? $tenant->setting('branding.name', $tenant->name) : config('app.platform_name', 'ShopManager');
                @endphp
                <p class="text-xs sm:text-sm text-brand-charcoal/60 max-w-md mx-auto leading-relaxed">
                    We have received your order <strong class="text-brand-charcoal">{{ $order->order_number }}</strong>. Your items are now being prepared by {{ $storeName }}.
                </p>
            </div>

            <!-- Receipt Breakdown -->
            <div class="bg-white rounded-3xl p-6 sm:p-10 border border-black/[0.06] shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-black/[0.06] text-xs">
                    <div>
                        <div class="font-bold text-sm text-brand-charcoal">Order #{{ $order->order_number }}</div>
                        <div class="text-brand-charcoal/50">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ $order->payment_status }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-black text-white">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="divide-y divide-black/[0.06]">
                    @foreach ($order->items as $item)
                        <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                            <div class="flex items-center gap-3">
                                @php
                                    $confImg = $item->variant?->product?->featured_image_url ?? ($item->variant?->product?->featured_image ? asset(ltrim($item->variant->product->featured_image, '/')) : null);
                                @endphp
                                <div class="w-12 h-14 rounded-lg bg-[#f4f4f4] overflow-hidden shrink-0 border border-black/[0.04] relative flex items-center justify-center">
                                    @if ($confImg)
                                        <img src="{{ $confImg }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                        <div class="hidden w-full h-full flex items-center justify-center bg-[#f4f4f4] text-[8px] font-bold text-brand-charcoal/40">
                                            BAG
                                        </div>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-[#f4f4f4] text-[8px] font-bold text-brand-charcoal/40">
                                            BAG
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-brand-charcoal">{{ $item->product_name }}</div>
                                    <div class="text-brand-charcoal/50 text-[11px]">{{ $item->variant_description }} &bull; Qty: {{ $item->quantity }}</div>
                                </div>
                            </div>
                            <div class="font-bold text-brand-charcoal">
                                ₦{{ number_format($item->subtotal, 0) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Financial Totals -->
                <div class="pt-6 border-t border-black/[0.06] space-y-2 text-xs text-brand-charcoal/70">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-bold text-brand-charcoal">₦{{ number_format($order->subtotal, 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Courier Delivery</span>
                        <span class="font-bold text-brand-charcoal">₦{{ number_format($order->shipping_fee, 0) }}</span>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-black/[0.06] text-sm">
                        <span class="font-display font-extrabold uppercase text-brand-charcoal">Total Paid</span>
                        <span class="font-display font-extrabold text-2xl text-brand-charcoal">₦{{ number_format($order->total_amount, 0) }}</span>
                    </div>
                </div>

                <!-- Shipping Destination -->
                <div class="pt-6 border-t border-black/[0.06] grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-brand-charcoal/70">
                    <div>
                        <h4 class="font-bold uppercase tracking-wider text-brand-charcoal mb-2">Delivery Destination</h4>
                        <p class="leading-relaxed">
                            <strong class="text-brand-charcoal">{{ $order->shipping_name }}</strong><br>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}<br>
                            Phone: {{ $order->shipping_phone }}
                        </p>
                    </div>
                    <div>
                        <h4 class="font-bold uppercase tracking-wider text-brand-charcoal mb-2">Payment Details</h4>
                        <p class="leading-relaxed">
                            Method: <strong class="text-brand-charcoal">Paystack Checkout</strong><br>
                            Ref: <code class="font-mono text-[11px]">{{ $order->latestPayment?->paystack_reference ?? 'N/A' }}</code><br>
                            Receipt Email: {{ $order->shipping_email }}
                        </p>
                    </div>
                </div>

                <div class="pt-6 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-6 py-3 rounded-full border border-black/15 hover:border-black bg-white hover:bg-black hover:text-white text-brand-charcoal font-bold text-xs uppercase tracking-wider transition shadow-sm">
                        <svg class="w-3.5 h-3.5 text-brand-pink shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Storefront Homepage</span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-6 py-3 rounded-full bg-black hover:bg-brand-pink text-white font-bold text-xs uppercase tracking-wider transition shadow-md">
                        <span>Member Dashboard &rarr;</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 rounded-full bg-brand-pink hover:bg-brand-pink-600 text-white font-bold text-xs uppercase tracking-wider transition shadow-md shadow-brand-pink/25">
                        Continue Shopping &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
