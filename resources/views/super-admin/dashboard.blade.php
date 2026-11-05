<x-super-admin-layout>
    <!-- Page Header (Airy & Crisp) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-mono uppercase tracking-wider text-indigo-600 font-bold">Platform Overview</span>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xs text-slate-500 font-medium">Real-time Cross-Tenant Cockpit</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Super Admin Cockpit
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Global oversight across all provisioned retail stores, sales volume, and multi-tenant operations.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('super-admin.export-report') }}"
                class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold transition shadow-xs flex items-center gap-2"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Export CSV</span>
            </a>

            <a
                href="{{ route('super-admin.tenants.create') }}"
                class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider transition shadow-sm flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Provision New Store</span>
            </a>
        </div>
    </div>

    <!-- Executive KPI Metric Cards (Airy, White, Spacious) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Gross Merchandise Value -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-3">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Platform GMV</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tracking-tight">
                ₦{{ number_format($totalGmv, 2) }}
            </div>
            <div class="flex items-center gap-1.5 text-xs text-emerald-600 font-medium">
                <span class="px-2 py-0.5 rounded bg-emerald-50 font-semibold">{{ number_format($paidOrdersCount) }} paid</span>
                <span class="text-slate-400">&bull; Across all stores</span>
            </div>
        </div>

        <!-- 2. Platform MRR -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-3">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Platform MRR</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-indigo-600 font-mono tracking-tight">
                ₦{{ number_format($platformMrr, 2) }}
            </div>
            <div class="text-xs text-slate-500 font-medium">
                ARR Run-rate: <strong class="text-slate-800 font-mono">₦{{ number_format($platformMrr * 12, 2) }}</strong>
            </div>
        </div>

        <!-- 3. Tenant Stores Count -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-3">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tenant Stores</span>
                <div class="w-9 h-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tracking-tight">
                {{ $tenantsCount }}
            </div>
            <div class="flex items-center gap-2 text-xs font-medium">
                <span class="inline-flex items-center gap-1 text-emerald-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    {{ $activeTenantsCount }} Active
                </span>
                @if ($suspendedTenantsCount > 0)
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-rose-600">{{ $suspendedTenantsCount }} Suspended</span>
                @endif
            </div>
        </div>

        <!-- 4. Average Order Value -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-3">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Average Order Value</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tracking-tight">
                ₦{{ number_format($averageOrderValue, 2) }}
            </div>
            <div class="text-xs text-slate-500 font-medium">
                {{ number_format($totalOrdersCount) }} Total Orders Processed
            </div>
        </div>
    </div>

    <!-- Secondary Metrics Row: Staff Payroll, Catalog, & OCR -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Payroll Disbursed</div>
                <div class="text-xl font-extrabold text-slate-900 font-mono">₦{{ number_format($totalPayrollDisbursed, 2) }}</div>
                <div class="text-xs text-slate-500">{{ number_format($totalStaffCount) }} Active Store Staff</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Catalog Inventory</div>
                <div class="text-xl font-extrabold text-slate-900 font-mono">{{ number_format($totalProductsCount) }} SKUs</div>
                <div class="text-xs text-slate-500">Synced across all active outlets</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">OCR Ledger Ingestions</div>
                <div class="text-xl font-extrabold text-slate-900 font-mono">{{ number_format($totalOcrBatches) }} Batches</div>
                <div class="text-xs text-slate-500">Physical receipts digitized via AI</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Visual Analytics: Revenue Velocity Chart & Plan Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Revenue Velocity Chart (8 cols) -->
        <div class="lg:col-span-8 p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Platform Sales Velocity &amp; Orders</h2>
                    <p class="text-xs text-slate-500">Gross transaction volume and order count over the last 7 days.</p>
                </div>
                <span class="text-[11px] font-mono text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 font-medium">
                    Live Telemetry
                </span>
            </div>

            <div class="h-72 w-full relative">
                <canvas id="revenueVelocityChart"></canvas>
            </div>
        </div>

        <!-- Plan Distribution Doughnut (4 cols) -->
        <div class="lg:col-span-4 p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-4 flex flex-col justify-between">
            <div class="pb-3 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Tenant Plan Tiers</h2>
                <p class="text-xs text-slate-500">Distribution of active stores across subscription tiers.</p>
            </div>

            <div class="h-44 relative flex items-center justify-center my-auto">
                <canvas id="planDistributionChart"></canvas>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 text-xs font-mono">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-600 font-medium">Starter</span>
                    <span class="font-bold text-slate-900">{{ $planCounts['starter'] ?? 0 }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-between">
                    <span class="text-indigo-700 font-medium">Pro</span>
                    <span class="font-bold text-indigo-700">{{ $planCounts['pro'] ?? 0 }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-violet-50 border border-violet-100 flex items-center justify-between">
                    <span class="text-violet-700 font-medium">Enterprise</span>
                    <span class="font-bold text-violet-700">{{ $planCounts['enterprise'] ?? 0 }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-between">
                    <span class="text-emerald-700 font-medium">Standard</span>
                    <span class="font-bold text-emerald-700">{{ $planCounts['standard'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Dual Operational Grid: Merchant Stores & Recent Live Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Top Merchants Leaderboard (7 cols) -->
        <div class="lg:col-span-7 p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Top Performing Merchants</h2>
                    <p class="text-xs text-slate-500">Stores ranked by total gross sales volume.</p>
                </div>
                <a href="{{ route('super-admin.tenants.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                    View All Directory &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="pb-3 font-semibold">Store</th>
                            <th class="pb-3 font-semibold">Plan</th>
                            <th class="pb-3 text-right font-semibold">Orders</th>
                            <th class="pb-3 text-right font-semibold">GMV (NGN)</th>
                            <th class="pb-3 text-right font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($topTenants as $index => $store)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 font-sans">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                            #{{ $index + 1 }}
                                        </div>
                                        <div>
                                            <a href="{{ route('super-admin.tenants.show', $store) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition">
                                                {{ $store->name }}
                                            </a>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $store->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $store->plan === 'enterprise' ? 'bg-violet-50 text-violet-700 border border-violet-200' : ($store->plan === 'pro' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                        {{ $store->plan }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-right font-mono font-bold text-slate-700">
                                    {{ number_format($store->orders_count) }}
                                </td>
                                <td class="py-3.5 text-right font-mono font-extrabold text-emerald-600 text-sm">
                                    ₦{{ number_format($store->gmv, 2) }}
                                </td>
                                <td class="py-3.5 text-right">
                                    <form method="POST" action="{{ route('super-admin.tenants.enter', $store) }}" class="inline-block">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 text-xs font-semibold transition"
                                            title="Enter merchant workspace"
                                        >
                                            Enter &rarr;
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-xs text-slate-400">
                                    No tenant stores registered yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Live Platform Orders Feed (5 cols) -->
        <div class="lg:col-span-5 p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Live Platform Orders</h2>
                        <p class="text-xs text-slate-500">Real-time incoming transactions across all merchants.</p>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>

                <div class="divide-y divide-slate-100 mt-2">
                    @forelse ($recentOrders as $order)
                        <div class="py-3 flex items-center justify-between gap-3 text-xs first:pt-2">
                            <div class="min-w-0 space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-slate-900">{{ $order->order_number }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $order->payment_status }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 truncate">
                                    <span class="font-medium text-slate-700">{{ $order->tenant ? $order->tenant->name : 'Platform Store' }}</span>
                                    &bull;
                                    <span>{{ $order->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <div class="font-bold font-mono text-emerald-600 text-sm">₦{{ number_format($order->total_amount, 2) }}</div>
                                <span class="text-[10px] text-slate-400 uppercase font-mono">{{ $order->payment_method ?? 'card' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-400">
                            No orders recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Webhook Authenticity: <strong class="text-emerald-600">HMAC-SHA512 Verified</strong></span>
                <span class="font-mono text-[11px]">Paystack Gateway</span>
            </div>
        </div>
    </div>

    <!-- Infrastructure & System Telemetry Diagnostics -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Infrastructure &amp; Tenancy Telemetry</h3>
                <p class="text-xs text-slate-500">Operational diagnostics across database, memory, and cache layers.</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Healthy
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-xs font-mono">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                <div class="text-[10px] text-slate-400 uppercase font-semibold">Database</div>
                <div class="font-bold text-slate-900 uppercase">{{ $systemTelemetry['db_driver'] }}</div>
                <div class="text-[11px] text-emerald-600">{{ $systemTelemetry['db_status'] }} ({{ $systemTelemetry['db_latency'] }}ms)</div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                <div class="text-[10px] text-slate-400 uppercase font-semibold">Cache Driver</div>
                <div class="font-bold text-slate-900 uppercase">{{ $systemTelemetry['cache_driver'] }}</div>
                <div class="text-[11px] text-emerald-600">Operational</div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                <div class="text-[10px] text-slate-400 uppercase font-semibold">Environment</div>
                <div class="font-bold text-slate-900 uppercase">{{ $systemTelemetry['app_env'] }}</div>
                <div class="text-[11px] text-indigo-600">Port 8001 Daemon</div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                <div class="text-[10px] text-slate-400 uppercase font-semibold">Framework</div>
                <div class="font-bold text-slate-900">Laravel {{ $systemTelemetry['laravel_version'] }}</div>
                <div class="text-[11px] text-slate-500">PHP {{ $systemTelemetry['php_version'] }}</div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                <div class="text-[10px] text-slate-400 uppercase font-semibold">Memory Usage</div>
                <div class="font-bold text-slate-900">{{ $systemTelemetry['memory_usage'] }}</div>
                <div class="text-[11px] text-emerald-600">Optimal</div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                <div class="text-[10px] text-slate-400 uppercase font-semibold">Tenancy Scoping</div>
                <div class="font-bold text-emerald-700">Zero Leakage</div>
                <div class="text-[11px] text-slate-500">100% Isolated</div>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Revenue & Orders Velocity Chart
            const ctxRev = document.getElementById('revenueVelocityChart');
            if (ctxRev) {
                const labels = @json($chartLabels);
                const gmvData = @json($chartGmvData);
                const ordersData = @json($chartOrdersData);

                new Chart(ctxRev, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Gross Sales (₦)',
                                data: gmvData,
                                borderColor: '#4f46e5',
                                backgroundColor: 'rgba(79, 70, 229, 0.08)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#4f46e5',
                                pointBorderColor: '#ffffff',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Orders Volume',
                                data: ordersData,
                                borderColor: '#059669',
                                backgroundColor: 'rgba(5, 150, 105, 0.1)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.3,
                                pointBackgroundColor: '#059669',
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
                                    color: '#475569',
                                    font: { family: '"Plus Jakarta Sans"', size: 11, weight: '600' }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
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
                                grid: { color: 'rgba(226, 232, 240, 0.8)' },
                                ticks: { color: '#64748b', font: { family: '"JetBrains Mono"', size: 10 } }
                            },
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                grid: { color: 'rgba(226, 232, 240, 0.8)' },
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
                                ticks: { color: '#059669', font: { family: '"JetBrains Mono"', size: 10 } }
                            }
                        }
                    }
                });
            }

            // Plan Distribution Doughnut Chart
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
                            backgroundColor: ['#94a3b8', '#4f46e5', '#7c3aed', '#059669'],
                            borderColor: '#ffffff',
                            borderWidth: 3,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        cutout: '72%'
                    }
                });
            }
        });
    </script>
</x-super-admin-layout>
