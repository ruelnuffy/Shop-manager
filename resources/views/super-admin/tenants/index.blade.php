<x-super-admin-layout>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tenant Stores Directory</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage stores and subscriptions</p>
        </div>

        <a
            href="{{ route('super-admin.tenants.create') }}"
            class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition shadow-xs flex items-center gap-1.5 shrink-0"
        >
            <span>+ Provision Store</span>
        </a>
    </div>

    <!-- Filters & Search Bar (White & Clean) -->
    <form method="GET" action="{{ route('super-admin.tenants.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col md:flex-row items-center gap-3">
        <div class="relative flex-1 w-full">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search stores by brand name, slug, or apex domain..."
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
                <option value="starter" {{ request('plan') === 'starter' ? 'selected' : '' }}>Starter</option>
                <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                <option value="standard" {{ request('plan') === 'standard' ? 'selected' : '' }}>Standard</option>
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
        </div>
    </form>

    <!-- Tenants Table (Spacious White Container) -->
    <div class="rounded-2xl bg-white border border-slate-200/90 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Store &amp; Branding</th>
                        <th class="px-4 py-4">Slug / Subdomain</th>
                        <th class="px-4 py-4">Plan &amp; Features</th>
                        <th class="px-4 py-4">Catalog &amp; Outlets</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($tenants as $tenant)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Store & Branding -->
                            <td class="px-6 py-4.5">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
                                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition text-sm">
                                            {{ $tenant->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-400 truncate max-w-[220px]">
                                            {{ $tenant->setting('branding.tagline', 'Retail Workspace') }}
                                        </div>
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

                            <!-- Plan & Features -->
                            <td class="px-4 py-4.5 space-y-1.5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $tenant->plan === 'enterprise' ? 'bg-violet-50 text-violet-700 border border-violet-200' : ($tenant->plan === 'pro' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                    {{ $tenant->plan }}
                                </span>
                                <div class="flex items-center gap-1.5 text-[10px]">
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
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('super-admin.tenants.enter', $tenant) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 text-xs font-semibold transition flex items-center gap-1 cursor-pointer"
                                            title="Enter Store Admin Console"
                                        >
                                            <span>Console &nearr;</span>
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
                                No stores found matching your criteria.
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
