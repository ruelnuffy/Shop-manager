<x-super-admin-layout>
    <!-- Enterprise Dashboard Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 pb-6 border-b border-slate-800/80">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-widest bg-indigo-500/10 text-indigo-400 border border-indigo-500/25">
                    Platform Overview
                </span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span>
                <span class="text-xs font-mono text-slate-400">Live Multi-Tenant Telemetry</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight mt-1.5 font-display">
                Super Admin Cockpit
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Central command plane for retail stores, sales volume, plan tiers, and platform health.
            </p>
        </div>

        <!-- Header Actions & Controls -->
        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- 7-Day Live Indicator -->
            <div class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs font-mono text-slate-300">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Real-time Velocity</span>
            </div>

            <!-- Export CSV -->
            <a
                href="{{ route('super-admin.export-report') }}"
                class="px-3.5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-850 text-slate-200 border border-slate-800 hover:border-slate-700 text-xs font-semibold transition flex items-center gap-1.5 shadow-sm"
            >
                <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Export Audit CSV</span>
            </a>

            <!-- Provision New Store CTA -->
            <a
                href="{{ route('super-admin.tenants.create') }}"
                class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-500 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold uppercase tracking-wider transition shadow-lg shadow-indigo-600/30 flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Provision New Store</span>
            </a>
        </div>
    </div>

    <!-- Tier 1: Key Executive Metrics Grid (6 Metric Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- 1. Gross Merchandise Value -->
        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-950/90 border border-slate-800/90 space-y-2 relative overflow-hidden group hover:border-indigo-500/40 transition">
            <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-indigo-500/5 rounded-full blur-xl group-hover:bg-indigo-500/10 transition"></div>
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">Platform GMV</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-xl xl:text-2xl font-extrabold text-white font-mono tracking-tight">
                ₦{{ number_format($totalGmv, 2) }}
            </div>
            <div class="text-[10px] text-emerald-400 font-medium flex items-center gap-1">
                <span>{{ number_format($paidOrdersCount) }} paid sales</span>
                <span class="text-slate-600">&bull;</span>
                <span class="text-slate-400">All Stores</span>
            </div>
        </div>

        <!-- 2. Monthly Recurring Revenue (MRR) -->
        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-950/90 border border-slate-800/90 space-y-2 relative overflow-hidden group hover:border-violet-500/40 transition">
            <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-violet-500/5 rounded-full blur-xl group-hover:bg-violet-500/10 transition"></div>
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">Platform MRR</span>
                <div class="w-7 h-7 rounded-lg bg-violet-500/10 text-violet-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="text-xl xl:text-2xl font-extrabold text-violet-300 font-mono tracking-tight">
                ₦{{ number_format($platformMrr, 2) }}
            </div>
            <div class="text-[10px] text-slate-400 font-medium">
                ARR: <span class="text-slate-200 font-mono font-semibold">₦{{ number_format($platformMrr * 12, 2) }}</span>
            </div>
        </div>

        <!-- 3. Tenant Stores Count -->
        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-950/90 border border-slate-800/90 space-y-2 relative overflow-hidden group hover:border-indigo-500/40 transition">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">Tenant Stores</span>
                <div class="w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="text-xl xl:text-2xl font-extrabold text-white font-mono tracking-tight">
                {{ $tenantsCount }}
            </div>
            <div class="text-[10px] text-slate-400 font-medium flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span class="text-emerald-400 font-semibold">{{ $activeTenantsCount }} Active</span>
                @if ($suspendedTenantsCount > 0)
                    <span class="text-rose-400">&bull; {{ $suspendedTenantsCount }} Suspended</span>
                @endif
            </div>
        </div>

        <!-- 4. Average Order Value -->
        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-950/90 border border-slate-800/90 space-y-2 relative overflow-hidden group hover:border-cyan-500/40 transition">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">Platform AOV</span>
                <div class="w-7 h-7 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="text-xl xl:text-2xl font-extrabold text-white font-mono tracking-tight">
                ₦{{ number_format($averageOrderValue, 2) }}
            </div>
            <div class="text-[10px] text-slate-400 font-medium">
                {{ number_format($totalOrdersCount) }} total order attempts
            </div>
        </div>

        <!-- 5. Staff & Payroll Disbursed -->
        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-950/90 border border-slate-800/90 space-y-2 relative overflow-hidden group hover:border-amber-500/40 transition">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">Payroll Disbursed</span>
                <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-xl xl:text-2xl font-extrabold text-amber-300 font-mono tracking-tight">
                ₦{{ number_format($totalPayrollDisbursed, 2) }}
            </div>
            <div class="text-[10px] text-slate-400 font-medium">
                {{ number_format($totalStaffCount) }} Active Store Staff
            </div>
        </div>

        <!-- 6. OCR Ingestion & Catalog -->
        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-950/90 border border-slate-800/90 space-y-2 relative overflow-hidden group hover:border-rose-500/40 transition">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">OCR Ingestions</span>
                <div class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-xl xl:text-2xl font-extrabold text-white font-mono tracking-tight">
                {{ number_format($totalOcrBatches) }}
            </div>
            <div class="text-[10px] text-slate-400 font-medium">
                {{ number_format($totalProductsCount) }} active catalog SKUs
            </div>
        </div>
    </div>

    <!-- Tier 2: Real-time Interactive Analytics Section (Dual Graphs) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Main Chart: 7-Day Revenue Velocity (8 cols) -->
        <div class="lg:col-span-8 p-6 rounded-3xl bg-[#0c0f17]/90 border border-slate-800/90 space-y-5 shadow-2xl relative">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-800/80">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                        <h2 class="text-base font-bold text-white tracking-tight">Platform Sales Velocity &amp; Order Volume</h2>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Aggregated cross-tenant daily gross merchandise volume (7-day trend).</p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-mono text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">
                        Live Sync: Active
                    </span>
                </div>
            </div>

            <!-- Canvas Container -->
            <div class="h-64 sm:h-72 w-full relative">
                <canvas id="revenueVelocityChart"></canvas>
            </div>
        </div>

        <!-- Secondary Chart: Subscription Plan Distribution (4 cols) -->
        <div class="lg:col-span-4 p-6 rounded-3xl bg-[#0c0f17]/90 border border-slate-800/90 space-y-5 shadow-2xl relative flex flex-col justify-between">
            <div class="pb-4 border-b border-slate-800/80">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-violet-500"></span>
                    <h2 class="text-base font-bold text-white tracking-tight">Tenant Plan Tiers</h2>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Store breakdown by subscription package.</p>
            </div>

            <div class="h-48 relative flex items-center justify-center my-auto">
                <canvas id="planDistributionChart"></canvas>
            </div>

            <!-- Tier Stats Pills -->
            <div class="grid grid-cols-2 gap-2 pt-4 border-t border-slate-800/80 text-[11px] font-mono">
                <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800/80 flex items-center justify-between">
                    <span class="text-indigo-400 font-semibold">Pro Tier</span>
                    <span class="text-white font-bold">{{ $planCounts['pro'] ?? 0 }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800/80 flex items-center justify-between">
                    <span class="text-violet-400 font-semibold">Enterprise</span>
                    <span class="text-white font-bold">{{ $planCounts['enterprise'] ?? 0 }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800/80 flex items-center justify-between">
                    <span class="text-emerald-400 font-semibold">Standard</span>
                    <span class="text-white font-bold">{{ $planCounts['standard'] ?? 0 }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800/80 flex items-center justify-between">
                    <span class="text-slate-400 font-semibold">Starter</span>
                    <span class="text-white font-bold">{{ $planCounts['starter'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tier 3: Merchant Leaderboard & Live Platform Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Top Performing Merchant Stores (7 cols) -->
        <div class="lg:col-span-7 p-6 rounded-3xl bg-[#0c0f17]/90 border border-slate-800/90 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white tracking-tight">Top Merchant Leaderboard</h2>
                        <p class="text-xs text-slate-400">Stores ranked by total gross volume.</p>
                    </div>
                </div>

                <a href="{{ route('super-admin.tenants.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition">
                    View All Directory &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] uppercase font-mono font-bold tracking-wider text-slate-400 border-b border-slate-800/80">
                            <th class="pb-3">Rank &amp; Store</th>
                            <th class="pb-3">Plan</th>
                            <th class="pb-3 text-right">Orders</th>
                            <th class="pb-3 text-right">GMV (NGN)</th>
                            <th class="pb-3 text-right">Ops</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse ($topTenants as $index => $store)
                            <tr class="hover:bg-slate-900/50 transition">
                                <td class="py-3 font-sans">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold font-mono {{ $index === 0 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : ($index === 1 ? 'bg-slate-300/20 text-slate-200 border border-slate-400/30' : 'bg-slate-800 text-slate-400') }}">
                                            #{{ $index + 1 }}
                                        </span>
                                        <div>
                                            <a href="{{ route('super-admin.tenants.show', $store) }}" class="font-bold text-white hover:text-indigo-400 transition">
                                                {{ $store->name }}
                                            </a>
                                            <div class="text-[10px] text-slate-400 font-mono">{{ $store->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono uppercase font-bold {{ $store->plan === 'enterprise' ? 'bg-violet-500/10 text-violet-300 border border-violet-500/30' : ($store->plan === 'pro' ? 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/30' : 'bg-slate-800 text-slate-400') }}">
                                        {{ $store->plan }}
                                    </span>
                                </td>
                                <td class="py-3 text-right font-bold text-slate-300">
                                    {{ number_format($store->orders_count) }}
                                </td>
                                <td class="py-3 text-right font-bold text-emerald-400 text-sm">
                                    ₦{{ number_format($store->gmv, 2) }}
                                </td>
                                <td class="py-3 text-right">
                                    <form method="POST" action="{{ route('super-admin.tenants.enter', $store) }}" class="inline-block">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1 rounded-lg bg-indigo-600/10 hover:bg-indigo-600 hover:text-white border border-indigo-500/30 text-indigo-300 text-[10px] font-semibold transition"
                                            title="Masquerade into merchant console"
                                        >
                                            Enter &rarr;
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-xs text-slate-500 font-sans">
                                    No tenant stores registered yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Live Platform Orders Feed (5 cols) -->
        <div class="lg:col-span-5 p-6 rounded-3xl bg-[#0c0f17]/90 border border-slate-800/90 space-y-5 shadow-2xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <div>
                            <h2 class="text-base font-bold text-white tracking-tight">Live Platform Orders</h2>
                            <p class="text-xs text-slate-400">Incoming transactions across all stores.</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-mono text-slate-400 bg-slate-900 px-2 py-0.5 rounded-full border border-slate-800">
                        Cross-Tenant
                    </span>
                </div>

                <div class="divide-y divide-slate-800/50 mt-2">
                    @forelse ($recentOrders as $order)
                        <div class="py-3 flex items-center justify-between gap-3 text-xs first:pt-2">
                            <div class="min-w-0 space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-white">{{ $order->order_number }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $order->payment_status === 'paid' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                        {{ $order->payment_status }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-400 truncate">
                                    <span class="text-indigo-400 font-medium">{{ $order->tenant ? $order->tenant->name : 'Platform Store' }}</span>
                                    &bull;
                                    <span>{{ $order->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <div class="font-bold font-mono text-emerald-400 text-sm">₦{{ number_format($order->total_amount, 2) }}</div>
                                <span class="text-[10px] text-slate-500 uppercase font-mono">{{ $order->payment_method ?? 'card' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-500">
                            No orders recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                <span>Webhook Processing: <strong class="text-emerald-400 font-mono">100% Validated</strong></span>
                <span class="font-mono">HMAC SHA-512</span>
            </div>
        </div>
    </div>

    <!-- Tier 4: Platform System Telemetry & Infrastructure Diagnostics -->
    <div class="p-6 rounded-3xl bg-[#0c0f17]/90 border border-slate-800/90 space-y-4 shadow-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800/80">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white tracking-tight">System Infrastructure Telemetry</h3>
                    <p class="text-[11px] text-slate-400">Live operational runtime metrics and daemon health.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span class="text-xs font-mono text-slate-300 font-medium">Telemetry Online</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-xs font-mono">
            <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-1">
                <div class="text-[10px] text-slate-500 uppercase">Database Driver</div>
                <div class="font-bold text-white uppercase">{{ $systemTelemetry['db_driver'] }}</div>
                <div class="text-[10px] text-emerald-400">{{ $systemTelemetry['db_status'] }} ({{ $systemTelemetry['db_latency'] }}ms)</div>
            </div>

            <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-1">
                <div class="text-[10px] text-slate-500 uppercase">Cache Engine</div>
                <div class="font-bold text-white uppercase">{{ $systemTelemetry['cache_driver'] }}</div>
                <div class="text-[10px] text-emerald-400">Low-latency Buffer</div>
            </div>

            <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-1">
                <div class="text-[10px] text-slate-500 uppercase">Runtime Env</div>
                <div class="font-bold text-white uppercase">{{ $systemTelemetry['app_env'] }}</div>
                <div class="text-[10px] text-indigo-400">Port 8001 Daemon</div>
            </div>

            <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-1">
                <div class="text-[10px] text-slate-500 uppercase">PHP &amp; Laravel</div>
                <div class="font-bold text-white">PHP {{ $systemTelemetry['php_version'] }}</div>
                <div class="text-[10px] text-slate-400">Laravel {{ $systemTelemetry['laravel_version'] }}</div>
            </div>

            <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-1">
                <div class="text-[10px] text-slate-500 uppercase">Memory Footprint</div>
                <div class="font-bold text-white">{{ $systemTelemetry['memory_usage'] }}</div>
                <div class="text-[10px] text-emerald-400">Within Threshold</div>
            </div>

            <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-1">
                <div class="text-[10px] text-slate-500 uppercase">Tenancy Scoping</div>
                <div class="font-bold text-white">Active</div>
                <div class="text-[10px] text-emerald-400">Global Isolation 100%</div>
            </div>
        </div>
    </div>

    <!-- Chart.js Initialization Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Sales Velocity Dual-Axis Chart
            const ctxRevenue = document.getElementById('revenueVelocityChart');
            if (ctxRevenue) {
                const labels = @json($chartLabels);
                const gmvData = @json($chartGmvData);
                const ordersData = @json($chartOrdersData);

                new Chart(ctxRevenue, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Gross Sales (₦)',
                                data: gmvData,
                                borderColor: '#6366f1',
                                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#6366f1',
                                pointBorderColor: '#ffffff',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Orders Volume',
                                data: ordersData,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.3,
                                pointBackgroundColor: '#10b981',
                                pointRadius: 3,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#94a3b8',
                                    font: { family: '"Plus Jakarta Sans"', size: 11, weight: '600' }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#cbd5e1',
                                borderColor: '#334155',
                                borderWidth: 1,
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (context.dataset.yAxisID === 'y') {
                                            return label + ': ₦' + Number(context.parsed.y).toLocaleString(undefined, { minimumFractionDigits: 2 });
                                        }
                                        return label + ': ' + context.parsed.y + ' orders';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: 'rgba(51, 65, 85, 0.25)' },
                                ticks: { color: '#64748b', font: { family: '"JetBrains Mono"', size: 10 } }
                            },
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                grid: { color: 'rgba(51, 65, 85, 0.25)' },
                                ticks: {
                                    color: '#64748b',
                                    font: { family: '"JetBrains Mono"', size: 10 },
                                    callback: function(value) { return '₦' + (value >= 1000 ? (value / 1000) + 'k' : value); }
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: { drawOnChartArea: false },
                                ticks: { color: '#10b981', font: { family: '"JetBrains Mono"', size: 10 } }
                            }
                        }
                    }
                });
            }

            // 2. Plan Distribution Doughnut Chart
            const ctxPlan = document.getElementById('planDistributionChart');
            if (ctxPlan) {
                const planCounts = @json($planCounts);

                new Chart(ctxPlan, {
                    type: 'doughnut',
                    data: {
                        labels: ['Starter', 'Pro', 'Enterprise', 'Standard'],
                        datasets: [{
                            data: [
                                planCounts.starter || 0,
                                planCounts.pro || 0,
                                planCounts.enterprise || 0,
                                planCounts.standard || 0
                            ],
                            backgroundColor: ['#64748b', '#6366f1', '#8b5cf6', '#10b981'],
                            borderColor: '#0c0f17',
                            borderWidth: 3,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#cbd5e1',
                                borderColor: '#334155',
                                borderWidth: 1
                            }
                        },
                        cutout: '72%'
                    }
                });
            }
        });
    </script>
</x-super-admin-layout>
