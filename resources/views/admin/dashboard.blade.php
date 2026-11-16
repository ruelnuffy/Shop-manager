<x-admin-layout>
    @php
        $currentTenant = app(\App\Services\CurrentTenant::class)->get();
        $storeName = $currentTenant ? $currentTenant->setting('branding.name', $currentTenant->name) : config('app.platform_name', 'ShopManager');
        $planActive = ! $currentTenant || $currentTenant->isActive();
        $pendingWhatsappText = $currentTenant
            ? rawurlencode("Hello ShopManager! Please activate my {$currentTenant->plan} plan for \"{$storeName}\" (Phone: ".(Auth::user()->phone ?: $currentTenant->setting('contact.phone', '')).").")
            : '';
    @endphp

    <div class="max-w-6xl mx-auto space-y-8">
        @if (session('whatsapp_onboarding'))
            @php
                $onboarding = session('whatsapp_onboarding');
            @endphp
            <div
                x-data="{ open: true }"
                x-show="open"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
            >
                <div
                    @click.outside="open = false"
                    class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-200 p-6 sm:p-7 space-y-5"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.031 2c-5.516 0-9.969 4.453-9.969 9.969 0 1.766.469 3.484 1.344 5l-1.406 5.156 5.281-1.391c1.469.797 3.109 1.219 4.75 1.219 5.516 0 9.969-4.453 9.969-9.969S17.547 2 12.031 2zm5.813 14.125c-.25.703-1.453 1.344-2 1.422-.516.078-1.156.109-1.875-.109-.438-.141-1-.328-1.719-.641-3.031-1.313-5-4.375-5.156-4.578-.156-.203-1.234-1.641-1.234-3.125s.781-2.219 1.063-2.516c.281-.297.609-.375.813-.375.203 0 .406 0 .578.016.188.016.438-.078.688.516.25.609.859 2.094.938 2.25.078.156.125.344.016.547-.109.203-.156.344-.313.516-.156.172-.328.391-.469.531-.156.156-.328.328-.141.641.188.313.844 1.391 1.813 2.25 1.25 1.109 2.297 1.453 2.609 1.609.313.156.5.125.688-.078.188-.203.797-.922 1.016-1.234.219-.313.422-.266.719-.156.297.109 1.875.891 2.203 1.047.328.156.547.234.625.359.078.125.078.734-.172 1.438z"/>
                                </svg>
                            </span>
                            <div>
                                <h3 class="font-display font-extrabold text-lg text-slate-900">
                                    Activate Your Plan on WhatsApp
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $onboarding['store_name'] ?? 'Your store' }} is ready
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="open = false"
                            class="text-slate-400 hover:text-slate-600 transition cursor-pointer"
                            aria-label="Close"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <p class="text-sm text-slate-600 leading-relaxed">
                        Send us a quick message on WhatsApp to complete billing and activate your <strong class="text-slate-900 uppercase">{{ $onboarding['plan'] ?? 'selected' }}</strong> plan, or we will reach out to <strong class="text-slate-900">{{ $onboarding['phone'] ?? 'your number' }}</strong>.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-2.5 pt-1">
                        <a
                            href="{{ $onboarding['whatsapp_url'] ?? 'https://wa.me/2349046686176' }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            @click="open = false"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/20 transition"
                        >
                            <span>Message on WhatsApp</span>
                            <span>&rarr;</span>
                        </a>
                        <button
                            type="button"
                            @click="open = false"
                            class="px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition cursor-pointer"
                        >
                            Later
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Welcome Greeting -->
        <div class="pt-2">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Welcome back</span>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">
                    {{ $storeName }}
                </h2>

                <div class="flex flex-wrap items-center gap-2">
                    @if ($planActive && $currentTenant && $currentTenant->hasFeature('storefront'))
                        @php
                            $shareStoreUrl = route('store.share', $currentTenant->slug);
                        @endphp
                        <a href="{{ $shareStoreUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white text-slate-700 text-xs font-semibold shadow-md shadow-slate-900/5 hover:shadow-lg transition">
                            <span>View Storefront</span>
                            <span class="text-slate-400">&nearr;</span>
                        </a>
                        <button
                            type="button"
                            onclick="navigator.clipboard?.writeText('{{ $shareStoreUrl }}'); this.querySelector('span').textContent = 'Copied!'; setTimeout(() => { this.querySelector('span').textContent = 'Copy Store Link'; }, 2000);"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold transition cursor-pointer"
                            title="{{ $shareStoreUrl }}"
                        >
                            <span>Copy Store Link</span>
                        </button>
                    @endif

                    @if ($planActive && Auth::user()->isAdmin() && !session()->has('impersonator_id'))
                        <form method="POST" action="{{ route('admin.impersonate-staff') }}" class="inline">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-md shadow-emerald-950/15 hover:shadow-lg transition cursor-pointer"
                                title="Login directly to Staff Walk-In POS Till"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                <span>Login to Staff Dashboard &nearr;</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @if (! $planActive)
            <div class="p-5 sm:p-6 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-wider text-amber-800">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>Plan Pending Activation</span>
                    </div>
                    <p class="text-sm text-amber-950 font-medium">
                        All store features are locked until your <strong class="uppercase">{{ $currentTenant->plan ?? 'selected' }}</strong> plan is activated. Message us on WhatsApp to complete billing.
                    </p>
                </div>
                <a
                    href="https://wa.me/2349046686176?text={{ $pendingWhatsappText }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-emerald-600/20 transition shrink-0"
                >
                    <span>Message on WhatsApp</span>
                    <span>&rarr;</span>
                </a>
            </div>
        @endif

        <div class="{{ $planActive ? 'space-y-8' : 'space-y-8 pointer-events-none opacity-45 select-none grayscale' }}" @if (! $planActive) aria-disabled="true" @endif>
        <!-- Cloud Workspace Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pt-2">

            <!-- Cloud 1: Make a Sale (POS) -->
            <div class="group relative overflow-hidden rounded-[2rem] p-6 bg-gradient-to-br from-white via-amber-50/40 to-amber-100/50 border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)] hover:shadow-[0_22px_45px_-12px_rgba(217,119,6,0.16)] hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[210px]">
                <div class="absolute -top-8 -right-8 w-28 h-28 rounded-full bg-amber-200/35 blur-md pointer-events-none"></div>
                <div class="absolute top-3 right-6 w-14 h-14 rounded-full bg-white/80 blur-xs pointer-events-none"></div>

                <div class="relative">
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-amber-100/80 text-[10px] font-bold uppercase tracking-wider text-amber-800">Walk-In Register</span>
                    <h3 class="font-display font-extrabold text-xl text-slate-900 mt-2.5">Make a Sale</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-600 font-medium">
                        <div class="flex items-center gap-2 text-slate-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span><strong>Today's Revenue:</strong> ₦{{ number_format($todayRevenue, 0) }}</span>
                        </div>
                        <div class="pl-3.5 text-[11px]">Barcode scan, cash &amp; card checkout</div>
                        <div class="pl-3.5 text-[11px]">Instant receipt generation</div>
                    </div>
                </div>

                <div class="relative pt-5">
                    <a href="{{ route('admin.sales.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/90 shadow-xs text-xs font-bold text-amber-900 group-hover:bg-amber-500 group-hover:text-white transition">
                        <span>Open register</span>
                        <span class="transition-transform group-hover:translate-x-0.5">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Cloud 2: Orders -->
            <div class="group relative overflow-hidden rounded-[2rem] p-6 bg-gradient-to-br from-white via-emerald-50/40 to-emerald-100/50 border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)] hover:shadow-[0_22px_45px_-12px_rgba(16,185,129,0.16)] hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[210px]">
                <div class="absolute -top-8 -right-8 w-28 h-28 rounded-full bg-emerald-200/35 blur-md pointer-events-none"></div>
                <div class="absolute top-3 right-6 w-14 h-14 rounded-full bg-white/80 blur-xs pointer-events-none"></div>

                <div class="relative">
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100/80 text-[10px] font-bold uppercase tracking-wider text-emerald-800">Fulfillment &amp; Shipping</span>
                    <h3 class="font-display font-extrabold text-xl text-slate-900 mt-2.5">Orders</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-600 font-medium">
                        <div class="flex items-center gap-2 text-slate-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span><strong>{{ $pendingOrdersCount }}</strong> pending to pack</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            <span><strong>{{ $processingOrdersCount }}</strong> out in delivery</span>
                        </div>
                        <div class="pl-3.5 text-[11px]">{{ $totalOrdersCount }} all-time customer orders</div>
                    </div>
                </div>

                <div class="relative pt-5">
                    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/90 shadow-xs text-xs font-bold text-emerald-900 group-hover:bg-emerald-600 group-hover:text-white transition">
                        <span>Open orders</span>
                        <span class="transition-transform group-hover:translate-x-0.5">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Cloud 3: Products -->
            <div class="group relative overflow-hidden rounded-[2rem] p-6 bg-gradient-to-br from-white via-rose-50/40 to-rose-100/50 border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)] hover:shadow-[0_22px_45px_-12px_rgba(244,63,94,0.16)] hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[210px]">
                <div class="absolute -top-8 -right-8 w-28 h-28 rounded-full bg-rose-200/35 blur-md pointer-events-none"></div>
                <div class="absolute top-3 right-6 w-14 h-14 rounded-full bg-white/80 blur-xs pointer-events-none"></div>

                <div class="relative">
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-rose-100/80 text-[10px] font-bold uppercase tracking-wider text-rose-800">Store Catalog</span>
                    <h3 class="font-display font-extrabold text-xl text-slate-900 mt-2.5">Products</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-600 font-medium">
                        <div class="text-slate-700 font-semibold">Curated items, sizes &amp; colors</div>
                        <div class="text-[11px]">Pricing, discounts, and photo uploads</div>
                        <div class="text-[11px]">Active in online store &amp; counter POS</div>
                    </div>
                </div>

                <div class="relative pt-5 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/90 shadow-xs text-xs font-bold text-rose-900 group-hover:bg-rose-600 group-hover:text-white transition">
                        <span>{{ Auth::user()->isAdmin() ? 'Manage catalog' : 'View catalog' }}</span>
                        <span class="transition-transform group-hover:translate-x-0.5">&rarr;</span>
                    </a>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.products.create') }}" class="px-3 py-1.5 rounded-full bg-white/70 hover:bg-white text-[11px] font-bold text-rose-800 transition">
                            + Add Item
                        </a>
                    @endif
                </div>
            </div>

            <!-- Cloud 4: Inventory Valuation & Health -->
            <div class="group relative overflow-hidden rounded-[2rem] p-6 bg-gradient-to-br from-white via-purple-50/40 to-purple-100/50 border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)] hover:shadow-[0_22px_45px_-12px_rgba(147,51,234,0.16)] hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[210px]">
                <div class="absolute -top-8 -right-8 w-28 h-28 rounded-full bg-purple-200/35 blur-md pointer-events-none"></div>
                <div class="absolute top-3 right-6 w-14 h-14 rounded-full bg-white/80 blur-xs pointer-events-none"></div>

                <div class="relative">
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-purple-100/80 text-[10px] font-bold uppercase tracking-wider text-purple-800">Inventory Health</span>
                    <h3 class="font-display font-extrabold text-xl text-slate-900 mt-2.5">Inventory Valuation</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-600 font-medium">
                        <div class="flex items-center gap-2 text-slate-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                            <span><strong>₦{{ number_format($totalInventoryValuation, 0) }}</strong> total stock worth</span>
                        </div>
                        @if ($lowStockVariants->count() > 0)
                            <div class="text-rose-700 font-bold pl-3.5 text-[11px]">
                                &bull; {{ $lowStockVariants->count() }} variants low on stock
                            </div>
                        @else
                            <div class="text-emerald-700 font-semibold pl-3.5 text-[11px]">
                                &bull; All item quantities healthy
                            </div>
                        @endif
                        <div class="pl-3.5 text-[11px]">Audit counts &amp; restock logs</div>
                    </div>
                </div>

                <div class="relative pt-5">
                    <a href="{{ route('admin.stock.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/90 shadow-xs text-xs font-bold text-purple-900 group-hover:bg-purple-600 group-hover:text-white transition">
                        <span>Check stock</span>
                        <span class="transition-transform group-hover:translate-x-0.5">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Cloud 5: Sales Reports -->
            <div class="group relative overflow-hidden rounded-[2rem] p-6 bg-gradient-to-br from-white via-orange-50/40 to-orange-100/50 border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)] hover:shadow-[0_22px_45px_-12px_rgba(249,115,22,0.16)] hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[210px]">
                <div class="absolute -top-8 -right-8 w-28 h-28 rounded-full bg-orange-200/35 blur-md pointer-events-none"></div>
                <div class="absolute top-3 right-6 w-14 h-14 rounded-full bg-white/80 blur-xs pointer-events-none"></div>

                <div class="relative">
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-orange-100/80 text-[10px] font-bold uppercase tracking-wider text-orange-800">Sales &amp; Finance</span>
                    <h3 class="font-display font-extrabold text-xl text-slate-900 mt-2.5">Analytics</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-600 font-medium">
                        <div class="flex items-center gap-2 text-slate-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                            <span><strong>₦{{ number_format($monthRevenue, 0) }}</strong> this month</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                            <span><strong>₦{{ number_format($weekRevenue, 0) }}</strong> rolling week</span>
                        </div>
                        <div class="pl-3.5 text-[11px]">Download reports &amp; CSV spreadsheets</div>
                    </div>
                </div>

                <div class="relative pt-5">
                    <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/90 shadow-xs text-xs font-bold text-orange-900 group-hover:bg-orange-600 group-hover:text-white transition">
                        <span>View insights</span>
                        <span class="transition-transform group-hover:translate-x-0.5">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Cloud 6: Scan Paper Daybook -->
            <div class="group relative overflow-hidden rounded-[2rem] p-6 bg-gradient-to-br from-white via-sky-50/40 to-sky-100/50 border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)] hover:shadow-[0_22px_45px_-12px_rgba(14,165,233,0.16)] hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[210px]">
                <div class="absolute -top-8 -right-8 w-28 h-28 rounded-full bg-sky-200/35 blur-md pointer-events-none"></div>
                <div class="absolute top-3 right-6 w-14 h-14 rounded-full bg-white/80 blur-xs pointer-events-none"></div>

                <div class="relative">
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-sky-100/80 text-[10px] font-bold uppercase tracking-wider text-sky-800">Paper Daybook</span>
                    <h3 class="font-display font-extrabold text-xl text-slate-900 mt-2.5">Scan Receipts</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-600 font-medium">
                        <div class="text-slate-700 font-semibold">Snap photo of written paper ledger</div>
                        <div class="text-[11px]">AI reads items, quantities &amp; prices</div>
                        <div class="text-[11px]">Imports automatically into store records</div>
                    </div>
                </div>

                <div class="relative pt-5">
                    <a href="{{ route('admin.ocr.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/90 shadow-xs text-xs font-bold text-sky-900 group-hover:bg-sky-600 group-hover:text-white transition">
                        <span>Scan page</span>
                        <span class="transition-transform group-hover:translate-x-0.5">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Cloud: Storefront Visitors -->
            <div class="group relative overflow-hidden rounded-[2rem] p-6 bg-gradient-to-br from-white via-cyan-50/40 to-cyan-100/50 border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)] hover:shadow-[0_22px_45px_-12px_rgba(6,182,212,0.16)] hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[210px]">
                <div class="absolute -top-8 -right-8 w-28 h-28 rounded-full bg-cyan-200/35 blur-md pointer-events-none"></div>
                <div class="absolute top-3 right-6 w-14 h-14 rounded-full bg-white/80 blur-xs pointer-events-none"></div>

                <div class="relative">
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-cyan-100/80 text-[10px] font-bold uppercase tracking-wider text-cyan-800">Storefront Traffic</span>
                    <h3 class="font-display font-extrabold text-xl text-slate-900 mt-2.5">Store Visitors</h3>
                    <div class="space-y-1.5 mt-3 text-xs text-slate-600 font-medium">
                        <div class="flex items-center gap-2 text-slate-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                            <span><strong>{{ number_format($storefrontVisitsToday ?? 0) }}</strong> visits today</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                            <span><strong>{{ number_format($storefrontUniqueVisitors ?? 0) }}</strong> unique visitors</span>
                        </div>
                        <div class="pl-3.5 text-[11px]">{{ number_format($storefrontVisitsTotal ?? 0) }} total storefront views</div>
                    </div>
                </div>

                <div class="relative pt-5">
                    <a href="{{ route('admin.visitors.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/90 shadow-xs text-xs font-bold text-cyan-900 group-hover:bg-cyan-600 group-hover:text-white transition">
                        <span>View visitors</span>
                        <span class="transition-transform group-hover:translate-x-0.5">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Cloud 7: Staff & Payroll (Admin Only) -->
            @if(Auth::user()->isAdmin())
                <div class="group relative overflow-hidden rounded-[2rem] p-6 bg-gradient-to-br from-white via-lime-50/40 to-lime-100/50 border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)] hover:shadow-[0_22px_45px_-12px_rgba(132,204,22,0.16)] hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[210px]">
                    <div class="absolute -top-8 -right-8 w-28 h-28 rounded-full bg-lime-200/35 blur-md pointer-events-none"></div>
                    <div class="absolute top-3 right-6 w-14 h-14 rounded-full bg-white/80 blur-xs pointer-events-none"></div>

                    <div class="relative">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-lime-100/80 text-[10px] font-bold uppercase tracking-wider text-lime-800">Store Team</span>
                        <h3 class="font-display font-extrabold text-xl text-slate-900 mt-2.5">Staff &amp; Payroll</h3>
                        <div class="space-y-1.5 mt-3 text-xs text-slate-600 font-medium">
                            <div class="text-slate-700 font-semibold">Cashiers, attendants &amp; sales staff</div>
                            <div class="text-[11px]">Sales commissions &amp; monthly salary sheets</div>
                            <div class="text-[11px]">Track attendance &amp; roles</div>
                        </div>
                    </div>

                    <div class="relative pt-5 flex items-center justify-between gap-2">
                        <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/90 shadow-xs text-xs font-bold text-lime-900 group-hover:bg-lime-600 group-hover:text-white transition">
                            <span>Manage team</span>
                            <span class="transition-transform group-hover:translate-x-0.5">&rarr;</span>
                        </a>
                        <a href="{{ route('admin.payroll.index') }}" class="px-3 py-1.5 rounded-full bg-white/70 hover:bg-white text-[11px] font-bold text-lime-800 transition">
                            Payroll &rarr;
                        </a>
                    </div>
                </div>

                @if(Auth::user()->isSuperAdmin())
                <!-- Cloud 8: Store Admin Accounts & Subscriptions (Super Admin Only) -->
                <div class="group relative overflow-hidden rounded-[2rem] p-6 bg-gradient-to-br from-white via-indigo-50/40 to-indigo-100/50 border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)] hover:shadow-[0_22px_45px_-12px_rgba(99,102,241,0.16)] hover:-translate-y-1 transition duration-200 flex flex-col justify-between min-h-[210px]">
                    <div class="absolute -top-8 -right-8 w-28 h-28 rounded-full bg-indigo-200/35 blur-md pointer-events-none"></div>
                    <div class="absolute top-3 right-6 w-14 h-14 rounded-full bg-white/80 blur-xs pointer-events-none"></div>

                    <div class="relative">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-indigo-100/80 text-[10px] font-bold uppercase tracking-wider text-indigo-800">Store Admins</span>
                        <h3 class="font-display font-extrabold text-xl text-slate-900 mt-2.5">Users &amp; Plans</h3>
                        <div class="space-y-1.5 mt-3 text-xs text-slate-600 font-medium">
                            <div class="flex items-center gap-2 text-slate-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span><strong>{{ $subscribedUsersCount }}</strong> Subscribed Users</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span><strong>{{ $freeUsersCount }}</strong> Free / Trial Users</span>
                            </div>
                            <div class="pl-3.5 text-[11px]">{{ $totalAdminUsersCount }} total store admin accounts</div>
                        </div>
                    </div>

                    <div class="relative pt-5 flex items-center justify-between">
                        <a href="#stores-section" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/90 shadow-xs text-xs font-bold text-indigo-900 group-hover:bg-indigo-600 group-hover:text-white transition">
                            <span>View accounts</span>
                            <span class="transition-transform group-hover:translate-y-0.5">&darr;</span>
                        </a>
                    </div>
                </div>
                @endif
            @endif

        </div>

        <!-- Store Admins Directory Section (Super Admin Only - Staff & Customers nested under each Admin) -->
        @if(Auth::user()->isSuperAdmin())
            <div id="stores-section" x-data="{ activeTier: 'all' }" class="bg-white rounded-2xl p-5 sm:p-7 shadow-xl shadow-slate-900/5 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-base text-slate-900">Store Admin Accounts</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 uppercase">
                                {{ $subscribedUsersCount }} Subscribed
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 uppercase">
                                {{ $freeUsersCount }} Free
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Primary merchant users on the platform. Staff members and customers are organized directly under each store admin.</p>
                    </div>

                    <!-- Quick Segment Tabs -->
                    <div class="flex items-center gap-1.5 overflow-x-auto">
                        <button
                            type="button"
                            @click="activeTier = 'all'"
                            :class="activeTier === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap cursor-pointer"
                        >
                            All Admins ({{ $totalAdminUsersCount }})
                        </button>
                        <button
                            type="button"
                            @click="activeTier = 'subscribed'"
                            :class="activeTier === 'subscribed' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'"
                            class="px-3 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap cursor-pointer flex items-center gap-1"
                        >
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            <span>Subscribed ({{ $subscribedUsersCount }})</span>
                        </button>
                        <button
                            type="button"
                            @click="activeTier = 'free'"
                            :class="activeTier === 'free' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'"
                            class="px-3 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap cursor-pointer flex items-center gap-1"
                        >
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            <span>Free Users ({{ $freeUsersCount }})</span>
                        </button>


                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-extrabold text-[10px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Store Admin User</th>
                                <th class="px-3 py-3">Store &amp; Subdomain</th>
                                <th class="px-3 py-3">Subscription Tier</th>
                                <th class="px-3 py-3">Team &amp; Customers</th>
                                <th class="px-3 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($storeAdmins as $adminUser)
                                @php
                                    $tenant = $adminUser->tenant;
                                    $isSubscribed = $tenant && in_array($tenant->plan, ['starter', 'pro', 'enterprise'], true);
                                    $tierType = $isSubscribed ? 'subscribed' : 'free';
                                    $staffList = $tenant?->users->where('role', 'staff') ?? collect();
                                    $customerList = $tenant?->users->where('role', 'customer') ?? collect();
                                @endphp
                                <tr
                                    x-show="activeTier === 'all' || activeTier === '{{ $tierType }}'"
                                    x-data="{ showMembers: false }"
                                    class="hover:bg-slate-50/70 transition"
                                >
                                    <!-- Store Admin User -->
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                                                {{ strtoupper(substr($adminUser->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
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
                                    <td class="px-3 py-3.5">
                                        <div class="font-bold text-slate-800">{{ $tenant?->name ?? 'No store attached' }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">
                                            {{ $tenant?->slug ?? 'local' }} &bull; {{ $tenant?->subdomain ? $tenant->subdomain . '.shopmanager.test' : 'apex' }}
                                        </div>
                                    </td>

                                    <!-- Subscription Tier -->
                                    <td class="px-3 py-3.5">
                                        @if ($isSubscribed)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Subscribed &bull; {{ $tenant->plan }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Free Tier &bull; {{ $tenant?->plan ?? 'standard' }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Team & Customers Under This Admin -->
                                    <td class="px-3 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                                {{ $staffList->count() }} Staff
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                                {{ $customerList->count() }} Customers
                                            </span>
                                        </div>

                                        <!-- Expandable Drawer for Staff and Customers -->
                                        @if($staffList->isNotEmpty() || $customerList->isNotEmpty())
                                            <div class="mt-1">
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
                                                    class="mt-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2 text-[11px]"
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
                                                                @foreach($customerList->take(4) as $custUser)
                                                                    <div class="flex items-center justify-between gap-2 p-1 rounded-md bg-white text-slate-600">
                                                                        <span class="truncate">{{ $custUser->name }}</span>
                                                                        <span class="text-slate-400 font-mono text-[10px]">{{ $custUser->email }}</span>
                                                                    </div>
                                                                @endforeach
                                                                @if($customerList->count() > 4)
                                                                    <div class="text-[10px] text-slate-400 italic pl-1">
                                                                        + {{ $customerList->count() - 4 }} more shoppers
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
                                    <td class="px-3 py-3.5">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ ($tenant?->status ?? 'active') === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                            {{ $tenant?->status ?? 'active' }}
                                        </span>
                                    </td>

                                    <!-- Action -->
                                    <td class="px-4 py-3.5 text-right">
                                        @if($tenant)
                                            <a
                                                href="{{ url('/admin?tenant=' . $tenant->slug) }}"
                                                class="px-2.5 py-1 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-bold transition shadow-xs inline-flex items-center gap-1"
                                            >
                                                <span>Switch &nearr;</span>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        No store admin accounts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Gentle Low Stock Notice (If Any Items Need Restock - Borderless with Soft Shadow) -->
        @if ($lowStockVariants->count() > 0)
            <div class="bg-amber-50/90 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg shadow-amber-950/5">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    <div>
                        <div class="font-bold text-sm text-slate-900">
                            {{ $lowStockVariants->count() }} {{ Str::plural('item', $lowStockVariants->count()) }} running low on stock
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            @foreach($lowStockVariants->take(3) as $v)
                                {{ $v->product->name }} ({{ $v->size ?? $v->color ?? 'variant' }}: {{ $v->stock_quantity }} left){{ !$loop->last ? ' &bull; ' : '' }}
                            @endforeach
                            @if($lowStockVariants->count() > 3)
                                and {{ $lowStockVariants->count() - 3 }} more...
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.stock.index') }}" class="px-4 py-2 rounded-xl bg-white text-amber-900 hover:bg-amber-100 text-xs font-bold transition shadow-md shadow-amber-950/5 shrink-0 self-start sm:self-center">
                    Review Stock &rarr;
                </a>
            </div>
        @endif

        <!-- Recent Customer Purchases (Borderless with Soft Shadow) -->
        <div class="bg-white rounded-2xl p-5 sm:p-7 shadow-xl shadow-slate-900/5 space-y-4">
            <div class="flex items-center justify-between pb-3">
                <div>
                    <h3 class="font-bold text-base text-slate-900">Recent Customer Purchases</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Latest sales made at the counter and online</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                    View all ({{ $totalOrdersCount }}) &rarr;
                </a>
            </div>

            @if ($recentOrders->count() > 0)
                <div class="divide-y divide-slate-100/80">
                    @foreach ($recentOrders as $order)
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition">
                                        #{{ $order->order_number }}
                                    </a>
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="text-slate-600 font-medium truncate">{{ $order->shipping_name ?? 'Walk-in Customer' }}</span>
                                </div>
                                <div class="text-slate-400 text-[11px] mt-0.5">
                                    {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }} &bull; {{ $order->created_at->diffForHumans() }}
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <span class="font-bold text-sm text-slate-900">₦{{ number_format($order->total_amount, 0) }}</span>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-800' }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10">
                    <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-2 shadow-xs">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div class="text-sm font-semibold text-slate-700">No orders recorded yet</div>
                    <p class="text-xs text-slate-400 mt-1">When transactions occur, they will display here in real time.</p>
                </div>
            @endif
        </div>
        </div>
    </div>
</x-admin-layout>
