<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <h1 class="font-display font-extrabold text-lg text-slate-900 tracking-tight">
                Store Admin Users &amp; Subscriptions
            </h1>
            <span class="text-xs text-slate-400 font-medium hidden sm:inline">&bull; Merchants &amp; Teams</span>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Top Header & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-0.5">Platform Merchants Directory</span>
                <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    Store Admin Accounts
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Store administrators are the primary accounts on the platform. Staff cashiers and customer shoppers are organized directly under each admin.
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
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Store Admin Users</span>
                    <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalAdminUsersCount) }}</div>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Total merchant owner accounts</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-lg shadow-xs">
                    👤
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-lg shadow-emerald-950/5 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-emerald-600 uppercase tracking-wider block">Subscribed Users</span>
                    <div class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($subscribedCount) }}</div>
                    <span class="text-[11px] text-emerald-600 font-medium mt-0.5 block">Active paid plans (Starter, Pro, Enterprise)</span>
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
                All Store Admins ({{ $totalAdminUsersCount }})
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
                    placeholder="Search by store admin name, email, phone, or store name..."
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

        <!-- Store Admins Directory Table (Staff & Customers nested under each Admin) -->
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-900/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-extrabold text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Store Admin User</th>
                            <th class="px-4 py-4">Store &amp; Subdomain</th>
                            <th class="px-4 py-4">Subscription Tier</th>
                            <th class="px-4 py-4">Team &amp; Customers</th>
                            <th class="px-4 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Switch / Open</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($storeAdmins as $adminUser)
                            @php
                                $tenant = $adminUser->tenant;
                                $isSubscribed = $tenant && in_array($tenant->plan, ['starter', 'pro', 'enterprise'], true);
                                $staffList = $tenant?->users->where('role', 'staff') ?? collect();
                                $customerList = $tenant?->users->where('role', 'customer') ?? collect();
                            @endphp
                            <tr
                                x-data="{ showMembers: false }"
                                class="hover:bg-slate-50/70 transition"
                            >
                                <!-- Store Admin User -->
                                <td class="px-6 py-4.5">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-extrabold text-xs shadow-xs shrink-0">
                                            {{ strtoupper(substr($adminUser->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-slate-900 text-sm flex items-center gap-1.5">
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
                                <td class="px-4 py-4.5">
                                    <div class="font-bold text-slate-900 text-sm">{{ $tenant?->name ?? 'No store attached' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">
                                        {{ $tenant?->slug ?? 'local' }} &bull; {{ $tenant?->subdomain ? $tenant->subdomain . '.shopmanager.test' : 'apex' }}
                                    </div>
                                </td>

                                <!-- Subscription & Plan -->
                                <td class="px-4 py-4.5 space-y-1">
                                    @if ($isSubscribed)
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Subscribed &bull; {{ $tenant->plan }}</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Free Tier &bull; {{ $tenant?->plan ?? 'standard' }}</span>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-1 text-[10px] text-slate-500">
                                        @if ($tenant?->hasFeature('storefront'))
                                            <span class="px-1.5 py-0.2 rounded bg-slate-100 font-medium">Storefront</span>
                                        @endif
                                        @if ($tenant?->hasFeature('payroll'))
                                            <span class="px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 font-medium">Payroll</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Staff & Customers Under This Admin -->
                                <td class="px-4 py-4.5">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                            {{ $staffList->count() }} Staff
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                            {{ $customerList->count() }} Customers
                                        </span>
                                    </div>

                                    @if($staffList->isNotEmpty() || $customerList->isNotEmpty())
                                        <div class="mt-1.5">
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
                                                class="mt-2 p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-2.5 text-[11px]"
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
                                                            @foreach($customerList->take(5) as $custUser)
                                                                <div class="flex items-center justify-between gap-2 p-1.5 rounded-md bg-white text-slate-600">
                                                                    <span class="truncate font-medium">{{ $custUser->name }}</span>
                                                                    <span class="text-slate-400 font-mono text-[10px]">{{ $custUser->email }}</span>
                                                                </div>
                                                            @endforeach
                                                            @if($customerList->count() > 5)
                                                                <div class="text-[10px] text-slate-400 italic pl-1">
                                                                    + {{ $customerList->count() - 5 }} more registered shoppers
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
                                <td class="px-4 py-4.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ ($tenant?->status ?? 'active') === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ ($tenant?->status ?? 'active') === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        {{ $tenant?->status ?? 'active' }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4.5 text-right">
                                    @if($tenant)
                                        <a
                                            href="{{ url('/admin?tenant=' . $tenant->slug) }}"
                                            class="px-3 py-1.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs inline-flex items-center gap-1"
                                            title="Open this store's console"
                                        >
                                            <span>Switch &nearr;</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    No store admin accounts found matching your filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($storeAdmins->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    {{ $storeAdmins->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
