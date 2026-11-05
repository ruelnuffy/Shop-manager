<x-super-admin-layout>
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <span class="text-xs font-mono uppercase tracking-wider text-rose-400 font-bold">Platform Overview</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1">Super Admin Cockpit</h1>
            <p class="text-xs text-slate-400 mt-1">Global oversight across all provisioned retail stores, sales volume, and operations.</p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('super-admin.tenants.create') }}"
                class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider transition shadow-md shadow-indigo-600/30 flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Provision New Store</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Stores -->
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Tenant Stores</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-white font-mono">{{ $tenantsCount }}</div>
            <div class="text-[11px] text-emerald-400 font-semibold flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span>{{ $activeTenantsCount }} Active</span>
                @if ($suspendedTenantsCount > 0)
                    <span class="text-rose-400 font-normal">&bull; {{ $suspendedTenantsCount }} Suspended</span>
                @endif
            </div>
        </div>

        <!-- Metric 2: Platform GMV -->
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Platform Gross Sales</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-white font-mono">₦{{ number_format($totalGmv, 2) }}</div>
            <div class="text-[11px] text-slate-400 font-medium">
                {{ number_format($totalOrdersCount) }} Total Orders Processed
            </div>
        </div>

        <!-- Metric 3: Products & Outlets -->
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Catalog Inventory</span>
                <div class="w-8 h-8 rounded-lg bg-violet-500/10 text-violet-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-white font-mono">{{ number_format($totalProductsCount) }}</div>
            <div class="text-[11px] text-indigo-400 font-medium">
                Multi-branch synced catalog
            </div>
        </div>

        <!-- Metric 4: Payroll & Staff -->
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Staff &amp; Payroll</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-white font-mono">₦{{ number_format($totalPayrollDisbursed, 2) }}</div>
            <div class="text-[11px] text-slate-400 font-medium">
                {{ number_format($totalStaffCount) }} Active Store Staff
            </div>
        </div>
    </div>

    <!-- Main Content Dual Grid: Tenant Stores & Live Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Tenant Stores Directory Preview (7 cols) -->
        <div class="lg:col-span-7 p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-base text-white">Active Tenant Stores</h2>
                    <span class="text-[11px] text-slate-400 font-mono">({{ $recentTenants->count() }} stores)</span>
                </div>
                <a href="{{ route('super-admin.tenants.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold transition">
                    View All Stores &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($recentTenants as $tenant)
                    <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80 hover:border-slate-700 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-300 flex items-center justify-center font-bold text-sm shrink-0">
                                {{ strtoupper(substr($tenant->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-white text-sm truncate">{{ $tenant->name }}</h3>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $tenant->status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                        {{ $tenant->status }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                    <span class="font-mono text-slate-500">{{ $tenant->slug }}</span>
                                    <span>&bull;</span>
                                    <span class="capitalize text-indigo-400 font-semibold">{{ $tenant->plan }} Plan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('super-admin.tenants.enter', $tenant) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-xs flex items-center gap-1 cursor-pointer"
                                    title="Impersonate and enter store console"
                                >
                                    <span>Enter Console</span>
                                    <span>&nearr;</span>
                                </button>
                            </form>
                            <a
                                href="{{ route('super-admin.tenants.show', $tenant) }}"
                                class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium transition"
                            >
                                Settings
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-slate-500">
                        No stores provisioned yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Cross-Platform Live Activity Feed (5 cols) -->
        <div class="lg:col-span-5 p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <h2 class="font-bold text-base text-white">Live Platform Orders</h2>
                </div>
                <span class="text-[11px] font-mono text-slate-400">All Tenants</span>
            </div>

            <div class="space-y-3">
                @forelse ($recentOrders as $order)
                    <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800/80 flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-white">{{ $order->order_number }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $order->payment_status === 'paid' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                    {{ $order->payment_status }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-400 truncate">
                                {{ $order->tenant ? $order->tenant->name : 'Platform' }} &bull; {{ $order->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="font-bold font-mono text-white">₦{{ number_format($order->total_amount, 2) }}</div>
                            <span class="text-[10px] text-slate-500 uppercase">{{ $order->payment_method ?? 'card' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-slate-500">
                        No orders recorded on platform yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-super-admin-layout>
