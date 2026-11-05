<x-super-admin-layout>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Super Admin Cockpit</h1>
            <p class="text-xs text-slate-500 mt-0.5">Platform Overview</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a
                href="{{ route('super-admin.export-report') }}"
                class="px-3.5 py-2 rounded-lg bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-medium transition shadow-xs"
            >
                Export CSV
            </a>

            <a
                href="{{ route('super-admin.tenants.create') }}"
                class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition shadow-xs"
            >
                + Provision New Store
            </a>
        </div>
    </div>

    <!-- 4 Main KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: GMV -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-xs space-y-2">
            <div class="text-xs font-medium text-slate-500">Platform GMV</div>
            <div class="text-2xl font-bold text-slate-900">
                ₦{{ number_format($totalGmv, 2) }}
            </div>
            <div class="text-xs text-emerald-600 font-medium">
                {{ number_format($paidOrdersCount) }} paid orders
            </div>
        </div>

        <!-- Card 2: MRR -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-xs space-y-2">
            <div class="text-xs font-medium text-slate-500">Monthly Recurring Revenue</div>
            <div class="text-2xl font-bold text-indigo-600">
                ₦{{ number_format($platformMrr, 2) }}
            </div>
            <div class="text-xs text-slate-500">
                Annual: ₦{{ number_format($platformMrr * 12, 2) }}
            </div>
        </div>

        <!-- Card 3: Stores -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-xs space-y-2">
            <div class="text-xs font-medium text-slate-500">Tenant Stores</div>
            <div class="text-2xl font-bold text-slate-900">
                {{ $tenantsCount }}
            </div>
            <div class="text-xs text-slate-500 flex items-center gap-1.5">
                <span class="text-emerald-600 font-medium">{{ $activeTenantsCount }} Active</span>
                @if ($suspendedTenantsCount > 0)
                    <span>&bull;</span>
                    <span class="text-rose-600">{{ $suspendedTenantsCount }} Suspended</span>
                @endif
            </div>
        </div>

        <!-- Card 4: Average Order -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-xs space-y-2">
            <div class="text-xs font-medium text-slate-500">Average Order Value</div>
            <div class="text-2xl font-bold text-slate-900">
                ₦{{ number_format($averageOrderValue, 2) }}
            </div>
            <div class="text-xs text-slate-500">
                {{ number_format($totalOrdersCount) }} total attempts
            </div>
        </div>
    </div>

    <!-- Quick Stats Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] text-slate-500">Payroll Disbursed</div>
                <div class="text-lg font-bold text-slate-900">₦{{ number_format($totalPayrollDisbursed, 2) }}</div>
            </div>
            <span class="text-xs text-slate-400">{{ number_format($totalStaffCount) }} Staff</span>
        </div>

        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] text-slate-500">Products Catalog</div>
                <div class="text-lg font-bold text-slate-900">{{ number_format($totalProductsCount) }} SKUs</div>
            </div>
            <span class="text-xs text-slate-400">All Outlets</span>
        </div>

        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] text-slate-500">OCR Ledgers</div>
                <div class="text-lg font-bold text-slate-900">{{ number_format($totalOcrBatches) }} Batches</div>
            </div>
            <span class="text-xs text-slate-400">Processed</span>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Sales Velocity -->
        <div class="lg:col-span-8 p-5 rounded-xl bg-white border border-slate-200 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Revenue &amp; Orders Trend</h2>
                <span class="text-[11px] text-slate-400">Last 7 Days</span>
            </div>
            <div class="h-64 w-full relative">
                <canvas id="revenueVelocityChart"></canvas>
            </div>
        </div>

        <!-- Plan Breakdown -->
        <div class="lg:col-span-4 p-5 rounded-xl bg-white border border-slate-200 shadow-xs space-y-3 flex flex-col justify-between">
            <h2 class="text-sm font-semibold text-slate-900">Plans Distribution</h2>
            <div class="h-44 relative flex items-center justify-center my-auto">
                <canvas id="planDistributionChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-slate-100">
                <div class="flex justify-between p-1.5 rounded bg-slate-50 text-slate-600">
                    <span>Starter</span>
                    <span class="font-bold text-slate-900">{{ $planCounts['starter'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between p-1.5 rounded bg-indigo-50 text-indigo-700">
                    <span>Pro</span>
                    <span class="font-bold">{{ $planCounts['pro'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between p-1.5 rounded bg-violet-50 text-violet-700">
                    <span>Enterprise</span>
                    <span class="font-bold">{{ $planCounts['enterprise'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between p-1.5 rounded bg-emerald-50 text-emerald-700">
                    <span>Standard</span>
                    <span class="font-bold">{{ $planCounts['standard'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Top Stores Leaderboard (7 cols) -->
        <div class="lg:col-span-7 p-5 rounded-xl bg-white border border-slate-200 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-900">Top Stores by Revenue</h2>
                <a href="{{ route('super-admin.tenants.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800">
                    View All &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[11px] text-slate-400 border-b border-slate-100">
                            <th class="pb-2.5 font-medium">Store</th>
                            <th class="pb-2.5 font-medium">Plan</th>
                            <th class="pb-2.5 text-right font-medium">Orders</th>
                            <th class="pb-2.5 text-right font-medium">GMV</th>
                            <th class="pb-2.5 text-right font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($topTenants as $index => $store)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3">
                                    <a href="{{ route('super-admin.tenants.show', $store) }}" class="font-medium text-slate-900 hover:text-indigo-600">
                                        {{ $store->name }}
                                    </a>
                                    <div class="text-[10px] text-slate-400">{{ $store->slug }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold {{ $store->plan === 'enterprise' ? 'bg-violet-50 text-violet-700' : ($store->plan === 'pro' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-600') }}">
                                        {{ $store->plan }}
                                    </span>
                                </td>
                                <td class="py-3 text-right font-medium text-slate-700">
                                    {{ number_format($store->orders_count) }}
                                </td>
                                <td class="py-3 text-right font-semibold text-emerald-600">
                                    ₦{{ number_format($store->gmv, 2) }}
                                </td>
                                <td class="py-3 text-right">
                                    <form method="POST" action="{{ route('super-admin.tenants.enter', $store) }}" class="inline-block">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1 rounded-md bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 text-xs transition"
                                        >
                                            Enter
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">No stores found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Orders Feed (5 cols) -->
        <div class="lg:col-span-5 p-5 rounded-xl bg-white border border-slate-200 shadow-xs space-y-3">
            <div class="pb-2 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-900">Recent Orders</h2>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($recentOrders as $order)
                    <div class="py-2.5 flex items-center justify-between text-xs first:pt-1">
                        <div>
                            <div class="font-medium text-slate-900">{{ $order->order_number }}</div>
                            <div class="text-[11px] text-slate-400">
                                {{ $order->tenant ? $order->tenant->name : 'Store' }} &bull; {{ $order->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="font-semibold text-slate-900">₦{{ number_format($order->total_amount, 2) }}</div>
                            <span class="inline-block px-1.5 py-0.2 rounded text-[9px] uppercase font-semibold {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $order->payment_status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">No orders yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctxRev = document.getElementById('revenueVelocityChart');
            if (ctxRev) {
                new Chart(ctxRev, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: 'Revenue (₦)',
                                data: @json($chartGmvData),
                                borderColor: '#4f46e5',
                                backgroundColor: 'rgba(79, 70, 229, 0.08)',
                                borderWidth: 2.5,
                                fill: true,
                                tension: 0.3,
                                pointBackgroundColor: '#4f46e5',
                                pointRadius: 3,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Orders',
                                data: @json($chartOrdersData),
                                borderColor: '#10b981',
                                backgroundColor: 'transparent',
                                borderWidth: 2,
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
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                labels: { font: { family: 'Poppins', size: 11 } }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: 'rgba(241, 245, 249, 1)' },
                                ticks: { font: { family: 'Poppins', size: 10 }, color: '#64748b' }
                            },
                            y: {
                                position: 'left',
                                grid: { color: 'rgba(241, 245, 249, 1)' },
                                ticks: {
                                    font: { family: 'Poppins', size: 10 },
                                    color: '#64748b',
                                    callback: function(v) { return '₦' + (v >= 1000 ? (v / 1000) + 'k' : v); }
                                }
                            },
                            y1: {
                                position: 'right',
                                grid: { drawOnChartArea: false },
                                ticks: { font: { family: 'Poppins', size: 10 }, color: '#10b981' }
                            }
                        }
                    }
                });
            }

            const ctxPlan = document.getElementById('planDistributionChart');
            if (ctxPlan) {
                const plans = @json($planCounts);
                new Chart(ctxPlan, {
                    type: 'doughnut',
                    data: {
                        labels: ['Starter', 'Pro', 'Enterprise', 'Standard'],
                        datasets: [{
                            data: [plans.starter || 0, plans.pro || 0, plans.enterprise || 0, plans.standard || 0],
                            backgroundColor: ['#94a3b8', '#4f46e5', '#8b5cf6', '#10b981'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        cutout: '70%'
                    }
                });
            }
        });
    </script>
</x-super-admin-layout>
