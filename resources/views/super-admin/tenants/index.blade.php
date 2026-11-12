<x-super-admin-layout>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tenant Stores Directory</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage stores, merchant owner accounts, and subscription tiers</p>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('super-admin.users.index') }}"
                class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-xs flex items-center gap-1.5"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Platform Users View &rarr;</span>
            </a>

            <a
                href="{{ route('super-admin.tenants.create') }}"
                class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition shadow-xs flex items-center gap-1.5 shrink-0"
            >
                <span>+ Provision Store</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">All Stores &amp; Merchants</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalStoresCount) }}</div>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Total live tenant businesses</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider block">Subscribed Users / Stores</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($subscribedCount) }}</div>
                <span class="text-[11px] text-emerald-600 font-medium mt-0.5 block">Paid plans (Starter, Pro, Enterprise)</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider block">Free &amp; Trial Users</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($freeCount) }}</div>
                <span class="text-[11px] text-amber-600 font-medium mt-0.5 block">Standard free tier &amp; 14-day trials</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </div>
        </div>
    </div>

    <!-- Segment Filter Tab Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        @php
            $currentTier = request('tier', 'all');
        @endphp

        <a
            href="{{ route('super-admin.tenants.index', array_merge(request()->except(['page', 'tier']), ['tier' => 'all'])) }}"
            class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap {{ $currentTier === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80' }}"
        >
            All Stores &amp; Users ({{ $totalStoresCount }})
        </a>

        <a
            href="{{ route('super-admin.tenants.index', array_merge(request()->except(['page', 'tier']), ['tier' => 'subscribed'])) }}"
            class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap flex items-center gap-1.5 {{ $currentTier === 'subscribed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-emerald-700 hover:bg-emerald-50 border border-emerald-200' }}"
        >
            <span class="w-1.5 h-1.5 rounded-full {{ $currentTier === 'subscribed' ? 'bg-white' : 'bg-emerald-500' }}"></span>
            <span>Subscribed Users ({{ $subscribedCount }})</span>
        </a>

        <a
            href="{{ route('super-admin.tenants.index', array_merge(request()->except(['page', 'tier']), ['tier' => 'free'])) }}"
            class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap flex items-center gap-1.5 {{ $currentTier === 'free' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-amber-700 hover:bg-amber-50 border border-amber-200' }}"
        >
            <span class="w-1.5 h-1.5 rounded-full {{ $currentTier === 'free' ? 'bg-white' : 'bg-amber-500' }}"></span>
            <span>Free &amp; Trial Users ({{ $freeCount }})</span>
        </a>
    </div>

    <!-- Filters & Search Bar (White & Clean) -->
    <form method="GET" action="{{ route('super-admin.tenants.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col md:flex-row items-center gap-3">
        @if (request('tier'))
            <input type="hidden" name="tier" value="{{ request('tier') }}" />
        @endif

        <div class="relative flex-1 w-full">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by store name, slug, merchant name, or owner email..."
                class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
            />
        </div>

        <div class="flex items-center gap-2.5 w-full md:w-auto">
            <select
                name="plan"
                class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-600"
                onchange="this.form.submit()"
            >
                <option value="all" {{ request('plan') === 'all' || !request('plan') ? 'selected' : '' }}>All Plans</option>
                <option value="starter" {{ request('plan') === 'starter' ? 'selected' : '' }}>Starter (Paid)</option>
                <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro (Paid)</option>
                <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise (Paid)</option>
                <option value="standard" {{ request('plan') === 'standard' ? 'selected' : '' }}>Standard / Free</option>
            </select>

            <select
                name="status"
                class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-600"
                onchange="this.form.submit()"
            >
                <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>

            <button
                type="submit"
                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition"
            >
                Filter
            </button>

            @if (request()->hasAny(['search', 'plan', 'status', 'tier']))
                <a
                    href="{{ route('super-admin.tenants.index') }}"
                    class="px-3 py-2 rounded-xl text-xs text-slate-500 hover:text-slate-800 transition"
                >
                    Reset
                </a>
            @endif
        </div>
    </form>

    <!-- Tenants Table (Spacious White Container) -->
    <div class="rounded-2xl bg-white border border-slate-200/90 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Store &amp; Merchant Owner</th>
                        <th class="px-4 py-4">Slug / Subdomain</th>
                        <th class="px-4 py-4">Subscription &amp; Plan</th>
                        <th class="px-4 py-4">Catalog &amp; Outlets</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($tenants as $tenant)
                        @php
                            $owner = $tenant->users->firstWhere('role', 'admin') ?? $tenant->users->first();
                            $isSubscribed = in_array($tenant->plan, ['starter', 'pro', 'enterprise'], true);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Store & Merchant Owner -->
                            <td class="px-6 py-4.5">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0 mt-0.5">
                                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition text-sm block">
                                            {{ $tenant->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-400 truncate max-w-[240px]">
                                            {{ $tenant->setting('branding.tagline', 'Retail Workspace') }}
                                        </div>
                                        @if ($owner)
                                            <div class="flex items-center gap-1.5 mt-1 text-[11px] text-slate-600">
                                                <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span class="font-semibold text-slate-800">{{ $owner->name }}</span>
                                                <span class="text-slate-400 font-mono text-[10px]">&bull; {{ $owner->email }}</span>
                                            </div>
                                        @else
                                            <div class="text-[11px] text-slate-400 italic mt-1">No merchant owner attached</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Slug / Subdomain -->
                            <td class="px-4 py-4.5">
                                <div class="font-mono text-slate-900 font-semibold">{{ $tenant->slug }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">
                                    {{ $tenant->domain ?: ($tenant->subdomain ? $tenant->subdomain . '.shopmanager.test' : 'local') }}
                                </div>
                            </td>

                            <!-- Subscription & Plan -->
                            <td class="px-4 py-4.5 space-y-1.5">
                                @if ($isSubscribed)
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Subscribed &bull; {{ $tenant->plan }}</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Free Tier &bull; {{ $tenant->plan ?? 'standard' }}</span>
                                    </div>
                                @endif

                                <div class="flex items-center gap-1 text-[10px] flex-wrap">
                                    @if ($tenant->hasFeature('storefront'))
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-medium">Storefront</span>
                                    @endif
                                    @if ($tenant->hasFeature('payroll'))
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-medium">Payroll</span>
                                    @endif
                                    @if ($tenant->hasFeature('ocr'))
                                        <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-medium">OCR</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Catalog & Outlets -->
                            <td class="px-4 py-4.5">
                                <div class="font-semibold text-slate-900">{{ $tenant->products_count }} Products</div>
                                <div class="text-[11px] text-slate-500">
                                    {{ $tenant->shop_locations_count }} Outlets &bull; {{ $tenant->staff_count }} Staff
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-4.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $tenant->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $tenant->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ $tenant->status }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4.5 text-right">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <form method="POST" action="{{ route('super-admin.tenants.impersonate-admin', $tenant) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 text-xs font-semibold transition flex items-center gap-1 cursor-pointer"
                                            title="Login directly as Store Admin"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            <span>Admin &nearr;</span>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('super-admin.tenants.impersonate-staff', $tenant) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 text-xs font-semibold transition flex items-center gap-1 cursor-pointer"
                                            title="Login directly as Staff / POS Till"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                            <span>Staff Till &nearr;</span>
                                        </button>
                                    </form>

                                    <a
                                        href="{{ route('super-admin.tenants.show', $tenant) }}"
                                        class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition"
                                    >
                                        Inspect
                                    </a>

                                    <form method="POST" action="{{ route('super-admin.tenants.toggle-status', $tenant) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1.5 rounded-xl border text-xs font-semibold transition cursor-pointer {{ $tenant->status === 'active' ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}"
                                            title="{{ $tenant->status === 'active' ? 'Suspend Store' : 'Activate Store' }}"
                                        >
                                            {{ $tenant->status === 'active' ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                No stores or merchants found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tenants->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
</x-super-admin-layout>
