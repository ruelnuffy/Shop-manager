<x-super-admin-layout>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <span class="text-xs font-mono uppercase tracking-wider text-rose-400 font-bold">Store Directory</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1">Tenant Stores Directory</h1>
            <p class="text-xs text-slate-400 mt-1">Manage isolated tenant workspaces, feature entitlements, and operational status.</p>
        </div>

        <a
            href="{{ route('super-admin.tenants.create') }}"
            class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider transition shadow-md shadow-indigo-600/30 flex items-center gap-2 shrink-0"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>+ Provision Store</span>
        </a>
    </div>

    <!-- Filters & Search Bar -->
    <form method="GET" action="{{ route('super-admin.tenants.index') }}" class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 flex flex-col md:flex-row items-center gap-3">
        <div class="relative flex-1 w-full">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search store by name, slug, or domain..."
                class="w-full pl-10 pr-4 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
            />
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <select
                name="plan"
                class="px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-300 focus:outline-none focus:border-indigo-500"
                onchange="this.form.submit()"
            >
                <option value="all" {{ request('plan') === 'all' || !request('plan') ? 'selected' : '' }}>All Plans</option>
                <option value="starter" {{ request('plan') === 'starter' ? 'selected' : '' }}>Starter</option>
                <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                <option value="standard" {{ request('plan') === 'standard' ? 'selected' : '' }}>Standard</option>
            </select>

            <select
                name="status"
                class="px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-300 focus:outline-none focus:border-indigo-500"
                onchange="this.form.submit()"
            >
                <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>

            <button
                type="submit"
                class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition"
            >
                Filter
            </button>
        </div>
    </form>

    <!-- Tenants Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 border-b border-slate-800 text-slate-400 uppercase font-mono text-[10px] tracking-wider">
                    <tr>
                        <th class="px-5 py-4 font-bold">Store &amp; Branding</th>
                        <th class="px-4 py-4 font-bold">Slug / Subdomain</th>
                        <th class="px-4 py-4 font-bold">Plan &amp; Features</th>
                        <th class="px-4 py-4 font-bold">Catalog &amp; Outlets</th>
                        <th class="px-4 py-4 font-bold">Status</th>
                        <th class="px-5 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 text-slate-300">
                    @forelse ($tenants as $tenant)
                        <tr class="hover:bg-slate-800/40 transition">
                            <!-- Store & Branding -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="font-bold text-white hover:text-indigo-300 transition text-sm">
                                            {{ $tenant->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-400 truncate max-w-[200px]">
                                            {{ $tenant->setting('branding.tagline', 'Retail Workspace') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Slug / Subdomain -->
                            <td class="px-4 py-4">
                                <div class="font-mono text-slate-300 font-semibold">{{ $tenant->slug }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">
                                    {{ $tenant->domain ?: ($tenant->subdomain ? $tenant->subdomain . '.shopmanager' : 'local') }}
                                </div>
                            </td>

                            <!-- Plan & Features -->
                            <td class="px-4 py-4 space-y-1.5">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                                    {{ $tenant->plan }}
                                </span>
                                <div class="flex items-center gap-1.5 text-[10px]">
                                    @if ($tenant->hasFeature('storefront'))
                                        <span class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300" title="Web Storefront Active">Storefront</span>
                                    @endif
                                    @if ($tenant->hasFeature('payroll'))
                                        <span class="px-1.5 py-0.5 rounded bg-slate-800 text-emerald-400" title="Paystack Payroll Active">Payroll</span>
                                    @endif
                                    @if ($tenant->hasFeature('ocr'))
                                        <span class="px-1.5 py-0.5 rounded bg-slate-800 text-amber-400" title="OCR Ledgers Active">OCR</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Catalog & Outlets -->
                            <td class="px-4 py-4">
                                <div class="font-semibold text-white">{{ $tenant->products_count }} Products</div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $tenant->shop_locations_count }} Outlets &bull; {{ $tenant->staff_count }} Staff
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $tenant->status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                    {{ $tenant->status }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('super-admin.tenants.enter', $tenant) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-xs flex items-center gap-1 cursor-pointer"
                                            title="Enter Store Admin Console"
                                        >
                                            <span>Console</span>
                                            <span>&nearr;</span>
                                        </button>
                                    </form>

                                    <a
                                        href="{{ route('super-admin.tenants.show', $tenant) }}"
                                        class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium transition"
                                    >
                                        Inspect
                                    </a>

                                    <form method="POST" action="{{ route('super-admin.tenants.toggle-status', $tenant) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-2 py-1.5 rounded-lg border text-xs font-semibold transition cursor-pointer {{ $tenant->status === 'active' ? 'border-rose-500/30 text-rose-400 hover:bg-rose-500/20' : 'border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20' }}"
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
                            <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                                No stores found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tenants->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
</x-super-admin-layout>
