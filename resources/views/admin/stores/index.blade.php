<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <h1 class="font-display font-extrabold text-lg text-slate-900 tracking-tight">
                Stores &amp; User Subscriptions
            </h1>
            <span class="text-xs text-slate-400 font-medium hidden sm:inline">&bull; Merchant Directory</span>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Top Header & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-0.5">Platform Stores Directory</span>
                <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    Merchant Stores Listed as Users
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Manage stores, merchant user accounts, and track subscribed vs free tier merchants.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="px-4 py-2 rounded-full bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-md shadow-slate-900/5 flex items-center gap-1.5"
                >
                    <span>&larr; Overview</span>
                </a>
            </div>
        </div>

        <!-- KPI Metric Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl p-5 shadow-lg shadow-slate-900/5 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">All Stores &amp; Users</span>
                    <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalStoresCount) }}</div>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Total merchant accounts</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-lg shadow-xs">
                    🏪
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-lg shadow-emerald-950/5 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-emerald-600 uppercase tracking-wider block">Subscribed Users</span>
                    <div class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($subscribedCount) }}</div>
                    <span class="text-[11px] text-emerald-600 font-medium mt-0.5 block">Starter, Pro, &amp; Enterprise</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg shadow-xs">
                    💳
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-lg shadow-amber-950/5 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-amber-600 uppercase tracking-wider block">Free &amp; Trial Users</span>
                    <div class="text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($freeCount) }}</div>
                    <span class="text-[11px] text-amber-600 font-medium mt-0.5 block">Standard free &amp; 14-day trials</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shadow-xs">
                    🌱
                </div>
            </div>
        </div>

        <!-- Segment Filter Tab Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            @php
                $currentTier = request('tier', 'all');
            @endphp

            <a
                href="{{ route('admin.stores.index', array_merge(request()->except(['page', 'tier']), ['tier' => 'all'])) }}"
                class="px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap {{ $currentTier === 'all' ? 'bg-slate-900 text-white shadow-md shadow-slate-950/15' : 'bg-white text-slate-700 hover:bg-slate-100 shadow-sm' }}"
            >
                All Stores &amp; Users ({{ $totalStoresCount }})
            </a>

            <a
                href="{{ route('admin.stores.index', array_merge(request()->except(['page', 'tier']), ['tier' => 'subscribed'])) }}"
                class="px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap flex items-center gap-1.5 {{ $currentTier === 'subscribed' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-950/15' : 'bg-white text-emerald-700 hover:bg-emerald-50 shadow-sm' }}"
            >
                <span class="w-1.5 h-1.5 rounded-full {{ $currentTier === 'subscribed' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                <span>Subscribed Users ({{ $subscribedCount }})</span>
            </a>

            <a
                href="{{ route('admin.stores.index', array_merge(request()->except(['page', 'tier']), ['tier' => 'free'])) }}"
                class="px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap flex items-center gap-1.5 {{ $currentTier === 'free' ? 'bg-amber-600 text-white shadow-md shadow-amber-950/15' : 'bg-white text-amber-700 hover:bg-amber-50 shadow-sm' }}"
            >
                <span class="w-1.5 h-1.5 rounded-full {{ $currentTier === 'free' ? 'bg-white' : 'bg-amber-500' }}"></span>
                <span>Free &amp; Trial Users ({{ $freeCount }})</span>
            </a>
        </div>

        <!-- Search & Filter Controls -->
        <form method="GET" action="{{ route('admin.stores.index') }}" class="bg-white rounded-2xl p-4 shadow-lg shadow-slate-900/5 flex flex-col md:flex-row items-center gap-3">
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
                    placeholder="Search by store name, slug, merchant owner, or email..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border-none text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition"
                />
            </div>

            <div class="flex items-center gap-2.5 w-full md:w-auto">
                <select
                    name="plan"
                    class="px-3 py-2.5 rounded-xl bg-slate-50 border-none text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900"
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
                    class="px-3 py-2.5 rounded-xl bg-slate-50 border-none text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900"
                    onchange="this.form.submit()"
                >
                    <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <button
                    type="submit"
                    class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs"
                >
                    Filter
                </button>

                @if (request()->hasAny(['search', 'plan', 'status', 'tier']))
                    <a
                        href="{{ route('admin.stores.index') }}"
                        class="px-3 py-2 rounded-xl text-xs text-slate-500 hover:text-slate-800 transition"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Stores as Users Table -->
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-900/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-extrabold text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Store &amp; Merchant Owner</th>
                            <th class="px-4 py-4">Domain / Slug</th>
                            <th class="px-4 py-4">Subscription &amp; Plan</th>
                            <th class="px-4 py-4">Catalog &amp; Team</th>
                            <th class="px-4 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Switch / Open</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($stores as $store)
                            @php
                                $owner = $store->users->firstWhere('role', 'admin') ?? $store->users->first();
                                $isSubscribed = in_array($store->plan, ['starter', 'pro', 'enterprise'], true);
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Store & Merchant Owner -->
                                <td class="px-6 py-4.5">
                                    <div class="flex items-start gap-3.5">
                                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-extrabold text-xs shadow-xs shrink-0 mt-0.5">
                                            {{ strtoupper(substr($store->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-slate-900 text-sm">
                                                {{ $store->name }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 truncate max-w-[240px]">
                                                {{ $store->setting('branding.tagline', 'Retail Workspace') }}
                                            </div>
                                            @if ($owner)
                                                <div class="flex items-center gap-1.5 mt-1 text-[11px] text-slate-600">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 shrink-0"></span>
                                                    <span class="font-bold text-slate-800">{{ $owner->name }}</span>
                                                    <span class="text-slate-400 font-mono text-[10px]">&bull; {{ $owner->email }}</span>
                                                </div>
                                            @else
                                                <div class="text-[11px] text-slate-400 italic mt-1">No admin attached</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Domain / Slug -->
                                <td class="px-4 py-4.5">
                                    <div class="font-mono text-slate-900 font-bold">{{ $store->slug }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">
                                        {{ $store->domain ?: ($store->subdomain ? $store->subdomain . '.shopmanager.test' : 'local') }}
                                    </div>
                                </td>

                                <!-- Subscription & Plan -->
                                <td class="px-4 py-4.5 space-y-1">
                                    @if ($isSubscribed)
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Subscribed &bull; {{ $store->plan }}</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Free Tier &bull; {{ $store->plan ?? 'standard' }}</span>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-1 text-[10px] text-slate-500">
                                        @if ($store->hasFeature('storefront'))
                                            <span class="px-1.5 py-0.2 rounded bg-slate-100 font-medium">Storefront</span>
                                        @endif
                                        @if ($store->hasFeature('payroll'))
                                            <span class="px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 font-medium">Payroll</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Catalog & Team -->
                                <td class="px-4 py-4.5">
                                    <div class="font-bold text-slate-900">{{ $store->products_count }} Products</div>
                                    <div class="text-[11px] text-slate-500">
                                        {{ $store->shop_locations_count }} Outlets &bull; {{ $store->staff_count }} Staff
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-4.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $store->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $store->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        {{ $store->status }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a
                                            href="{{ url('/admin?tenant=' . $store->slug) }}"
                                            class="px-3 py-1.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs inline-flex items-center gap-1"
                                            title="Open this store's console"
                                        >
                                            <span>Open &nearr;</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    No stores or merchants found matching your filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($stores->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    {{ $stores->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
