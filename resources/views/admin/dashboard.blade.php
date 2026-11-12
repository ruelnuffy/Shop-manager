<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <h1 class="font-display font-extrabold text-lg text-slate-900 tracking-tight">
                Atelier Overview
            </h1>
            <span class="text-xs text-slate-400 font-medium hidden sm:inline">&bull; Daily Workspace</span>
        </div>
    </x-slot>

    @php
        $currentTenant = app(\App\Services\CurrentTenant::class)->get();
        $storeName = $currentTenant ? $currentTenant->setting('branding.name', $currentTenant->name) : config('app.platform_name', 'ShopManager');
    @endphp

    <div class="max-w-6xl mx-auto space-y-8">
        <!-- Welcome Greeting -->
        <div class="pt-2">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Welcome back</span>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">
                    {{ $storeName }}
                </h2>

                <div class="flex items-center gap-2">
                    @if ($currentTenant && $currentTenant->hasFeature('storefront'))
                        <a href="{{ route('storefront') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white text-slate-700 text-xs font-semibold shadow-md shadow-slate-900/5 hover:shadow-lg transition">
                            <span>View Storefront</span>
                            <span class="text-slate-400">&nearr;</span>
                        </a>
                    @endif

                    @if (Auth::user()->isAdmin() && !session()->has('impersonator_id'))
                        <form method="POST" action="{{ route('admin.impersonate-staff') }}" class="inline">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-md shadow-emerald-950/15 hover:shadow-lg transition cursor-pointer"
                                title="Login directly to Staff Walk-In POS Till"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                <span>Login to Staff Dashboard &nearr;</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sticky Notes Workspace Grid (Completely Borderless with Soft Shadows) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pt-2">

            <!-- Sticky Note 1: Yellow - Make a Sale (POS) -->
            <div class="group relative rounded-2xl rounded-br-[28px] p-6 bg-[#fef9c3] shadow-lg shadow-amber-900/5 hover:shadow-2xl hover:shadow-amber-900/10 hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[220px]">
                <!-- Frosted tape at top center (borderless) -->
                <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-16 h-5 bg-white/80 backdrop-blur-xs shadow-xs rounded-xs rotate-[-1deg] pointer-events-none"></div>
                <!-- Folded dog-ear corner effect on bottom-right -->
                <div class="absolute bottom-0 right-0 w-6 h-6 bg-gradient-to-tl from-amber-300/40 to-transparent rounded-br-[28px] pointer-events-none"></div>

                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800/70">Walk-In Register</span>
                    <h3 class="font-display font-extrabold text-2xl text-slate-900 mt-1">Make a Sale</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-700 font-medium">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                            <span><strong>Today's Revenue:</strong> ₦{{ number_format($todayRevenue, 0) }}</span>
                        </div>
                        <div class="text-slate-600 pl-3.5 text-[11px]">Barcode scan, cash, &amp; card checkout</div>
                        <div class="text-slate-600 pl-3.5 text-[11px]">Instant receipt generation</div>
                    </div>
                </div>

                <div class="pt-5">
                    <a href="{{ route('admin.sales.create') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-amber-950 group-hover:text-black group-hover:underline">
                        <span>Open register</span>
                        <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Sticky Note 2: Mint Green - Orders -->
            <div class="group relative rounded-2xl rounded-br-[28px] p-6 bg-[#dcfce7] shadow-lg shadow-emerald-900/5 hover:shadow-2xl hover:shadow-emerald-900/10 hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[220px]">
                <!-- Frosted tape at top center (borderless) -->
                <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-16 h-5 bg-white/80 backdrop-blur-xs shadow-xs rounded-xs rotate-[1deg] pointer-events-none"></div>
                <!-- Folded dog-ear corner -->
                <div class="absolute bottom-0 right-0 w-6 h-6 bg-gradient-to-tl from-emerald-300/40 to-transparent rounded-br-[28px] pointer-events-none"></div>

                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800/70">Fulfillment &amp; Shipping</span>
                    <h3 class="font-display font-extrabold text-2xl text-slate-900 mt-1">Orders</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-700 font-medium">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            <span><strong>{{ $pendingOrdersCount }}</strong> pending to pack</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span><strong>{{ $processingOrdersCount }}</strong> out in delivery</span>
                        </div>
                        <div class="text-slate-600 pl-3.5 text-[11px]">{{ $totalOrdersCount }} all-time customer orders</div>
                    </div>
                </div>

                <div class="pt-5">
                    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-emerald-950 group-hover:text-black group-hover:underline">
                        <span>Open orders</span>
                        <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Sticky Note 3: Pastel Pink - Products -->
            <div class="group relative rounded-2xl rounded-br-[28px] p-6 bg-[#ffe4e6] shadow-lg shadow-rose-900/5 hover:shadow-2xl hover:shadow-rose-900/10 hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[220px]">
                <!-- Frosted tape at top center (borderless) -->
                <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-16 h-5 bg-white/80 backdrop-blur-xs shadow-xs rounded-xs rotate-[-0.5deg] pointer-events-none"></div>
                <!-- Folded dog-ear corner -->
                <div class="absolute bottom-0 right-0 w-6 h-6 bg-gradient-to-tl from-rose-300/40 to-transparent rounded-br-[28px] pointer-events-none"></div>

                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-800/70">Store Catalog</span>
                    <h3 class="font-display font-extrabold text-2xl text-slate-900 mt-1">Products</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-700 font-medium">
                        <div class="text-slate-800 font-semibold">Curated items, sizes &amp; colors</div>
                        <div class="text-slate-600 text-[11px]">Pricing, discounts, and photo uploads</div>
                        <div class="text-slate-600 text-[11px]">Active in online store &amp; counter POS</div>
                    </div>
                </div>

                <div class="pt-5 flex items-center justify-between">
                    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-rose-950 group-hover:text-black group-hover:underline">
                        <span>{{ Auth::user()->isAdmin() ? 'Manage catalog' : 'View catalog' }}</span>
                        <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                    </a>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.products.create') }}" class="text-[11px] font-bold text-rose-800 hover:text-rose-950">
                            + Add Item
                        </a>
                    @endif
                </div>
            </div>

            <!-- Sticky Note 4: Pastel Purple - Inventory Valuation & Health -->
            <div class="group relative rounded-2xl rounded-br-[28px] p-6 bg-[#f3e8ff] shadow-lg shadow-purple-900/5 hover:shadow-2xl hover:shadow-purple-900/10 hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[220px]">
                <!-- Frosted tape at top center (borderless) -->
                <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-16 h-5 bg-white/80 backdrop-blur-xs shadow-xs rounded-xs rotate-[0.8deg] pointer-events-none"></div>
                <!-- Folded dog-ear corner -->
                <div class="absolute bottom-0 right-0 w-6 h-6 bg-gradient-to-tl from-purple-300/40 to-transparent rounded-br-[28px] pointer-events-none"></div>

                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-800/70">Inventory Health</span>
                    <h3 class="font-display font-extrabold text-2xl text-slate-900 mt-1">Inventory Valuation</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-700 font-medium">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                            <span><strong>₦{{ number_format($totalInventoryValuation, 0) }}</strong> total stock worth</span>
                        </div>
                        @if ($lowStockVariants->count() > 0)
                            <div class="text-rose-700 font-bold pl-3.5 text-[11px]">
                                &bull; {{ $lowStockVariants->count() }} variants low on stock
                            </div>
                        @else
                            <div class="text-emerald-700 font-semibold pl-3.5 text-[11px]">
                                &bull; All item quantities healthy
                            </div>
                        @endif
                        <div class="text-slate-600 pl-3.5 text-[11px]">Audit counts &amp; restock logs</div>
                    </div>
                </div>

                <div class="pt-5">
                    <a href="{{ route('admin.stock.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-purple-950 group-hover:text-black group-hover:underline">
                        <span>Check stock</span>
                        <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Sticky Note 5: Pastel Peach / Orange - Sales Reports -->
            <div class="group relative rounded-2xl rounded-br-[28px] p-6 bg-[#ffedd5] shadow-lg shadow-orange-900/5 hover:shadow-2xl hover:shadow-orange-900/10 hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[220px]">
                <!-- Frosted tape at top center (borderless) -->
                <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-16 h-5 bg-white/80 backdrop-blur-xs shadow-xs rounded-xs rotate-[-1deg] pointer-events-none"></div>
                <!-- Folded dog-ear corner -->
                <div class="absolute bottom-0 right-0 w-6 h-6 bg-gradient-to-tl from-orange-300/40 to-transparent rounded-br-[28px] pointer-events-none"></div>

                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-orange-800/70">Sales &amp; Finance</span>
                    <h3 class="font-display font-extrabold text-2xl text-slate-900 mt-1">Analytics</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-700 font-medium">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span>
                            <span><strong>₦{{ number_format($monthRevenue, 0) }}</strong> this month</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                            <span><strong>₦{{ number_format($weekRevenue, 0) }}</strong> rolling week</span>
                        </div>
                        <div class="text-slate-600 pl-3.5 text-[11px]">Download reports &amp; CSV spreadsheets</div>
                    </div>
                </div>

                <div class="pt-5">
                    <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-orange-950 group-hover:text-black group-hover:underline">
                        <span>View insights</span>
                        <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Sticky Note 6: Pastel Sky Blue - Scan Paper Daybook -->
            <div class="group relative rounded-2xl rounded-br-[28px] p-6 bg-[#e0f2fe] shadow-lg shadow-sky-900/5 hover:shadow-2xl hover:shadow-sky-900/10 hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[220px]">
                <!-- Frosted tape at top center (borderless) -->
                <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-16 h-5 bg-white/80 backdrop-blur-xs shadow-xs rounded-xs rotate-[0.5deg] pointer-events-none"></div>
                <!-- Folded dog-ear corner -->
                <div class="absolute bottom-0 right-0 w-6 h-6 bg-gradient-to-tl from-sky-300/40 to-transparent rounded-br-[28px] pointer-events-none"></div>

                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-sky-800/70">Paper Daybook</span>
                    <h3 class="font-display font-extrabold text-2xl text-slate-900 mt-1">Scan Receipts</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-700 font-medium">
                        <div class="text-slate-800 font-semibold">Snap photo of written paper ledger</div>
                        <div class="text-slate-600 text-[11px]">AI reads items, quantities &amp; prices</div>
                        <div class="text-slate-600 text-[11px]">Imports automatically into store records</div>
                    </div>
                </div>

                <div class="pt-5">
                    <a href="{{ route('admin.ocr.create') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-sky-950 group-hover:text-black group-hover:underline">
                        <span>Scan page</span>
                        <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Sticky Note 7: Pastel Lime - Staff & Payroll (Admin Only) -->
            @if(Auth::user()->isAdmin())
                <div class="group relative rounded-2xl rounded-br-[28px] p-6 bg-[#ecfccb] shadow-lg shadow-lime-900/5 hover:shadow-2xl hover:shadow-lime-900/10 hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[220px]">
                    <!-- Frosted tape at top center (borderless) -->
                    <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-16 h-5 bg-white/80 backdrop-blur-xs shadow-xs rounded-xs rotate-[-0.8deg] pointer-events-none"></div>
                    <!-- Folded dog-ear corner -->
                    <div class="absolute bottom-0 right-0 w-6 h-6 bg-gradient-to-tl from-lime-300/40 to-transparent rounded-br-[28px] pointer-events-none"></div>

                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-lime-800/70">Store Team</span>
                        <h3 class="font-display font-extrabold text-2xl text-slate-900 mt-1">Staff &amp; Payroll</h3>
                        <div class="space-y-1.5 mt-3 text-xs text-slate-700 font-medium">
                            <div class="text-slate-800 font-semibold">Cashiers, attendants &amp; sales staff</div>
                            <div class="text-slate-600 text-[11px]">Sales commissions &amp; monthly salary sheets</div>
                            <div class="text-slate-600 text-[11px]">Track attendance &amp; roles</div>
                        </div>
                    </div>

                    <div class="pt-5 flex items-center justify-between">
                        <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-lime-950 group-hover:text-black group-hover:underline">
                            <span>Manage team</span>
                            <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                        </a>
                        <a href="{{ route('admin.payroll.index') }}" class="text-[11px] font-bold text-lime-800 hover:text-lime-950">
                            Payroll &rarr;
                        </a>
                    </div>
                </div>

                <!-- Sticky Note 8: Pastel Indigo - Store Admin Accounts & Subscriptions (Admin Only) -->
                <div class="group relative rounded-2xl rounded-br-[28px] p-6 bg-[#e0e7ff] shadow-lg shadow-indigo-900/5 hover:shadow-2xl hover:shadow-indigo-900/10 hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[220px]">
                    <!-- Frosted tape at top center (borderless) -->
                    <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-16 h-5 bg-white/80 backdrop-blur-xs shadow-xs rounded-xs rotate-[0.6deg] pointer-events-none"></div>
                    <!-- Folded dog-ear corner -->
                    <div class="absolute bottom-0 right-0 w-6 h-6 bg-gradient-to-tl from-indigo-300/40 to-transparent rounded-br-[28px] pointer-events-none"></div>

                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-800/70">Store Admins</span>
                        <h3 class="font-display font-extrabold text-2xl text-slate-900 mt-1">Users &amp; Plans</h3>
                        <div class="space-y-1.5 mt-3 text-xs text-slate-700 font-medium">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span><strong>{{ $subscribedUsersCount }}</strong> Subscribed Users</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span><strong>{{ $freeUsersCount }}</strong> Free / Trial Users</span>
                            </div>
                            <div class="text-slate-600 pl-3.5 text-[11px]">{{ $totalAdminUsersCount }} total store admin accounts</div>
                        </div>
                    </div>

                    <div class="pt-5 flex items-center justify-between">
                        <a href="{{ route('admin.stores.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-indigo-950 group-hover:text-black group-hover:underline">
                            <span>Manage admins</span>
                            <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                        </a>
                        <a href="#stores-section" class="text-[11px] font-bold text-indigo-800 hover:text-indigo-950">
                            View accounts &darr;
                        </a>
                    </div>
                </div>
            @endif

        </div>

        <!-- Store Admins Directory Section (Admin Only - Staff & Customers nested under each Admin) -->
        @if(Auth::user()->isAdmin())
            <div id="stores-section" x-data="{ activeTier: 'all' }" class="bg-white rounded-2xl p-5 sm:p-7 shadow-xl shadow-slate-900/5 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-base text-slate-900">Store Admin Accounts</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 uppercase">
                                {{ $subscribedUsersCount }} Subscribed
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 uppercase">
                                {{ $freeUsersCount }} Free
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Primary merchant users on the platform. Staff members and customers are organized directly under each store admin.</p>
                    </div>

                    <!-- Quick Segment Tabs -->
                    <div class="flex items-center gap-1.5 overflow-x-auto">
                        <button
                            type="button"
                            @click="activeTier = 'all'"
                            :class="activeTier === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap cursor-pointer"
                        >
                            All Admins ({{ $totalAdminUsersCount }})
                        </button>
                        <button
                            type="button"
                            @click="activeTier = 'subscribed'"
                            :class="activeTier === 'subscribed' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'"
                            class="px-3 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap cursor-pointer flex items-center gap-1"
                        >
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            <span>Subscribed ({{ $subscribedUsersCount }})</span>
                        </button>
                        <button
                            type="button"
                            @click="activeTier = 'free'"
                            :class="activeTier === 'free' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'"
                            class="px-3 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap cursor-pointer flex items-center gap-1"
                        >
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            <span>Free Users ({{ $freeUsersCount }})</span>
                        </button>

                        <a href="{{ route('admin.stores.index') }}" class="px-3 py-1.5 rounded-full text-xs font-bold text-indigo-600 hover:text-indigo-800 transition whitespace-nowrap">
                            Full Directory &rarr;
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-extrabold text-[10px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Store Admin User</th>
                                <th class="px-3 py-3">Store &amp; Subdomain</th>
                                <th class="px-3 py-3">Subscription Tier</th>
                                <th class="px-3 py-3">Team &amp; Customers</th>
                                <th class="px-3 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($storeAdmins as $adminUser)
                                @php
                                    $tenant = $adminUser->tenant;
                                    $isSubscribed = $tenant && in_array($tenant->plan, ['starter', 'pro', 'enterprise'], true);
                                    $tierType = $isSubscribed ? 'subscribed' : 'free';
                                    $staffList = $tenant?->users->where('role', 'staff') ?? collect();
                                    $customerList = $tenant?->users->where('role', 'customer') ?? collect();
                                @endphp
                                <tr
                                    x-show="activeTier === 'all' || activeTier === '{{ $tierType }}'"
                                    x-data="{ showMembers: false }"
                                    class="hover:bg-slate-50/70 transition"
                                >
                                    <!-- Store Admin User -->
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                                                {{ strtoupper(substr($adminUser->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                                    <span>{{ $adminUser->name }}</span>
                                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold bg-indigo-50 text-indigo-700 uppercase">
                                                        Store Admin
                                                    </span>
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-mono">
                                                    {{ $adminUser->email }}
                                                    @if($adminUser->phone)
                                                        &bull; {{ $adminUser->phone }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Store & Subdomain -->
                                    <td class="px-3 py-3.5">
                                        <div class="font-bold text-slate-800">{{ $tenant?->name ?? 'No store attached' }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">
                                            {{ $tenant?->slug ?? 'local' }} &bull; {{ $tenant?->subdomain ? $tenant->subdomain . '.shopmanager.test' : 'apex' }}
                                        </div>
                                    </td>

                                    <!-- Subscription Tier -->
                                    <td class="px-3 py-3.5">
                                        @if ($isSubscribed)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Subscribed &bull; {{ $tenant->plan }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Free Tier &bull; {{ $tenant?->plan ?? 'standard' }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Team & Customers Under This Admin -->
                                    <td class="px-3 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                                {{ $staffList->count() }} Staff
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                                {{ $customerList->count() }} Customers
                                            </span>
                                        </div>

                                        <!-- Expandable Drawer for Staff and Customers -->
                                        @if($staffList->isNotEmpty() || $customerList->isNotEmpty())
                                            <div class="mt-1">
                                                <button
                                                    type="button"
                                                    @click="showMembers = !showMembers"
                                                    class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 transition cursor-pointer flex items-center gap-0.5"
                                                >
                                                    <span x-text="showMembers ? 'Hide accounts ▲' : 'View under this admin ▼'"></span>
                                                </button>

                                                <div
                                                    x-show="showMembers"
                                                    x-collapse
                                                    class="mt-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2 text-[11px]"
                                                    style="display: none;"
                                                >
                                                    @if($staffList->isNotEmpty())
                                                        <div>
                                                            <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">
                                                                Staff Tills Under This Admin:
                                                            </div>
                                                            <div class="space-y-1">
                                                                @foreach($staffList as $staffUser)
                                                                    <div class="flex items-center justify-between gap-2 p-1.5 rounded-lg bg-white border border-slate-100 shadow-2xs">
                                                                        <div class="truncate">
                                                                            <strong class="text-slate-800">{{ $staffUser->name }}</strong>
                                                                            <span class="text-slate-400 font-mono text-[10px]">({{ $staffUser->email }})</span>
                                                                        </div>
                                                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700">Cashier</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if($customerList->isNotEmpty())
                                                        <div>
                                                            <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">
                                                                Customers Under This Admin:
                                                            </div>
                                                            <div class="space-y-1">
                                                                @foreach($customerList->take(4) as $custUser)
                                                                    <div class="flex items-center justify-between gap-2 p-1 rounded-md bg-white text-slate-600">
                                                                        <span class="truncate">{{ $custUser->name }}</span>
                                                                        <span class="text-slate-400 font-mono text-[10px]">{{ $custUser->email }}</span>
                                                                    </div>
                                                                @endforeach
                                                                @if($customerList->count() > 4)
                                                                    <div class="text-[10px] text-slate-400 italic pl-1">
                                                                        + {{ $customerList->count() - 4 }} more shoppers
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td class="px-3 py-3.5">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ ($tenant?->status ?? 'active') === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                            {{ $tenant?->status ?? 'active' }}
                                        </span>
                                    </td>

                                    <!-- Action -->
                                    <td class="px-4 py-3.5 text-right">
                                        @if($tenant)
                                            <a
                                                href="{{ url('/admin?tenant=' . $tenant->slug) }}"
                                                class="px-2.5 py-1 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-bold transition shadow-xs inline-flex items-center gap-1"
                                            >
                                                <span>Switch &nearr;</span>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        No store admin accounts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Gentle Low Stock Notice (If Any Items Need Restock - Borderless with Soft Shadow) -->
        @if ($lowStockVariants->count() > 0)
            <div class="bg-amber-50/90 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg shadow-amber-950/5">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    <div>
                        <div class="font-bold text-sm text-slate-900">
                            {{ $lowStockVariants->count() }} {{ Str::plural('item', $lowStockVariants->count()) }} running low on stock
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            @foreach($lowStockVariants->take(3) as $v)
                                {{ $v->product->name }} ({{ $v->size ?? $v->color ?? 'variant' }}: {{ $v->stock_quantity }} left){{ !$loop->last ? ' &bull; ' : '' }}
                            @endforeach
                            @if($lowStockVariants->count() > 3)
                                and {{ $lowStockVariants->count() - 3 }} more...
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.stock.index') }}" class="px-4 py-2 rounded-xl bg-white text-amber-900 hover:bg-amber-100 text-xs font-bold transition shadow-md shadow-amber-950/5 shrink-0 self-start sm:self-center">
                    Review Stock &rarr;
                </a>
            </div>
        @endif

        <!-- Recent Customer Purchases (Borderless with Soft Shadow) -->
        <div class="bg-white rounded-2xl p-5 sm:p-7 shadow-xl shadow-slate-900/5 space-y-4">
            <div class="flex items-center justify-between pb-3">
                <div>
                    <h3 class="font-bold text-base text-slate-900">Recent Customer Purchases</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Latest sales made at the counter and online</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                    View all ({{ $totalOrdersCount }}) &rarr;
                </a>
            </div>

            @if ($recentOrders->count() > 0)
                <div class="divide-y divide-slate-100/80">
                    @foreach ($recentOrders as $order)
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition">
                                        #{{ $order->order_number }}
                                    </a>
                                    <span class="text-slate-300">&bull;</span>
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
                    <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-2 shadow-xs">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div class="text-sm font-semibold text-slate-700">No orders recorded yet</div>
                    <p class="text-xs text-slate-400 mt-1">When transactions occur, they will display here in real time.</p>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
