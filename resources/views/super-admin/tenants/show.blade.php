<x-super-admin-layout>
    <!-- Header with Back Link & Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <a href="{{ route('super-admin.tenants.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 transition">
                    &larr; Back to Stores Directory
                </a>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-2xl font-bold text-slate-900">{{ $tenant->name }}</h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase {{ $tenant->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                    {{ ucfirst($tenant->status) }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ ucfirst($tenant->plan) }}
                </span>
            </div>
            <p class="text-xs text-slate-500">
                Slug: <code class="text-slate-800">{{ $tenant->slug }}</code> &bull; 
                Domain: <code class="text-slate-800">{{ $tenant->domain ?? ($tenant->subdomain ? $tenant->subdomain . '.' . config('app.url_base', 'shopmanager.test') : 'Not set') }}</code>
            </p>
        </div>

        <!-- Quick Store Actions -->
        <div class="flex items-center gap-2.5 flex-wrap">
            <form method="POST" action="{{ route('super-admin.tenants.enter', $tenant) }}">
                @csrf
                <button
                    type="submit"
                    class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider transition shadow-sm flex items-center gap-1.5 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span>Masquerade Console &nearr;</span>
                </button>
            </form>

            <a
                href="{{ route('super-admin.tenants.edit', $tenant) }}"
                class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition border border-slate-200 shadow-xs flex items-center gap-1.5"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Settings</span>
            </a>

            <form method="POST" action="{{ route('super-admin.tenants.toggle-status', $tenant) }}">
                @csrf
                <button
                    type="submit"
                    onclick="return confirm('Are you sure you want to change this store status?')"
                    class="px-3.5 py-2.5 rounded-xl text-xs font-semibold transition border cursor-pointer {{ $tenant->status === 'active' ? 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}"
                >
                    {{ $tenant->status === 'active' ? 'Suspend Store' : 'Activate Store' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Store Metrics Grid (Airy White Cards) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="p-4.5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total GMV</div>
            <div class="text-xl font-bold text-emerald-600 font-mono">₦{{ number_format($tenantRevenue, 2) }}</div>
        </div>
        <div class="p-4.5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Orders</div>
            <div class="text-xl font-bold text-slate-900 font-mono">{{ number_format($tenant->orders_count) }}</div>
        </div>
        <div class="p-4.5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Catalog SKUs</div>
            <div class="text-xl font-bold text-slate-900 font-mono">{{ number_format($tenant->products_count) }}</div>
        </div>
        <div class="p-4.5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Staff Members</div>
            <div class="text-xl font-bold text-slate-900 font-mono">{{ number_format($tenant->staff_count) }}</div>
        </div>
        <div class="p-4.5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Retail Outlets</div>
            <div class="text-xl font-bold text-slate-900 font-mono">{{ number_format($tenant->shop_locations_count) }}</div>
        </div>
        <div class="p-4.5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">OCR Ledgers</div>
            <div class="text-xl font-bold text-slate-900 font-mono">{{ number_format($tenant->ocr_import_batches_count) }}</div>
        </div>
    </div>

    <!-- Details & Features Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Col 1: Store Configuration & Features -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-5">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Features &amp; Subscriptions</h2>
                <p class="text-xs text-slate-500 mt-0.5">Enabled capabilities for this retail domain.</p>
            </div>

            <div class="space-y-2.5">
                @php
                    $features = [
                        'storefront' => 'Public Online Storefront',
                        'payroll' => 'Staff Payroll & Auto-Disbursements',
                        'ocr' => 'OCR Ledger Ingestion & Scanning',
                        'custom_domain' => 'Custom Domain Routing',
                    ];
                @endphp
                @foreach ($features as $key => $label)
                    @php $hasFeature = $tenant->hasFeature($key); @endphp
                    <div class="flex items-center justify-between p-3 rounded-xl {{ $hasFeature ? 'bg-indigo-50/60 border border-indigo-200/60' : 'bg-slate-50 border border-slate-200 text-slate-400' }}">
                        <span class="text-xs font-semibold {{ $hasFeature ? 'text-slate-900' : 'text-slate-400 line-through' }}">{{ $label }}</span>
                        @if ($hasFeature)
                            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Enabled
                            </span>
                        @else
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Disabled</span>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">Created:</span>
                    <span class="text-slate-700 font-mono font-medium">{{ $tenant->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Last Modified:</span>
                    <span class="text-slate-700 font-mono font-medium">{{ $tenant->updated_at->diffForHumans() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Currency:</span>
                    <span class="text-slate-700 font-mono font-medium">{{ data_get($tenant->settings, 'currency', 'NGN') }} ({{ data_get($tenant->settings, 'currency_symbol', '₦') }})</span>
                </div>
            </div>
        </div>

        <!-- Col 2 & 3: Store Administrators & Recent Orders -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Store Administrators Card -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Store Administrators</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Users with administrative ownership over this tenant.</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-semibold bg-slate-100 text-slate-700">
                        {{ $tenantAdmins->count() }} {{ Str::plural('Admin', $tenantAdmins->count()) }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($tenantAdmins as $admin)
                        <div class="py-3 flex items-center justify-between first:pt-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">{{ $admin->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $admin->email }}</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                                Store Owner
                            </span>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 py-3 text-center">No dedicated tenant admin found.</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Store Orders Card -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Recent Store Orders</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Latest transactions recorded for this merchant.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 text-[10px] uppercase font-bold tracking-wider">
                                <th class="pb-2 font-semibold">Order</th>
                                <th class="pb-2 font-semibold">Customer</th>
                                <th class="pb-2 font-semibold">Amount</th>
                                <th class="pb-2 font-semibold">Status</th>
                                <th class="pb-2 font-semibold text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-mono">
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td class="py-3 font-bold text-slate-900">{{ $order->order_number }}</td>
                                    <td class="py-3 text-slate-600 font-sans truncate max-w-[150px]">{{ $order->user->name ?? $order->customer_name ?? 'Guest' }}</td>
                                    <td class="py-3 text-emerald-600 font-bold">₦{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-sans font-bold uppercase {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ $order->payment_status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-slate-400 text-right text-[11px]">{{ $order->created_at->format('M d, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 font-sans text-xs">
                                        No orders recorded yet for this store.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>
