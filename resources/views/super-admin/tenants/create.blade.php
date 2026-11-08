<x-super-admin-layout>
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <a href="{{ route('super-admin.tenants.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 mb-1 transition">
                &larr; Back to Stores
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Provision New Tenant Store</h1>
            <p class="text-xs text-slate-500 mt-0.5">Configure store identity, custom branding, logo, colors, and admin credentials in one flow.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium space-y-1">
            <div class="font-bold">Please correct the following errors:</div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Creation Form -->
    <form
        method="POST"
        action="{{ route('super-admin.tenants.store') }}"
        enctype="multipart/form-data"
        class="space-y-8 max-w-5xl"
        x-data="{
            name: '{{ old('name', '') }}',
            slug: '{{ old('slug', '') }}',
            brandName: '{{ old('brand_name', '') }}',
            tagline: '{{ old('tagline', 'Curated Boutique & Ready-to-Wear') }}',
            primaryColor: '{{ old('primary_color', '#c5a880') }}',
            secondaryColor: '{{ old('secondary_color', '#1a1a1a') }}',
            accentColor: '{{ old('accent_color', '#e8d8c8') }}',
            logoPreview: null,
            previewLogo(event) {
                const file = event.target.files[0];
                if (file) {
                    this.logoPreview = URL.createObjectURL(file);
                }
            },
            setPalette(p, s, a) {
                this.primaryColor = p;
                this.secondaryColor = s;
                this.accentColor = a;
            }
        }"
    >
        @csrf

        <!-- 1. Store Profile & Routing -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                <span>Store Identity &amp; Domain Routing</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Business Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Legal Business Name *</label>
                    <input
                        type="text"
                        name="name"
                        x-model="name"
                        @input="if(!slug) slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, ''); if(!brandName) brandName = name;"
                        value="{{ old('name') }}"
                        required
                        placeholder="e.g. Zenith Fashion House Ltd"
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
                    <span class="text-[10px] text-slate-400 mt-1 block">Used for URL routing and multi-tenant scoping.</span>
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
                        <span class="px-3.5 py-2.5 rounded-r-xl bg-slate-100 border border-l-0 border-slate-200 text-xs text-slate-500 font-mono whitespace-nowrap">
                            .shopmanager.test
                        </span>
                    </div>
                </div>

                <!-- Custom Apex Domain -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Custom Vanity Domain (Optional)</label>
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

        <!-- 2. Brand Identity, Logo & Storefront Styling (New) -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white text-xs flex items-center justify-center font-bold">2</span>
                    <span>Storefront Branding, Logo &amp; Color Theme</span>
                </h2>
                <span class="text-xs text-indigo-600 font-semibold">Live Real-Time Theming</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Branding Inputs (2 cols) -->
                <div class="lg:col-span-2 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Brand Display Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Storefront Display Name</label>
                            <input
                                type="text"
                                name="brand_name"
                                x-model="brandName"
                                value="{{ old('brand_name') }}"
                                placeholder="e.g. Zenith Luxury"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                            />
                            <span class="text-[10px] text-slate-400 mt-1 block">Displayed on public storefront navbar &amp; headers.</span>
                        </div>

                        <!-- Tagline -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Brand Tagline / Slogan</label>
                            <input
                                type="text"
                                name="tagline"
                                x-model="tagline"
                                value="{{ old('tagline') }}"
                                placeholder="e.g. Classy Looks For Every You"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                            />
                            <span class="text-[10px] text-slate-400 mt-1 block">Featured on hero headlines &amp; guest pages.</span>
                        </div>
                    </div>

                    <!-- Logo Upload & URL -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Brand Logo Image</label>
                            <span class="text-[11px] text-slate-400">PNG, JPG, SVG, WebP (Max 3MB)</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Upload Logo File</label>
                                <input
                                    type="file"
                                    name="logo"
                                    accept="image/*"
                                    @change="previewLogo"
                                    class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                                />
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Or Logo Image URL</label>
                                <input
                                    type="text"
                                    name="logo_url"
                                    value="{{ old('logo_url') }}"
                                    placeholder="https://... or images/logo.png"
                                    class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 font-mono"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Color Palette Section -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Storefront Color Palette</label>
                            <span class="text-[11px] text-slate-400">Controls buttons, badges, accents &amp; highlights</span>
                        </div>

                        <!-- Quick Color Swatches Presets -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[11px] font-semibold text-slate-500">Presets:</span>
                            <button
                                type="button"
                                @click="setPalette('#c5a880', '#1a1a1a', '#e8d8c8')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-white border border-slate-200 hover:border-slate-400 transition cursor-pointer shadow-2xs"
                            >
                                <span class="w-3 h-3 rounded-full bg-[#c5a880]"></span>
                                <span>Luxury Gold</span>
                            </button>
                            <button
                                type="button"
                                @click="setPalette('#e06383', '#1a1a1a', '#fbe6eb')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-white border border-slate-200 hover:border-slate-400 transition cursor-pointer shadow-2xs"
                            >
                                <span class="w-3 h-3 rounded-full bg-[#e06383]"></span>
                                <span>Boutique Rose</span>
                            </button>
                            <button
                                type="button"
                                @click="setPalette('#4f46e5', '#0f172a', '#e0e7ff')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-white border border-slate-200 hover:border-slate-400 transition cursor-pointer shadow-2xs"
                            >
                                <span class="w-3 h-3 rounded-full bg-[#4f46e5]"></span>
                                <span>Royal Indigo</span>
                            </button>
                            <button
                                type="button"
                                @click="setPalette('#059669', '#064e3b', '#d1fae5')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-white border border-slate-200 hover:border-slate-400 transition cursor-pointer shadow-2xs"
                            >
                                <span class="w-3 h-3 rounded-full bg-[#059669]"></span>
                                <span>Emerald</span>
                            </button>
                            <button
                                type="button"
                                @click="setPalette('#18181b', '#09090b', '#f4f4f5')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-white border border-slate-200 hover:border-slate-400 transition cursor-pointer shadow-2xs"
                            >
                                <span class="w-3 h-3 rounded-full bg-[#18181b]"></span>
                                <span>Noir</span>
                            </button>
                        </div>

                        <!-- Individual Color Inputs -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                            <!-- Primary Color -->
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                                <label class="block text-[11px] font-bold uppercase text-slate-700">Primary Color</label>
                                <div class="flex items-center gap-2">
                                    <input
                                        type="color"
                                        x-model="primaryColor"
                                        class="w-8 h-8 rounded-lg border-0 p-0 cursor-pointer bg-transparent shrink-0"
                                    />
                                    <input
                                        type="text"
                                        name="primary_color"
                                        x-model="primaryColor"
                                        placeholder="#c5a880"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-mono text-slate-800 uppercase focus:outline-none focus:border-indigo-500"
                                    />
                                </div>
                            </div>

                            <!-- Secondary Color -->
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                                <label class="block text-[11px] font-bold uppercase text-slate-700">Secondary / Dark</label>
                                <div class="flex items-center gap-2">
                                    <input
                                        type="color"
                                        x-model="secondaryColor"
                                        class="w-8 h-8 rounded-lg border-0 p-0 cursor-pointer bg-transparent shrink-0"
                                    />
                                    <input
                                        type="text"
                                        name="secondary_color"
                                        x-model="secondaryColor"
                                        placeholder="#1a1a1a"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-mono text-slate-800 uppercase focus:outline-none focus:border-indigo-500"
                                    />
                                </div>
                            </div>

                            <!-- Accent Color -->
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                                <label class="block text-[11px] font-bold uppercase text-slate-700">Accent / Tint</label>
                                <div class="flex items-center gap-2">
                                    <input
                                        type="color"
                                        x-model="accentColor"
                                        class="w-8 h-8 rounded-lg border-0 p-0 cursor-pointer bg-transparent shrink-0"
                                    />
                                    <input
                                        type="text"
                                        name="accent_color"
                                        x-model="accentColor"
                                        placeholder="#e8d8c8"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-mono text-slate-800 uppercase focus:outline-none focus:border-indigo-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Receipt Header & Currency -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Invoice / Receipt Header</label>
                            <input
                                type="text"
                                name="invoice_header"
                                value="{{ old('invoice_header') }}"
                                placeholder="e.g. Zenith Fashion — Flagship Boutique"
                                class="w-full px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Currency Code &amp; Symbol</label>
                            <div class="flex items-center gap-2">
                                <input
                                    type="text"
                                    name="currency"
                                    value="{{ old('currency', 'NGN') }}"
                                    placeholder="NGN"
                                    class="w-1/2 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 font-mono focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                                />
                                <input
                                    type="text"
                                    name="currency_symbol"
                                    value="{{ old('currency_symbol', '₦') }}"
                                    placeholder="₦"
                                    class="w-1/2 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 font-mono text-center focus:outline-none focus:border-indigo-600 focus:bg-white transition"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Storefront Mockup Preview Card (1 col) -->
                <div class="rounded-2xl border border-slate-200 p-5 bg-gradient-to-b from-slate-50 to-white flex flex-col justify-between space-y-4 shadow-sm">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Live Storefront Preview</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">Reactive</span>
                        </div>

                        <!-- Mini Mock Navbar -->
                        <div class="p-3 rounded-xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <template x-if="logoPreview">
                                    <img :src="logoPreview" class="h-6 w-auto object-contain max-w-[80px]" alt="Logo Preview" />
                                </template>
                                <template x-if="!logoPreview">
                                    <div
                                        class="w-7 h-7 rounded-lg text-white font-bold flex items-center justify-center text-[10px] shadow-2xs"
                                        :style="'background-color: ' + primaryColor"
                                        x-text="(brandName || name || 'SM').substring(0, 2).toUpperCase()"
                                    ></div>
                                </template>
                                <span class="font-bold text-xs text-slate-900 truncate max-w-[110px]" x-text="brandName || name || 'Brand Name'"></span>
                            </div>

                            <div class="w-4 h-4 rounded-full" :style="'background-color: ' + accentColor"></div>
                        </div>

                        <!-- Mini Mock Hero -->
                        <div class="p-4 rounded-xl border text-center space-y-2 bg-white" :style="'border-color: ' + accentColor">
                            <span class="text-[9px] font-bold uppercase tracking-widest block text-slate-400" x-text="tagline || 'Curated Boutique & Ready-to-Wear'"></span>
                            <h3 class="font-bold text-base text-slate-900 tracking-tight" x-text="brandName || name || 'Your Brand'"></h3>
                            <button
                                type="button"
                                class="px-3.5 py-1.5 rounded-full text-[11px] font-bold text-white shadow-xs mx-auto block cursor-default"
                                :style="'background-color: ' + primaryColor"
                            >
                                Shop Collection &rarr;
                            </button>
                        </div>
                    </div>

                    <!-- Color Swatches Footer -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-around text-center">
                        <div>
                            <div class="w-6 h-6 rounded-full mx-auto border border-black/10 shadow-2xs" :style="'background-color: ' + primaryColor"></div>
                            <span class="text-[9px] font-mono text-slate-500 mt-1 block">Primary</span>
                        </div>
                        <div>
                            <div class="w-6 h-6 rounded-full mx-auto border border-black/10 shadow-2xs" :style="'background-color: ' + secondaryColor"></div>
                            <span class="text-[9px] font-mono text-slate-500 mt-1 block">Dark</span>
                        </div>
                        <div>
                            <div class="w-6 h-6 rounded-full mx-auto border border-black/10 shadow-2xs" :style="'background-color: ' + accentColor"></div>
                            <span class="text-[9px] font-mono text-slate-500 mt-1 block">Accent</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Plan & Feature Entitlements -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white text-xs flex items-center justify-center font-bold">3</span>
                <span>Subscription Tier &amp; Feature Flags</span>
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

        <!-- 4. Initial Store Admin Account -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white text-xs flex items-center justify-center font-bold">4</span>
                <span>Merchant Owner / Store Admin Account</span>
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
                Provision Store &amp; Brand &rarr;
            </button>
        </div>
    </form>
</x-super-admin-layout>
