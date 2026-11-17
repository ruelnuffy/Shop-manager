@php
    $currentTenant = app(\App\Services\CurrentTenant::class)->get();
    $brandName = $currentTenant ? $currentTenant->setting('branding.name', $currentTenant->name) : config('app.platform_name', 'ShopManager');
    $storeUrl = $currentTenant ? route('store.share', $currentTenant->slug) : url('/');
@endphp

<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-10 space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $brandName }}</p>
                <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 mt-1">
                    Welcome, {{ Auth::user()->name }}
                </h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ $storeUrl }}" class="px-4 py-2 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                    Browse Store &rarr;
                </a>
                <a href="{{ route('cart.index') }}" class="px-4 py-2 rounded-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    Cart ({{ $cartCount }})
                </a>
                <a href="{{ route('profile.edit') }}" class="px-4 py-2 rounded-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    Profile
                </a>
            </div>
        </div>

        <div class="rounded-3xl bg-white border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-display font-bold text-base text-slate-900">My Orders</h2>
                <span class="text-xs text-slate-400">{{ $orders->count() }} total</span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($orders as $order)
                    <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div>
                            <div class="font-bold text-slate-900">{{ $order->order_number }}</div>
                            <div class="text-slate-400 mt-0.5">{{ $order->created_at?->format('M d, Y g:i A') }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                {{ $order->status }}
                            </span>
                            <span class="font-bold text-slate-900">₦{{ number_format($order->total_amount, 0) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-xs text-slate-400">
                        You haven't placed any orders yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
