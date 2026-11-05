<x-super-admin-layout>
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 mb-1">
                &larr; Back to {{ $tenant->name }}
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Edit Store Settings</h1>
        </div>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('super-admin.tenants.update', $tenant) }}" class="space-y-8 max-w-4xl">
        @csrf
        @method('PUT')

        <!-- Store Identity Section -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 pb-3 border-b border-slate-100">
                1. Store Identity &amp; Routing
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Store Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Store / Brand Name *</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $tenant->name) }}"
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    />
                </div>

                <!-- Slug -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Tenant Slug * (Unique)</label>
                    <input
                        type="text"
                        name="slug"
                        value="{{ old('slug', $tenant->slug) }}"
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 font-mono focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    />
                    <span class="text-[10px] text-slate-400 mt-1 block">Internal identifier for routing and scoping.</span>
                </div>

                <!-- Subdomain -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Subdomain (Optional)</label>
                    <div class="flex items-center">
                        <input
                            type="text"
                            name="subdomain"
                            value="{{ old('subdomain', $tenant->subdomain) }}"
                            placeholder="store-name"
                            class="w-full px-4 py-2.5 rounded-l-xl bg-slate-50 border border-r-0 border-slate-200 text-xs text-slate-900 placeholder-slate-400 font-mono focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                        />
                        <span class="px-3 py-2.5 rounded-r-xl bg-slate-100 border border-l-0 border-slate-200 text-xs text-slate-500 font-mono whitespace-nowrap">
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
                        value="{{ old('domain', $tenant->domain) }}"
                        placeholder="e.g. www.jessiclassiq.com"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 font-mono focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    />
                </div>

                <!-- Tagline -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Store Tagline / Slogan</label>
                    <input
                        type="text"
                        name="tagline"
                        value="{{ old('tagline', data_get($tenant->settings, 'branding.tagline')) }}"
                        placeholder="e.g. Luxury Haute Couture & Ready-to-Wear Atelier"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    />
                </div>
            </div>
        </div>

        <!-- Plan & Status Section -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 pb-3 border-b border-slate-100">
                2. Subscription Tier &amp; Account Status
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Plan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Platform Plan *</label>
                    <select
                        name="plan"
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    >
                        <option value="starter" {{ old('plan', $tenant->plan) === 'starter' ? 'selected' : '' }}>Starter Tier</option>
                        <option value="pro" {{ old('plan', $tenant->plan) === 'pro' ? 'selected' : '' }}>Pro Tier</option>
                        <option value="enterprise" {{ old('plan', $tenant->plan) === 'enterprise' ? 'selected' : '' }}>Enterprise Tier</option>
                        <option value="standard" {{ old('plan', $tenant->plan) === 'standard' ? 'selected' : '' }}>Standard Tier</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Store Status *</label>
                    <select
                        name="status"
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                    >
                        <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>Active (Full Access)</option>
                        <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>Suspended (Access Blocked)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Feature Capabilities Section -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 pb-3 border-b border-slate-100">
                3. Feature Modules &amp; Flags
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-slate-300 transition">
                    <input
                        type="checkbox"
                        name="features[storefront]"
                        value="1"
                        {{ old('features.storefront', $tenant->hasFeature('storefront')) ? 'checked' : '' }}
                        class="mt-1 rounded bg-white border-slate-300 text-indigo-600 focus:ring-0"
                    />
                    <div>
                        <div class="text-xs font-bold text-slate-900">Public Online Storefront</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Enables the customer e-commerce catalog, cart, and self-service checkout.</div>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-slate-300 transition">
                    <input
                        type="checkbox"
                        name="features[payroll]"
                        value="1"
                        {{ old('features.payroll', $tenant->hasFeature('payroll')) ? 'checked' : '' }}
                        class="mt-1 rounded bg-white border-slate-300 text-indigo-600 focus:ring-0"
                    />
                    <div>
                        <div class="text-xs font-bold text-slate-900">Staff Payroll &amp; Paystack Transfers</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Automated bank account validation and bulk salary transfers via Paystack.</div>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-slate-300 transition">
                    <input
                        type="checkbox"
                        name="features[ocr]"
                        value="1"
                        {{ old('features.ocr', $tenant->hasFeature('ocr')) ? 'checked' : '' }}
                        class="mt-1 rounded bg-white border-slate-300 text-indigo-600 focus:ring-0"
                    />
                    <div>
                        <div class="text-xs font-bold text-slate-900">OCR Paper Ledger Ingestion</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">AI and vision-assisted physical ledger parsing into digital orders.</div>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-slate-300 transition">
                    <input
                        type="checkbox"
                        name="features[custom_domain]"
                        value="1"
                        {{ old('features.custom_domain', $tenant->hasFeature('custom_domain')) ? 'checked' : '' }}
                        class="mt-1 rounded bg-white border-slate-300 text-indigo-600 focus:ring-0"
                    />
                    <div>
                        <div class="text-xs font-bold text-slate-900">Custom Apex Domain Routing</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Allows tenant to bind a white-labeled custom vanity domain.</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-4">
            <a
                href="{{ route('super-admin.tenants.show', $tenant) }}"
                class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider transition shadow-sm cursor-pointer"
            >
                Save Store Changes
            </button>
        </div>
    </form>
</x-super-admin-layout>
