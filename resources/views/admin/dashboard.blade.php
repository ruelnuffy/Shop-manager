<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <h1 class="font-display font-extrabold text-xl uppercase tracking-tight text-slate-900">
                Atelier Overview
            </h1>
            <span class="text-xs text-slate-400 font-normal hidden sm:inline">&bull; Live Operations</span>
        </div>
    </x-slot>

    @php
        $currentTenant = app(\App\Services\CurrentTenant::class)->get();
        $storeName = $currentTenant ? $currentTenant->setting('branding.name', $currentTenant->name) : config('app.platform_name', 'ShopManager');
    @endphp

    <div class="space-y-6 sm:space-y-8">
        <!-- Welcome Hero Banner -->
        <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-slate-900 via-slate-850 to-indigo-950 text-white shadow-xl shadow-slate-950/5 relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-semibold text-indigo-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>{{ $storeName }} &bull; Operational Control Center</span>
                    </div>
                    <h2 class="font-display font-extrabold text-2xl sm:text-3xl tracking-tight text-white">
                        Welcome back, {{ Auth::user()->name }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300 font-light leading-relaxed">
                        Here is today's real-time summary across your online storefront, physical in-store checkout, live inventory valuation, and stock health.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('admin.sales.create') }}" class="px-5 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        <span>New Sale (POS)</span>
                    </a>
                    <a href="{{ route('admin.products.create') }}" class="px-4 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/15 text-white font-semibold text-xs tracking-wider transition">
                        + Add Product
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Launch Action Bar -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <a href="{{ route('admin.sales.create') }}" class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-emerald-500/50 hover:shadow-md transition group">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                </div>
                <div class="font-bold text-xs sm:text-sm text-slate-900">Walk-In POS Sale</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Fast retail barcode &amp; cash checkout</div>
            </a>

            <a href="{{ route('admin.products.create') }}" class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-indigo-500/50 hover:shadow-md transition group">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </div>
                <div class="font-bold text-xs sm:text-sm text-slate-900">New Product</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Publish items with sizes &amp; colors</div>
            </a>

            <a href="{{ route('admin.stock.index') }}" class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-amber-500/50 hover:shadow-md transition group">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <div class="font-bold text-xs sm:text-sm text-slate-900">Adjust Inventory</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Restock shipments &amp; count audits</div>
            </a>

            <a href="{{ route('admin.ocr.create') }}" class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-purple-500/50 hover:shadow-md transition group">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <div class="font-bold text-xs sm:text-sm text-slate-900">Scan Paper Ledger</div>
                <div class="text-[11px] text-slate-400 mt-0.5">AI OCR receipt &amp; daybook import</div>
            </a>
        </div>

        <!-- Revenue & Financial KPIs -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
            <!-- Today's Revenue -->
            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Today's Revenue</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">₦</span>
                </div>
                <div class="font-display font-extrabold text-xl sm:text-2xl lg:text-3xl text-slate-900 tracking-tight truncate">
                    ₦{{ number_format($todayRevenue, 0) }}
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-500 font-medium truncate">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Online &amp; In-Store Sales</span>
                </div>
            </div>

            <!-- This Week's Revenue -->
            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">This Week</span>
                    <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </span>
                </div>
                <div class="font-display font-extrabold text-xl sm:text-2xl lg:text-3xl text-slate-900 tracking-tight truncate">
                    ₦{{ number_format($weekRevenue, 0) }}
                </div>
                <div class="mt-2 text-[11px] text-indigo-600 font-semibold truncate">
                    Current Rolling Week
                </div>
            </div>

            <!-- This Month's Revenue -->
            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">This Month</span>
                    <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </span>
                </div>
                <div class="font-display font-extrabold text-xl sm:text-2xl lg:text-3xl text-slate-900 tracking-tight truncate">
                    ₦{{ number_format($monthRevenue, 0) }}
                </div>
                <div class="mt-2 text-[11px] text-slate-500 font-medium truncate">
                    {{ now()->format('F Y') }} Total
                </div>
            </div>

            <!-- Inventory Valuation -->
            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Inventory Valuation</span>
                    <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </span>
                </div>
                <div class="font-display font-extrabold text-xl sm:text-2xl lg:text-3xl text-slate-900 tracking-tight truncate">
                    ₦{{ number_format($totalInventoryValuation, 0) }}
                </div>
                <div class="mt-2 text-[11px] text-slate-500 font-medium truncate">
                    Live Stock Asset Value
                </div>
            </div>
        </div>

        <!-- Orders Pipeline Status Bar -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Orders Pipeline:</span>
                <span class="text-xs font-bold text-slate-900">{{ $totalOrdersCount }} Lifetime</span>
            </div>

            <div class="flex items-center gap-3 text-xs font-medium">
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200/60 hover:bg-amber-100 transition">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>{{ $pendingOrdersCount }} Pending</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-800 border border-blue-200/60 hover:bg-blue-100 transition">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span>{{ $processingOrdersCount }} In Fulfillment</span>
                </a>
            </div>
        </div>

        <!-- Low Stock Alerts Section -->
        @if ($lowStockVariants->count() > 0)
            <div class="bg-white rounded-3xl p-5 sm:p-7 border border-rose-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse shrink-0"></span>
                        <h2 class="font-display font-bold text-sm sm:text-base uppercase tracking-tight text-rose-950 truncate">
                            Low Stock Alerts ({{ $lowStockVariants->count() }} Variants Needing Restock)
                        </h2>
                    </div>
                    <a href="{{ route('admin.stock.index') }}" class="text-xs font-bold text-rose-700 hover:text-rose-900 hover:underline shrink-0 flex items-center gap-1">
                        <span>Restock All</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach ($lowStockVariants as $variant)
                        <div class="p-4 rounded-2xl bg-rose-50/40 border border-rose-100 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-slate-900 truncate">{{ $variant->product->name }}</div>
                                <div class="text-[11px] text-slate-500 truncate mt-0.5">
                                    {{ $variant->size }} &bull; {{ $variant->color }} &bull; <span class="font-mono text-slate-400">{{ $variant->sku }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold uppercase bg-rose-100 text-rose-800 shrink-0">
                                {{ $variant->stock_quantity }} left
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Recent Orders & Stock Activity Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
            <!-- Recent Online Orders -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-5 sm:p-7 border border-slate-200/80 shadow-xs space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="font-display font-bold text-base sm:text-lg text-slate-900 tracking-tight">
                            Recent Online Orders
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Latest purchases from customer storefront</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline shrink-0">
                        View All ({{ $totalOrdersCount }}) &rarr;
                    </a>
                </div>

                @if ($recentOrders->count() > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach ($recentOrders as $order)
                            <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0 pr-1">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 truncate block">
                                        #{{ $order->order_number }}
                                    </a>
                                    <div class="text-slate-400 text-[11px] truncate mt-0.5">
                                        {{ $order->shipping_name }} &bull; {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }} &bull; {{ $order->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5 shrink-0 text-right">
                                    <span class="font-bold text-slate-900 whitespace-nowrap">₦{{ number_format($order->total_amount, 0) }}</span>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider whitespace-nowrap {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-amber-50 text-amber-800 border border-amber-200/60' }}">
                                        {{ $order->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-xs text-slate-400">
                        No orders recorded yet. As customers shop, they will stream here.
                    </div>
                @endif
            </div>

            <!-- Recent Stock Audit Movements -->
            <div class="lg:col-span-5 bg-white rounded-3xl p-5 sm:p-7 border border-slate-200/80 shadow-xs space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="font-display font-bold text-base sm:text-lg text-slate-900 tracking-tight">
                            Stock Movement Audit
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Live inventory adjustment log</p>
                    </div>
                    <a href="{{ route('admin.stock.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline shrink-0">
                        Audit Trail &rarr;
                    </a>
                </div>

                @if ($recentStockMovements->count() > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach ($recentStockMovements as $move)
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0 pr-1">
                                    <div class="font-bold text-slate-900 truncate">{{ $move->variant?->product?->name ?? 'Inventory Item' }}</div>
                                    <div class="text-[11px] text-slate-400 truncate mt-0.5">
                                        {{ $move->reason }} &bull; {{ $move->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono shrink-0 whitespace-nowrap {{ $move->type === 'in' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $move->type === 'in' ? '+' : '-' }}{{ $move->quantity }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-xs text-slate-400">
                        No stock adjustments logged yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
