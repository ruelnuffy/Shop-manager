<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Financial Intelligence
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium">{{ $startDate }} &rarr; {{ $endDate }}</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-brand-charcoal text-slate-900 mt-1">
                    Financial &amp; Sales Reports
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Real-time omnichannel revenue breakdown, gross margin valuation, and automated ledger exports.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 text-white hover:bg-indigo-600 text-xs font-semibold rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export Report (CSV)</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Date Range Filter Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col md:flex-row md:items-end gap-3.5">
                <div class="flex-1">
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reporting Start Date
                    </label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                           class="w-full px-3.5 py-2 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                </div>
                <div class="flex-1">
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reporting End Date
                    </label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                           class="w-full px-3.5 py-2 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-xl hover:bg-indigo-700 transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter Window
                    </button>
                    <a href="{{ route('admin.reports.index') }}"
                       class="px-3.5 py-2 border border-slate-200 text-slate-600 text-xs font-medium rounded-xl hover:bg-slate-50 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Revenue Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Combined Revenue -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-5 rounded-2xl shadow-sm relative overflow-hidden border border-slate-700/50">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold text-slate-400">Total Net Sales</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30">Omnichannel</span>
                </div>
                <div class="font-display font-extrabold text-2xl text-white tracking-tight">
                    ₦{{ number_format($combinedRevenue, 0) }}
                </div>
                <div class="mt-2 text-[11px] text-slate-400 flex items-center gap-1.5">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Online store + POS sales</span>
                </div>
            </div>

            <!-- Online Revenue -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold text-slate-500">Storefront Orders</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Paystack</span>
                </div>
                <div class="font-display font-extrabold text-2xl text-slate-900 tracking-tight">
                    ₦{{ number_format($onlineRevenue, 0) }}
                </div>
                <div class="text-[11px] text-slate-500 mt-2 flex items-center justify-between">
                    <span>{{ $onlineOrdersCount }} completed orders</span>
                    <span class="font-semibold text-emerald-600 font-mono">100% Settled</span>
                </div>
            </div>

            <!-- In-Store Revenue -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold text-slate-500">In-Store / POS</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Direct Retail</span>
                </div>
                <div class="font-display font-extrabold text-2xl text-slate-900 tracking-tight">
                    ₦{{ number_format($manualSalesRevenue, 0) }}
                </div>
                <div class="text-[11px] text-slate-500 mt-2 flex items-center justify-between">
                    <span>{{ $manualSalesCount }} walk-in sales</span>
                    <span class="font-semibold text-amber-600 font-mono">Cash/Transfer</span>
                </div>
            </div>

            <!-- Total Valuation -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold text-slate-500">Inventory Valuation</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">Live Asset</span>
                </div>
                <div class="font-display font-extrabold text-2xl text-slate-900 tracking-tight">
                    ₦{{ number_format($totalValuation, 0) }}
                </div>
                <div class="text-[11px] text-slate-500 mt-2 flex items-center justify-between">
                    <span>{{ $totalUnitsInStock }} units in stock</span>
                    <span class="text-indigo-600 font-semibold font-mono">Retail Value</span>
                </div>
            </div>
        </div>

        <!-- Comparative Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Channel Distribution -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 tracking-tight">
                            Omnichannel Channel Distribution
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Ratio between digital storefront orders and in-store point of sale between {{ $startDate }} and {{ $endDate }}.
                        </p>
                    </div>
                </div>

                @php
                    $onlinePct = $combinedRevenue > 0 ? round(($onlineRevenue / $combinedRevenue) * 100, 1) : 0;
                    $inStorePct = $combinedRevenue > 0 ? round(($manualSalesRevenue / $combinedRevenue) * 100, 1) : 0;
                @endphp

                <div class="space-y-4 pt-2">
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                            <span class="flex items-center gap-2 text-slate-700">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                                Storefront Checkout (Paystack)
                            </span>
                            <span class="text-slate-900 font-mono">{{ $onlinePct }}% &bull; ₦{{ number_format($onlineRevenue, 0) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: {{ $onlinePct }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                            <span class="flex items-center gap-2 text-slate-700">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                In-Store / Walk-in Register (POS)
                            </span>
                            <span class="text-slate-900 font-mono">{{ $inStorePct }}% &bull; ₦{{ number_format($manualSalesRevenue, 0) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $inStorePct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Export & Audit Guidance -->
            <div class="bg-gradient-to-br from-slate-50 to-indigo-50/30 rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                        <h3 class="font-bold text-sm text-slate-900">
                            Ledger &amp; Financial Compliance
                        </h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Download raw itemized sales logs formatted for bookkeeping, Excel, and accounting software. The CSV export contains individual records for both online orders and in-store sales recorded during the specified date range.
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-4">
                    <span class="text-xs text-slate-500">
                        Date Window: <strong class="text-slate-800">{{ $startDate }}</strong> &rarr; <strong class="text-slate-800">{{ $endDate }}</strong>
                    </span>
                    <a href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                       class="px-4 py-2 bg-slate-900 text-white text-xs font-semibold rounded-xl hover:bg-indigo-600 transition shadow-xs flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Download .CSV</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
