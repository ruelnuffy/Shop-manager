<x-super-admin-layout>
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <a href="{{ route('super-admin.tenants.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 mb-1">
                &larr; Back to Stores
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Provision New Tenant Store</h1>
        </div>
    </div>

    <!-- Creation Form -->
    <form method="POST" action="{{ route('super-admin.tenants.store') }}" class="space-y-8 max-w-4xl" x-data="{ name: '', slug: '' }">
        @csrf

        <!-- Store Profile Section -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 pb-3 border-b border-slate-100">
                1. Store Identity &amp; Domain Routing
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Store Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Store / Brand Name *</label>
                    <input
                        type="text"
                        name="name"
                        x-model="name"
                        @input="slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')"
                        value="{{ old('name') }}"
                        required
                        placeholder="e.g. Zenith Fashion House"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    />
                </div>

                <!-- Slug -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Tenant Slug * (Unique)</label>
                    <input
                        type="text"
                        name="slug"
                        x-model="slug"
                        value="{{ old('slug') }}"
                        required
                        placeholder="e.g. zenith-fashion"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 font-mono focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    />
                    <span class="text-[10px] text-slate-400 mt-1 block">Used in internal tenant scoping &amp; sessions.</span>
                </div>

                <!-- Subdomain -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Subdomain (Optional)</label>
                    <div class="flex items-center">
                        <input
                            type="text"
                            name="subdomain"
                            value="{{ old('subdomain') }}"
                            placeholder="zenith"
                            class="flex-1 px-4 py-2.5 rounded-l-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 font-mono focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                        />
                        <span class="px-3.5 py-2.5 rounded-r-xl bg-slate-100 border border-l-0 border-slate-200 text-xs text-slate-500 font-mono">
                            .shopmanager.test
                        </span>
                    </div>
                </div>

                <!-- Custom Domain -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Custom Apex / Vanity Domain</label>
                    <input
                        type="text"
                        name="domain"
                        value="{{ old('domain') }}"
                        placeholder="e.g. www.zenithstore.com"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 font-mono focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    />
                </div>
            </div>
        </div>

        <!-- Plan & Features Entitlements -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 pb-3 border-b border-slate-100">
                2. Subscription Tier &amp; Feature Flags
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Plan Tier -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Platform Plan Tier *</label>
                    <select
                        name="plan"
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    >
                        <option value="starter" {{ old('plan') === 'starter' ? 'selected' : '' }}>Starter Boutique (₦25,000/mo)</option>
                        <option value="pro" {{ old('plan') === 'pro' || !old('plan') ? 'selected' : '' }}>Multi-Store Pro (₦65,000/mo)</option>
                        <option value="enterprise" {{ old('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise Chain (₦180,000/mo)</option>
                        <option value="standard" {{ old('plan') === 'standard' ? 'selected' : '' }}>Standard Legacy</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Operational Status *</label>
                    <select
                        name="status"
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    >
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Full Access)</option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended (Access Blocked)</option>
                    </select>
                </div>
            </div>

            <!-- Feature Checkboxes -->
            <div class="space-y-3 pt-2">
                <span class="block text-xs font-bold uppercase tracking-wider text-slate-700">Feature Entitlements</span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-slate-300 flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="features[storefront]" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Public Storefront</span>
                            <span class="text-[11px] text-slate-500">Customer web catalog &amp; checkout</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-slate-300 flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="features[payroll]" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Automated Paystack Payroll</span>
                            <span class="text-[11px] text-slate-500">Batch salary transfers to staff NUBAN</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-slate-300 flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="features[ocr]" value="1" checked class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">OCR Ledger Digitize</span>
                            <span class="text-[11px] text-slate-500">Extract handwritten sales notebook scans</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-slate-300 flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="features[custom_domain]" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Custom Domain SSL</span>
                            <span class="text-[11px] text-slate-500">Apex domain host resolution</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Initial Store Admin Account -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 pb-3 border-b border-slate-100">
                3. Merchant Owner / Store Admin Account
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Admin Full Name *</label>
                    <input
                        type="text"
                        name="admin_name"
                        value="{{ old('admin_name') }}"
                        required
                        placeholder="e.g. Adeola Johnson"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Admin Email * (Login)</label>
                    <input
                        type="email"
                        name="admin_email"
                        value="{{ old('admin_email') }}"
                        required
                        placeholder="e.g. adeola@zenith.com"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Password *</label>
                    <input
                        type="password"
                        name="admin_password"
                        required
                        placeholder="Min 8 characters"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    />
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a
                href="{{ route('super-admin.tenants.index') }}"
                class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition"
            >
                Cancel
            </a>
            <button
                type="submit"
                class="px-7 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider transition shadow-sm cursor-pointer"
            >
                Provision Store &rarr;
            </button>
        </div>
    </form>
</x-super-admin-layout>
