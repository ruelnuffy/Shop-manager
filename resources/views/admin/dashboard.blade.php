<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-display font-extrabold text-2xl text-slate-900 tracking-tight">
                    Atelier Overview
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Welcome back, {{ Auth::user()->name }}. Here is how your store is doing today.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.sales.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm hover:shadow">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>Record Sale</span>
                </a>
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider transition shadow-xs">
                    <span>+ Add Product</span>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $currentTenant = app(\App\Services\CurrentTenant::class)->get();
        $storeName = $currentTenant ? $currentTenant->setting('branding.name', $currentTenant->name) : config('app.platform_name', 'ShopManager');
    @endphp

    <div class="space-y-6 sm:space-y-8">
        <!-- 4 Key Numbers (Simple, Clear, Big) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            <!-- Today's Revenue -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500">Today's Revenue</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">₦</span>
                </div>
                <div class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    ₦{{ number_format($todayRevenue, 0) }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Online &amp; in-store sales
                </div>
            </div>

            <!-- Orders to Fulfill -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500">Orders to Pack</span>
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    </span>
                </div>
                <div class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    {{ $pendingOrdersCount }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    {{ $pendingOrdersCount === 1 ? '1 order pending fulfillment' : ($pendingOrdersCount > 0 ? $pendingOrdersCount . ' orders pending' : 'All orders fulfilled') }}
                </div>
            </div>

            <!-- This Month -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500">This Month</span>
                    <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </span>
                </div>
                <div class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    ₦{{ number_format($monthRevenue, 0) }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    {{ now()->format('F Y') }} sales
                </div>
            </div>

            <!-- Inventory Valuation -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500">Inventory Valuation</span>
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </span>
                </div>
                <div class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    ₦{{ number_format($totalInventoryValuation, 0) }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Stock value in store
                </div>
            </div>
        </div>

        <!-- 4 Quick Actions (Friendly & Direct) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <a href="{{ route('admin.sales.create') }}" class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-emerald-500 hover:shadow-sm transition group">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:bg-emerald-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                </div>
                <div class="font-bold text-sm text-slate-900">Make Sale (POS)</div>
                <div class="text-xs text-slate-500 mt-0.5">Sell to a walk-in customer</div>
            </a>

            <a href="{{ route('admin.products.create') }}" class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-indigo-500 hover:shadow-sm transition group">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3 group-hover:bg-indigo-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </div>
                <div class="font-bold text-sm text-slate-900">Add Product</div>
                <div class="text-xs text-slate-500 mt-0.5">Add items, sizes &amp; prices</div>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-blue-500 hover:shadow-sm transition group">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                </div>
                <div class="font-bold text-sm text-slate-900">Orders</div>
                <div class="text-xs text-slate-500 mt-0.5">See all customer orders</div>
            </a>

            <a href="{{ route('admin.stock.index') }}" class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-amber-500 hover:shadow-sm transition group">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3 group-hover:bg-amber-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <div class="font-bold text-sm text-slate-900">Inventory Stock</div>
                <div class="text-xs text-slate-500 mt-0.5">Update item quantities</div>
            </a>
        </div>

        <!-- Gentle Low Stock Notice (Only if items are low) -->
        @if ($lowStockVariants->count() > 0)
            <div class="bg-amber-50/60 border border-amber-200/70 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </span>
                    <div>
                        <div class="font-bold text-sm text-slate-900">
                            {{ $lowStockVariants->count() }} {{ Str::plural('item', $lowStockVariants->count()) }} running low on stock
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            @foreach($lowStockVariants->take(3) as $v)
                                {{ $v->product->name }} ({{ $v->size ?? $v->color ?? 'variant' }}: {{ $v->stock_quantity }} left){{ !$loop->last ? ' • ' : '' }}
                            @endforeach
                            @if($lowStockVariants->count() > 3)
                                and {{ $lowStockVariants->count() - 3 }} more...
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.stock.index') }}" class="px-4 py-2 rounded-xl bg-white border border-amber-300 text-amber-900 hover:bg-amber-100 text-xs font-bold transition shrink-0 self-start sm:self-center">
                    Review Stock &rarr;
                </a>
            </div>
        @endif

        <!-- Recent Orders & Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Recent Orders (Main View) -->
            <div class="lg:col-span-8 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="font-bold text-base text-slate-900">
                            Recent Customer Orders
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Latest sales made online and in store</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                        View all &rarr;
                    </a>
                </div>

                @if ($recentOrders->count() > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach ($recentOrders as $order)
                            <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition">
                                            #{{ $order->order_number }}
                                        </a>
                                        <span class="text-slate-400">&bull;</span>
                                        <span class="text-slate-600 font-medium truncate">{{ $order->shipping_name ?? 'Walk-in Customer' }}</span>
                                    </div>
                                    <div class="text-slate-400 text-[11px] mt-0.5">
                                        {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }} &bull; {{ $order->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="font-bold text-sm text-slate-900">₦{{ number_format($order->total_amount, 0) }}</span>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-800' }}">
                                        {{ $order->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-base">🛍️</div>
                        <div class="text-sm font-semibold text-slate-700">No orders recorded yet</div>
                        <p class="text-xs text-slate-400 mt-1">When customers place orders, they will appear here.</p>
                    </div>
                @endif
            </div>

            <!-- Recent Stock Activity (Clean Side Card) -->
            <div class="lg:col-span-4 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="font-bold text-base text-slate-900">
                            Stock Updates
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Recent stock adjustments</p>
                    </div>
                    <a href="{{ route('admin.stock.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                        History &rarr;
                    </a>
                </div>

                @if ($recentStockMovements->count() > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach ($recentStockMovements as $move)
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-slate-900 truncate">{{ $move->variant?->product?->name ?? 'Item' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 truncate">
                                        {{ $move->reason ?? 'Updated' }} &bull; {{ $move->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-xs font-bold font-mono shrink-0 {{ $move->type === 'in' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $move->type === 'in' ? '+' : '-' }}{{ $move->quantity }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-xs text-slate-400">
                        No recent stock adjustments.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
