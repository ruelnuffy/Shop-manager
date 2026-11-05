<x-super-admin-layout>
    <!-- Header -->
    <div class="flex items-center justify-between pb-6 border-b border-slate-800">
        <div>
            <a href="{{ route('super-admin.tenants.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold transition flex items-center gap-1 mb-1">
                &larr; Back to Stores Directory
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Provision New Tenant Store</h1>
            <p class="text-xs text-slate-400 mt-1">Spin up an isolated retail workspace, configure feature flags, and create the merchant owner account.</p>
        </div>
    </div>

    <!-- Creation Form -->
    <form method="POST" action="{{ route('super-admin.tenants.store') }}" class="space-y-8 max-w-4xl" x-data="{ name: '', slug: '' }">
        @csrf

        <!-- Store Profile Section -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-white pb-3 border-b border-slate-800">
                1. Store Identity &amp; Domain Routing
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Store Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Store / Brand Name *</label>
                    <input
                        type="text"
                        name="name"
                        x-model="name"
                        @input="slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')"
                        value="{{ old('name') }}"
                        required
                        placeholder="e.g. Zenith Fashion House"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                    />
                </div>

                <!-- Slug -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Tenant Slug * (Unique)</label>
                    <input
                        type="text"
                        name="slug"
                        x-model="slug"
                        value="{{ old('slug') }}"
                        required
                        placeholder="e.g. zenith-fashion"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 font-mono focus:outline-none focus:border-indigo-500"
                    />
                    <span class="text-[10px] text-slate-500 mt-1 block">Used in internal routing &amp; session tokens.</span>
                </div>

                <!-- Subdomain -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Subdomain (Optional)</label>
                    <div class="flex items-center">
                        <input
                            type="text"
                            name="subdomain"
                            value="{{ old('subdomain') }}"
                            placeholder="zenith"
                            class="flex-1 px-4 py-2.5 rounded-l-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 font-mono focus:outline-none focus:border-indigo-500"
                        />
                        <span class="px-3.5 py-2.5 rounded-r-xl bg-slate-800 border border-l-0 border-slate-700 text-xs text-slate-400 font-mono">
                            .shopmanager.test
                        </span>
                    </div>
                </div>

                <!-- Custom Domain -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Custom Apex / CNAME Domain</label>
                    <input
                        type="text"
                        name="domain"
                        value="{{ old('domain') }}"
                        placeholder="e.g. zenithstore.com"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 font-mono focus:outline-none focus:border-indigo-500"
                    />
                </div>
            </div>
        </div>

        <!-- Plan & Features Entitlements -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-white pb-3 border-b border-slate-800">
                2. Subscription Tier &amp; Feature Flags
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Plan Tier -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Platform Plan Tier *</label>
                    <select
                        name="plan"
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500"
                    >
                        <option value="starter" {{ old('plan') === 'starter' ? 'selected' : '' }}>Starter Boutique (₦25,000/mo)</option>
                        <option value="pro" {{ old('plan') === 'pro' || !old('plan') ? 'selected' : '' }}>Multi-Store Pro (₦65,000/mo)</option>
                        <option value="enterprise" {{ old('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise Chain (₦150,000/mo)</option>
                        <option value="standard" {{ old('plan') === 'standard' ? 'selected' : '' }}>Standard Legacy</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Operational Status *</label>
                    <select
                        name="status"
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500"
                    >
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Immediate Access)</option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended (Maintenance/Locked)</option>
                    </select>
                </div>
            </div>

            <!-- Feature Checkboxes -->
            <div class="space-y-3 pt-2">
                <span class="block text-xs font-bold uppercase tracking-wider text-slate-300">Feature Entitlements</span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="features[storefront]" value="1" checked class="rounded border-slate-700 text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <span class="text-xs font-bold text-white block">Public Storefront</span>
                            <span class="text-[11px] text-slate-400">Customer web catalog &amp; shopping cart</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="features[payroll]" value="1" checked class="rounded border-slate-700 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="text-xs font-bold text-white block">Automated Paystack Payroll</span>
                            <span class="text-[11px] text-slate-400">Batch salary transfers to staff NUBAN</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="features[ocr]" value="1" checked class="rounded border-slate-700 text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="text-xs font-bold text-white block">OCR Ledger Digitize</span>
                            <span class="text-[11px] text-slate-400">Extract handwritten sales notebook scans</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="features[custom_domain]" value="1" class="rounded border-slate-700 text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <span class="text-xs font-bold text-white block">Custom Domain SSL</span>
                            <span class="text-[11px] text-slate-400">Apex domain host resolution</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Initial Store Admin Account -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-white pb-3 border-b border-slate-800">
                3. Merchant Owner / Store Admin Account
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Admin Full Name *</label>
                    <input
                        type="text"
                        name="admin_name"
                        value="{{ old('admin_name') }}"
                        required
                        placeholder="e.g. Adeola Johnson"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Admin Email * (Login)</label>
                    <input
                        type="email"
                        name="admin_email"
                        value="{{ old('admin_email') }}"
                        required
                        placeholder="e.g. adeola@zenith.com"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Password *</label>
                    <input
                        type="password"
                        name="admin_password"
                        required
                        placeholder="Min 8 characters"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                    />
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a
                href="{{ route('super-admin.tenants.index') }}"
                class="px-5 py-2.5 rounded-xl border border-slate-800 hover:bg-slate-800 text-slate-300 text-xs font-semibold transition"
            >
                Cancel
            </a>
            <button
                type="submit"
                class="px-7 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider transition shadow-lg shadow-indigo-600/30 cursor-pointer"
            >
                Provision Store &rarr;
            </button>
        </div>
    </form>
</x-super-admin-layout>

