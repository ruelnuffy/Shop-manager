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
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">All-Time GMV</div>
            <div class="text-xl font-bold text-emerald-600 font-mono">₦{{ number_format($allTimeRevenue, 2) }}</div>
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

    <!-- Sales & Reports Period Filter Bar -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-bold text-slate-900">Store Sales &amp; Performance Reports</h2>
            <p class="text-xs text-slate-500 mt-0.5">Filter sales volume, average transaction size, and settlement status.</p>
        </div>

        <div class="flex items-center gap-1.5 flex-wrap">
            @php
                $periods = [
                    'all' => 'All Time',
                    'today' => 'Today',
                    '7_days' => 'Last 7 Days',
                    '30_days' => 'Last 30 Days',
                    'this_month' => 'This Month',
                ];
            @endphp
            @foreach ($periods as $key => $label)
                <a
                    href="{{ route('super-admin.tenants.show', [$tenant, 'period' => $key]) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $period === $key ? 'bg-indigo-600 text-white shadow-xs font-semibold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Period Analytics Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <div class="text-[11px] font-medium text-slate-500">Period Revenue (Paid)</div>
            <div class="text-2xl font-bold text-emerald-600 font-mono">₦{{ number_format($periodRevenue, 2) }}</div>
            <div class="text-xs text-slate-400">{{ number_format($periodPaidCount) }} successful settlements</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <div class="text-[11px] font-medium text-slate-500">Period Order Attempts</div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($periodTotalOrders) }}</div>
            <div class="text-xs text-slate-400">Total transaction traffic</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <div class="text-[11px] font-medium text-slate-500">Average Order Value (AOV)</div>
            <div class="text-2xl font-bold text-indigo-600 font-mono">₦{{ number_format($periodAov, 2) }}</div>
            <div class="text-xs text-slate-400">Per paid transaction</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-2">
            <div class="text-[11px] font-medium text-slate-500">Payment Breakdown</div>
            <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1 font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                    {{ $orderStatusCounts['paid'] }} Paid
                </span>
                <span class="inline-flex items-center gap-1 font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">
                    {{ $orderStatusCounts['pending'] }} Pending
                </span>
                <span class="inline-flex items-center gap-1 font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded">
                    {{ $orderStatusCounts['failed'] }} Failed
                </span>
            </div>
        </div>
    </div>

    <!-- Details, Admins & Orders Grid -->
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
            <!-- Store Administrators Card with Add & Reset Password Modals -->
            <div
                class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-4"
                x-data="{
                    addAdminOpen: false,
                    resetModalOpen: false,
                    resetAdminId: null,
                    resetAdminName: '',
                    resetAdminEmail: '',
                    openResetModal(id, name, email) {
                        this.resetAdminId = id;
                        this.resetAdminName = name;
                        this.resetAdminEmail = email;
                        this.resetModalOpen = true;
                    }
                }"
            >
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Store Administrators</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Manage administrative credentials and store access.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-semibold bg-slate-100 text-slate-700">
                            {{ $tenantAdmins->count() }} {{ Str::plural('Admin', $tenantAdmins->count()) }}
                        </span>
                        <button
                            type="button"
                            @click="addAdminOpen = true"
                            class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition flex items-center gap-1 cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Add Admin</span>
                        </button>
                    </div>
                </div>

                @if ($errors->has('admin_error'))
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                        {{ $errors->first('admin_error') }}
                    </div>
                @endif

                <div class="divide-y divide-slate-100">
                    @forelse ($tenantAdmins as $admin)
                        <div class="py-3.5 flex items-center justify-between flex-wrap gap-3 first:pt-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-xs shrink-0">
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                        <span>{{ $admin->name }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Admin
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $admin->email }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="openResetModal({{ $admin->id }}, '{{ addslashes($admin->name) }}', '{{ addslashes($admin->email) }}')"
                                    class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition cursor-pointer flex items-center gap-1"
                                    title="Reset this admin's password"
                                >
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                    <span>Reset Password</span>
                                </button>

                                @if ($tenantAdmins->count() > 1)
                                    <form method="POST" action="{{ route('super-admin.tenants.admins.destroy', [$tenant, $admin]) }}" onsubmit="return confirm('Are you sure you want to remove administrator {{ $admin->email }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                            title="Remove admin access"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 py-3 text-center">No dedicated tenant admin found.</div>
                    @endforelse
                </div>

                <!-- Modal: Add Admin -->
                <div
                    x-show="addAdminOpen"
                    x-cloak
                    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4"
                    @keydown.escape.window="addAdminOpen = false"
                >
                    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4" @click.outside="addAdminOpen = false">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Add Store Administrator</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Provision credentials for a new store admin.</p>
                            </div>
                            <button type="button" @click="addAdminOpen = false" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
                        </div>

                        <form method="POST" action="{{ route('super-admin.tenants.admins.store', $tenant) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Funke Adeleke">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                                <input type="email" name="email" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="admin@store.com">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                                <input type="password" name="password" minlength="8" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="At least 8 characters">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm Password</label>
                                <input type="password" name="password_confirmation" minlength="8" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" @click="addAdminOpen = false" class="px-3.5 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</button>
                                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs cursor-pointer">Provision Account</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal: Reset Password -->
                <div
                    x-show="resetModalOpen"
                    x-cloak
                    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4"
                    @keydown.escape.window="resetModalOpen = false"
                >
                    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4" @click.outside="resetModalOpen = false">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Reset Admin Password</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Account: <span class="font-mono text-slate-700" x-text="resetAdminEmail"></span></p>
                            </div>
                            <button type="button" @click="resetModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
                        </div>

                        <form
                            method="POST"
                            :action="'{{ url('super-admin/tenants/' . $tenant->id . '/admins') }}/' + resetAdminId + '/reset-password'"
                            class="space-y-4"
                        >
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">New Password</label>
                                <input type="password" name="password" minlength="8" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Minimum 8 characters">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm New Password</label>
                                <input type="password" name="password_confirmation" minlength="8" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" @click="resetModalOpen = false" class="px-3.5 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</button>
                                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs cursor-pointer">Update Password</button>
                            </div>
                        </form>
                    </div>
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
