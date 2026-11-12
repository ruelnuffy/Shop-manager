<x-super-admin-layout>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Platform Users &amp; Merchants Directory</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage all registered store owners, staff cashiers, and subscribed accounts</p>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('super-admin.tenants.index') }}"
                class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-xs flex items-center gap-1.5"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Stores View</span>
            </a>

            <a
                href="{{ route('super-admin.tenants.create') }}"
                class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition shadow-xs flex items-center gap-1.5 shrink-0"
            >
                <span>+ Provision Store</span>
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Users</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalUsersCount) }}</div>
                <span class="text-[11px] text-slate-400 mt-0.5 block">All platform accounts</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider block">Subscribed Users</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($subscribedUsersCount) }}</div>
                <span class="text-[11px] text-emerald-600 font-medium mt-0.5 block">Active paid store plans</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider block">Free &amp; Trial Users</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($freeUsersCount) }}</div>
                <span class="text-[11px] text-amber-600 font-medium mt-0.5 block">Standard free tier</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-indigo-600 uppercase tracking-wider block">Staff &amp; Cashiers</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($staffCount) }}</div>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $adminCount }} store admins</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
        </div>
    </div>

    <!-- Segment Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        @php
            $currentTier = request('tier', 'all');
            $currentRole = request('role', 'all');
        @endphp

        <a
            href="{{ route('super-admin.users.index', array_merge(request()->except(['page', 'tier']), ['tier' => 'all'])) }}"
            class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap {{ $currentTier === 'all' && $currentRole === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80' }}"
        >
            All Users ({{ $totalUsersCount }})
        </a>

        <a
            href="{{ route('super-admin.users.index', array_merge(request()->except(['page', 'tier']), ['tier' => 'subscribed'])) }}"
            class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap flex items-center gap-1.5 {{ $currentTier === 'subscribed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-emerald-700 hover:bg-emerald-50 border border-emerald-200' }}"
        >
            <span class="w-1.5 h-1.5 rounded-full {{ $currentTier === 'subscribed' ? 'bg-white' : 'bg-emerald-500' }}"></span>
            <span>Subscribed Users ({{ $subscribedUsersCount }})</span>
        </a>

        <a
            href="{{ route('super-admin.users.index', array_merge(request()->except(['page', 'tier']), ['tier' => 'free'])) }}"
            class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap flex items-center gap-1.5 {{ $currentTier === 'free' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-amber-700 hover:bg-amber-50 border border-amber-200' }}"
        >
            <span class="w-1.5 h-1.5 rounded-full {{ $currentTier === 'free' ? 'bg-white' : 'bg-amber-500' }}"></span>
            <span>Free &amp; Trial Users ({{ $freeUsersCount }})</span>
        </a>

        <a
            href="{{ route('super-admin.users.index', array_merge(request()->except(['page', 'role']), ['role' => 'admin'])) }}"
            class="px-3.5 py-2 rounded-xl text-xs font-medium transition whitespace-nowrap {{ $currentRole === 'admin' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}"
        >
            Store Admins ({{ $adminCount }})
        </a>

        <a
            href="{{ route('super-admin.users.index', array_merge(request()->except(['page', 'role']), ['role' => 'staff'])) }}"
            class="px-3.5 py-2 rounded-xl text-xs font-medium transition whitespace-nowrap {{ $currentRole === 'staff' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}"
        >
            Staff ({{ $staffCount }})
        </a>

        <a
            href="{{ route('super-admin.users.index', array_merge(request()->except(['page', 'role']), ['role' => 'customer'])) }}"
            class="px-3.5 py-2 rounded-xl text-xs font-medium transition whitespace-nowrap {{ $currentRole === 'customer' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}"
        >
            Shoppers ({{ $customerCount }})
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('super-admin.users.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col md:flex-row items-center gap-3">
        <input type="hidden" name="tier" value="{{ request('tier', 'all') }}">

        <div class="relative flex-1 w-full">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search users by name, email, phone, or store name..."
                class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
            />
        </div>

        <div class="flex items-center gap-2.5 w-full md:w-auto">
            <select
                name="role"
                class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-600"
                onchange="this.form.submit()"
            >
                <option value="all" {{ request('role') === 'all' || !request('role') ? 'selected' : '' }}>All Roles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Store Admin</option>
                <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff / Cashier</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
            </select>

            <button
                type="submit"
                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition"
            >
                Filter
            </button>

            @if(request()->hasAny(['search', 'role', 'tier']))
                <a href="{{ route('super-admin.users.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 transition" title="Clear filters">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            @endif
        </div>
    </form>

    <!-- Users Table -->
    <div class="rounded-2xl bg-white border border-slate-200/90 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">User &amp; Contact</th>
                        <th class="px-4 py-4">Platform Role</th>
                        <th class="px-4 py-4">Store / Business</th>
                        <th class="px-4 py-4">Subscription Status</th>
                        <th class="px-4 py-4">Joined Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($users as $user)
                        @php
                            $isSubscribed = $user->tenant && in_array($user->tenant->plan, ['starter', 'pro', 'enterprise']);
                            $staffList = $user->tenant?->users->where('role', 'staff') ?? collect();
                            $customerList = $user->tenant?->users->where('role', 'customer') ?? collect();
                        @endphp
                        <tr x-data="{ showTeam: false }" class="hover:bg-slate-50/80 transition">
                            <!-- User & Contact -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl {{ $user->isSuperAdmin() ? 'bg-indigo-600 text-white' : ($user->isAdmin() ? 'bg-indigo-100 text-indigo-700' : ($user->isStaff() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700')) }} flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if ($user->isSuperAdmin())
                                                <span class="px-1.5 py-0.2 rounded text-[9px] bg-rose-100 text-rose-700 font-extrabold uppercase">Platform Super</span>
                                            @elseif ($user->isAdmin())
                                                <span class="px-1.5 py-0.2 rounded text-[9px] bg-indigo-50 text-indigo-700 font-extrabold uppercase">Store Admin</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-mono">
                                            {{ $user->email }}
                                            @if($user->phone)
                                                &bull; <span class="text-slate-400 font-mono">{{ $user->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($staffList->isNotEmpty() || $customerList->isNotEmpty())
                                    <div class="mt-2 pl-12">
                                        <button
                                            type="button"
                                            @click="showTeam = !showTeam"
                                            class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 transition cursor-pointer flex items-center gap-1"
                                        >
                                            <span x-text="showTeam ? 'Hide Team &amp; Customers ▲' : 'View Staff ({{ $staffList->count() }}) &amp; Customers ({{ $customerList->count() }}) ▼'"></span>
                                        </button>

                                        <div
                                            x-show="showTeam"
                                            x-collapse
                                            class="mt-2 p-3 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2 text-[11px]"
                                            style="display: none;"
                                        >
                                            @if($staffList->isNotEmpty())
                                                <div>
                                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                                                        Staff Tills Under This Admin:
                                                    </div>
                                                    <div class="space-y-1">
                                                        @foreach($staffList as $stf)
                                                            <div class="flex items-center justify-between gap-2 p-1.5 rounded-lg bg-white border border-slate-100">
                                                                <span class="font-semibold text-slate-800">{{ $stf->name }} <span class="font-mono text-[10px] text-slate-400">({{ $stf->email }})</span></span>
                                                                <form method="POST" action="{{ route('super-admin.users.impersonate', $stf) }}" class="inline">
                                                                    @csrf
                                                                    <button type="submit" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-800">Till &nearr;</button>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if($customerList->isNotEmpty())
                                                <div>
                                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                                                        Customers Under This Admin ({{ $customerList->count() }}):
                                                    </div>
                                                    <div class="space-y-1 max-h-32 overflow-y-auto">
                                                        @foreach($customerList->take(6) as $cst)
                                                            <div class="flex items-center justify-between text-slate-600 p-1 bg-white rounded">
                                                                <span>{{ $cst->name }}</span>
                                                                <span class="text-slate-400 font-mono text-[10px]">{{ $cst->email }}</span>
                                                            </div>
                                                        @endforeach
                                                        @if($customerList->count() > 6)
                                                            <div class="text-[10px] text-slate-400 italic">
                                                                + {{ $customerList->count() - 6 }} more customers
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- Role -->
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $user->role === 'super_admin' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($user->role === 'admin' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : ($user->role === 'staff' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200')) }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>

                            <!-- Store / Business -->
                            <td class="px-4 py-4">
                                @if ($user->tenant)
                                    <div>
                                        <a href="{{ route('super-admin.tenants.show', $user->tenant) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition">
                                            {{ $user->tenant->name }}
                                        </a>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $user->tenant->slug }}</div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">No Store (Platform Level)</span>
                                @endif
                            </td>

                            <!-- Subscription Status -->
                            <td class="px-4 py-4">
                                @if ($isSubscribed)
                                    <div class="space-y-0.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Subscribed
                                        </span>
                                        <div class="text-[11px] font-semibold text-slate-700 uppercase">
                                            {{ $user->tenant->plan }} Plan
                                        </div>
                                    </div>
                                @elseif ($user->tenant)
                                    <div class="space-y-0.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Free User
                                        </span>
                                        <div class="text-[11px] text-slate-400">
                                            14-Day Free Trial
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400">Platform Account</span>
                                @endif
                            </td>

                            <!-- Joined Date -->
                            <td class="px-4 py-4 text-[11px] text-slate-500">
                                {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                                <div class="text-[10px] text-slate-400">{{ $user->created_at ? $user->created_at->diffForHumans() : '' }}</div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                @if ($user->id !== Auth::id())
                                    <form method="POST" action="{{ route('super-admin.users.impersonate', $user) }}" class="inline">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 text-xs font-semibold transition inline-flex items-center gap-1 cursor-pointer"
                                            title="Direct login as {{ $user->name }}"
                                        >
                                            <span>Login &nearr;</span>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Current User</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                No users found matching your search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-super-admin-layout>
