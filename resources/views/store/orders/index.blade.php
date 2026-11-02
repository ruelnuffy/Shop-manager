<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-charcoal/50 mb-1">
                    <a href="{{ route('home') }}" class="hover:text-brand-pink transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-brand-pink" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Home</span>
                    </a>
                    <span>/</span>
                    <a href="{{ route('dashboard') }}" class="hover:text-brand-pink transition">
                        <span>Dashboard</span>
                    </a>
                    <span>/</span>
                    <span class="text-brand-charcoal">My Orders</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl sm:text-3xl uppercase tracking-tight text-brand-charcoal">
                    Client Orders History
                </h1>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-black/10 hover:border-black text-xs font-bold uppercase tracking-wider text-brand-charcoal hover:bg-black hover:text-white transition shadow-xs">
                    <svg class="w-3.5 h-3.5 text-brand-pink shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Storefront</span>
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-black text-white hover:bg-brand-pink text-xs font-bold uppercase tracking-wider transition shadow-xs">
                    <span>&larr; Dashboard</span>
                </a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-1.5 rounded-full bg-brand-pink text-white hover:bg-brand-pink-600 text-xs font-bold uppercase tracking-wider transition shadow-xs">
                    Shop Drops &rarr;
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($orders->count() > 0)
                <div class="space-y-6">
                    @foreach ($orders as $order)
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-black/[0.06] shadow-sm space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-black/[0.06] text-xs">
                                <div>
                                    <div class="font-bold text-sm text-brand-charcoal">Order #{{ $order->order_number }}</div>
                                    <div class="text-brand-charcoal/50">{{ $order->created_at->format('M d, Y &bull; h:i A') }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                        {{ $order->payment_status }}
                                    </span>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-black text-white">
                                        {{ $order->status }}
                                    </span>
                                </div>
                            </div>

                            <!-- Items in Order -->
                            <div class="divide-y divide-black/[0.04]">
                                @foreach ($order->items as $item)
                                    <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between text-xs">
                                        <div>
                                            <span class="font-bold text-brand-charcoal">{{ $item->product_name }}</span>
                                            <span class="text-brand-charcoal/50 text-[11px]">({{ $item->variant_description }}) &times; {{ $item->quantity }}</span>
                                        </div>
                                        <div class="font-semibold text-brand-charcoal">
                                            ₦{{ number_format($item->subtotal, 0) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="pt-4 border-t border-black/[0.06] flex items-center justify-between text-xs">
                                <span class="text-brand-charcoal/50">Total Paid: <strong class="text-brand-charcoal font-bold text-sm">₦{{ number_format($order->total_amount, 0) }}</strong></span>
                                <a href="{{ route('order.confirmation', $order->order_number) }}" class="font-bold text-brand-charcoal hover:text-brand-pink underline underline-offset-4">
                                    View Receipt &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-8">
                        {{ $orders->links() }}
                    </div>
                </div>
            @else
                <div class="bg-white rounded-3xl p-16 text-center border border-black/[0.06] max-w-md mx-auto">
                    <div class="w-16 h-16 rounded-full bg-[#f4f4f4] flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-brand-charcoal/50" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="font-display font-extrabold text-2xl uppercase tracking-tight text-brand-charcoal">
                        No orders yet
                    </h3>
                    <p class="text-xs text-brand-charcoal/60 mt-2 max-w-xs mx-auto leading-relaxed">
                        You have not placed any orders yet. Explore our luxury boutique collection and order via Paystack.
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-6 px-8 py-3.5 rounded-full bg-black hover:bg-brand-pink text-white font-bold text-xs uppercase tracking-wider transition shadow-lg">
                        Explore Catalog
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
