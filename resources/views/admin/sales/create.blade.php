<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="font-display font-extrabold text-xl sm:text-2xl text-slate-900 tracking-tight">
                    Direct In-Store POS Sale
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    Fast counter checkout &bull; Instant inventory deduction &amp; receipt
                </p>
            </div>

            <!-- End-of-Day Closeout (Z-Report) Trigger -->
            <button
                type="button"
                @click="showTakingsModal = true"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold shadow-lg shadow-slate-900/5 transition cursor-pointer self-start sm:self-auto"
            >
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Daily Closeout &bull; Takings</span>
            </button>
        </div>
    </x-slot>

    @php
        $currentTenant = app(\App\Services\CurrentTenant::class)->get();
        $storeName = $currentTenant ? $currentTenant->setting('branding.name', $currentTenant->name) : config('app.platform_name', 'ShopManager');
        $tagline = $currentTenant ? $currentTenant->setting('branding.tagline', 'Retail Boutique') : 'Retail Boutique';

        $variantsJson = $variants->map(function ($v) {
            $price = $v->price_override ?: ($v->product?->base_price ?: 0);
            return [
                'id' => $v->id,
                'name' => $v->product?->name ?? 'Product',
                'size' => $v->size,
                'color' => $v->color,
                'stock' => $v->stock_quantity,
                'price' => (float) $price,
            ];
        })->values();

        $recentSalesJson = $recentSales->map(function ($s) {
            $pm = 'POS TERMINAL';
            if (str_contains($s->notes, 'via ')) {
                $pm = explode(' |', explode('via ', $s->notes)[1] ?? 'POS TERMINAL')[0];
            }
            return [
                'id' => $s->id,
                'item_description' => $s->item_description,
                'quantity' => $s->quantity,
                'unit_price' => (float) $s->unit_price,
                'amount' => (float) $s->amount,
                'date' => $s->date->format('d M Y'),
                'time' => $s->created_at->format('h:i A'),
                'creator' => $s->creator?->name ?? 'Staff',
                'payment_method' => strtoupper(trim($pm)),
                'notes' => $s->notes,
            ];
        })->values();
    @endphp

    <div
        class="space-y-8"
        x-data="{
            customers: {{ json_encode($customers->map(fn($c) => ['name' => $c->name, 'phone' => $c->phone])->values()) }},
            variants: {{ json_encode($variantsJson) }},
            recentSales: {{ json_encode($recentSalesJson) }},
            search: '',
            selectedId: {{ $variants->first()?->id ?? 'null' }},
            quantity: 1,
            paymentMethod: 'pos_terminal',
            customerName: '',
            customerPhone: '',
            phoneMatches: [],
            splitCashAmount: '',
            splitTransferAmount: '',
            notes: '',
            customAmount: null,
            chargePassword: '',
            showPassword: false,
            showReceipt: false,
            showTakingsModal: false,
            receiptData: null,

            init() {
                const autoReceiptId = {{ session('receipt_sale_id', 0) }};
                if (autoReceiptId) {
                    const match = this.recentSales.find(s => s.id === autoReceiptId);
                    if (match) {
                        this.openReceipt(match);
                    }
                }
            },

            onPhoneInput() {
                if (!this.customerPhone || this.customerPhone.length < 3) {
                    this.phoneMatches = [];
                    return;
                }
                const clean = this.customerPhone.replace(/[^0-9]/g, '');
                this.phoneMatches = this.customers.filter(c => c.phone && c.phone.replace(/[^0-9]/g, '').includes(clean));
            },

            selectCustomer(c) {
                this.customerName = c.name;
                this.customerPhone = c.phone;
                this.phoneMatches = [];
            },

            onSplitCashInput() {
                const cash = parseFloat(this.splitCashAmount) || 0;
                const total = this.totalAmount;
                const diff = Math.max(0, total - cash);
                this.splitTransferAmount = diff > 0 ? diff : 0;
            },

            initSplit() {
                this.paymentMethod = 'split';
                const half = Math.round(this.totalAmount / 2);
                this.splitCashAmount = half;
                this.splitTransferAmount = this.totalAmount - half;
            },

            cleanPhone(phone) {
                if (!phone) return '';
                let p = phone.replace(/[^0-9]/g, '');
                if (p.startsWith('0') && p.length === 11) {
                    p = '234' + p.substring(1);
                }
                return p;
            },

            get selectedVariant() {
                return this.variants.find(v => v.id == this.selectedId) || null;
            },

            get filteredVariants() {
                if (!this.search.trim()) return this.variants;
                const q = this.search.toLowerCase();
                return this.variants.filter(v =>
                    v.name.toLowerCase().includes(q) ||
                    v.size.toLowerCase().includes(q) ||
                    v.color.toLowerCase().includes(q)
                );
            },

            get unitPrice() {
                return this.selectedVariant ? this.selectedVariant.price : 0;
            },

            get totalAmount() {
                if (this.customAmount !== null && this.customAmount !== '') {
                    return parseFloat(this.customAmount) || 0;
                }
                return this.unitPrice * this.quantity;
            },

            selectVariant(id) {
                this.selectedId = id;
                this.customAmount = null;
                const v = this.selectedVariant;
                if (v && this.quantity > v.stock) {
                    this.quantity = v.stock;
                }
            },

            incrementQty() {
                const max = this.selectedVariant ? this.selectedVariant.stock : 999;
                if (this.quantity < max) {
                    this.quantity++;
                    this.customAmount = null;
                }
            },

            decrementQty() {
                if (this.quantity > 1) {
                    this.quantity--;
                    this.customAmount = null;
                }
            },

            formatMoney(val) {
                return Number(val || 0).toLocaleString('en-NG', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            },

            openReceipt(sale) {
                this.receiptData = sale;
                this.showReceipt = true;
            },

            printReceipt() {
                window.print();
            },

            get rawReceiptText() {
                if (!this.receiptData) return '';
                const sep = '--------------------------------\n';
                const store = '{{ strtoupper($storeName) }}';
                const tag = '{{ $tagline }}';
                let t = '';
                t += store + '\n';
                if (tag) t += tag + '\n';
                t += '*** OFFICIAL STORE RECEIPT ***\n';
                t += sep;
                t += 'Receipt: SR-' + this.receiptData.id + '\n';
                t += 'Date:    ' + this.receiptData.date + ' ' + this.receiptData.time + '\n';
                t += 'Cashier: ' + this.receiptData.creator + '\n';
                if (this.receiptData.notes && this.receiptData.notes.includes(' to ')) {
                    t += 'Customer: ' + this.receiptData.notes.split(' to ')[1].split(' |')[0] + '\n';
                }
                t += sep;
                t += this.receiptData.item_description + '\n';
                t += this.receiptData.quantity + ' x N' + this.formatMoney(this.receiptData.unit_price) + ' = N' + this.formatMoney(this.receiptData.amount) + '\n';
                t += sep;
                t += 'TOTAL PAID:     NGN ' + this.formatMoney(this.receiptData.amount) + '\n';
                t += 'PAYMENT METHOD: ' + this.receiptData.payment_method + '\n';
                t += sep;
                t += '  Thank you for your patronage!\n';
                t += '  Goods in good condition.\n';
                t += '  Powered by {{ config('app.platform_name', 'ShopManager') }}\n\n\n\n';
                return t;
            },

            get rawbtUrl() {
                if (!this.receiptData) return '#';
                try {
                    return 'rawbt:data:text/plain;base64,' + btoa(unescape(encodeURIComponent(this.rawReceiptText)));
                } catch (e) {
                    return '#';
                }
            },

            get whatsappUrl() {
                if (!this.receiptData) return '#';
                const clean = this.cleanPhone(this.customerPhone);
                const text = encodeURIComponent(
                    '*{{ $storeName }} — SALES RECEIPT*\n' +
                    'Receipt #: SR-' + this.receiptData.id + '\n' +
                    'Date: ' + this.receiptData.date + ' ' + this.receiptData.time + '\n' +
                    'Item: ' + this.receiptData.item_description + '\n' +
                    'Qty: ' + this.receiptData.quantity + ' @ ₦' + this.formatMoney(this.receiptData.unit_price) + '\n' +
                    'Total Paid: ₦' + this.formatMoney(this.receiptData.amount) + '\n' +
                    'Payment Method: ' + this.receiptData.payment_method + '\n' +
                    'Thank you for shopping with us!'
                );
                return clean ? ('https://wa.me/' + clean + '?text=' + text) : ('https://wa.me/?text=' + text);
            }
        }"
    >
        <!-- Main Moniepoint POS Split Screen -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN: Quick Tap Catalog (7 Cols on desktop) -->
            <div class="lg:col-span-7 space-y-4">
                <!-- Search & Filters -->
                <div class="bg-white rounded-2xl p-4 shadow-xl shadow-slate-900/5 flex items-center gap-3">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Search items by name, size, or color..."
                        class="w-full bg-transparent border-0 text-xs sm:text-sm font-semibold text-slate-900 placeholder-slate-400 focus:ring-0 p-0"
                    >
                    <button
                        type="button"
                        x-show="search.length > 0"
                        @click="search = ''"
                        class="p-1 text-slate-400 hover:text-slate-600 transition cursor-pointer"
                        title="Clear search"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Product Variants Grid (Tap to Select) -->
                @if ($variants->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[560px] overflow-y-auto pr-1">
                        <template x-for="v in filteredVariants" :key="v.id">
                            <button
                                type="button"
                                @click="selectVariant(v.id)"
                                :class="selectedId == v.id ? 'bg-slate-900 text-white shadow-xl shadow-slate-900/15 ring-2 ring-slate-900' : 'bg-white hover:bg-slate-50 text-slate-800 shadow-md shadow-slate-900/5'"
                                class="p-4 rounded-2xl text-left transition duration-150 flex flex-col justify-between min-h-[110px] cursor-pointer group"
                            >
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="font-bold text-xs sm:text-sm leading-tight line-clamp-2" x-text="v.name"></div>
                                        <span
                                            x-show="selectedId == v.id"
                                            class="w-5 h-5 rounded-full bg-emerald-400 text-slate-950 flex items-center justify-center shrink-0 shadow-2xs"
                                        >
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-1.5 mt-1 text-[11px]" :class="selectedId == v.id ? 'text-slate-300' : 'text-slate-400'">
                                        <span class="font-semibold" x-text="v.size"></span>
                                        <span>&bull;</span>
                                        <span x-text="v.color"></span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-2 mt-1">
                                    <span class="font-display font-extrabold text-sm sm:text-base tracking-tight" :class="selectedId == v.id ? 'text-emerald-300' : 'text-slate-900'">
                                        ₦<span x-text="formatMoney(v.price)"></span>
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" :class="selectedId == v.id ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-600'">
                                        <span x-text="v.stock + ' in stock'"></span>
                                    </span>
                                </div>
                            </button>
                        </template>
                    </div>

                    <div x-show="filteredVariants.length === 0" class="bg-white rounded-2xl p-8 text-center shadow-md shadow-slate-900/5">
                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <div class="font-bold text-sm text-slate-800">No matching items found</div>
                        <p class="text-xs text-slate-400 mt-1">Try a different search query or clear the filter.</p>
                    </div>
                @else
                    <div class="bg-white rounded-3xl p-10 text-center shadow-xl shadow-slate-900/5 space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="font-bold text-base text-slate-900">No active stock available to sell</div>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Add products with active inventory to begin ringing up sales at the counter.
                        </p>
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-900 text-white text-xs font-bold shadow-md">
                                + Add New Product
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- RIGHT COLUMN: The Moniepoint POS Terminal (5 Cols on desktop) -->
            <div class="lg:col-span-5">
                <div class="bg-white rounded-3xl shadow-2xl shadow-slate-900/10 p-6 sm:p-7 space-y-5 sticky top-20">

                    <!-- Terminal Device Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">POS Terminal</span>
                        </div>
                        <span class="text-[10px] font-mono text-slate-400 font-semibold">Ready to Charge</span>
                    </div>

                    <!-- Live Digital Screen Display (Moniepoint Style) -->
                    <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white rounded-2xl p-5 shadow-lg shadow-slate-950/15 space-y-3 relative overflow-hidden">
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>TOTAL AMOUNT DUE</span>
                            <span class="px-2 py-0.5 rounded-full bg-white/10 text-[10px] font-bold text-emerald-300">NGN (₦)</span>
                        </div>

                        <!-- Big Digital Digits -->
                        <div class="font-display font-extrabold text-3xl sm:text-4xl tracking-tight text-white flex items-baseline gap-1">
                            <span class="text-emerald-400 text-2xl font-bold">₦</span>
                            <span x-text="formatMoney(totalAmount)"></span>
                        </div>

                        <!-- Selected Item Banner inside Terminal Display -->
                        <template x-if="selectedVariant">
                            <div class="pt-2 border-t border-white/10 flex items-center justify-between text-xs text-slate-300">
                                <span class="truncate font-semibold max-w-[190px]" x-text="selectedVariant.name"></span>
                                <span class="font-mono text-emerald-300 font-bold" x-text="selectedVariant.size + ' &bull; ₦' + formatMoney(selectedVariant.price)"></span>
                            </div>
                        </template>

                        <template x-if="!selectedVariant">
                            <div class="pt-2 border-t border-white/10 text-xs text-slate-400 italic">
                                Tap a product on the left to select
                            </div>
                        </template>
                    </div>

                    <form method="POST" action="{{ route('admin.sales.store') }}" class="space-y-5">
                        @csrf

                        <!-- Hidden Inputs Submitted to Controller -->
                        <input type="hidden" name="variant_id" :value="selectedId">
                        <input type="hidden" name="amount" :value="totalAmount">
                        <input type="hidden" name="quantity" :value="quantity">
                        <input type="hidden" name="payment_method" :value="paymentMethod">
                        <input type="hidden" name="split_cash_amount" :value="splitCashAmount">
                        <input type="hidden" name="split_transfer_amount" :value="splitTransferAmount">
                        <input type="hidden" name="customer_phone" :value="customerPhone">

                        <!-- Quantity Selector Controls -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                                Quantity to Sell
                            </label>
                            <div class="flex items-center justify-between bg-slate-50 rounded-2xl p-1.5 shadow-inner">
                                <button
                                    type="button"
                                    @click="decrementQty()"
                                    :disabled="quantity <= 1"
                                    class="w-11 h-11 rounded-xl bg-white hover:bg-slate-100 disabled:opacity-40 font-extrabold text-base text-slate-800 flex items-center justify-center shadow-xs transition cursor-pointer"
                                >
                                    &minus;
                                </button>

                                <div class="text-center px-4">
                                    <span class="font-display font-extrabold text-xl text-slate-900" x-text="quantity"></span>
                                    <span class="text-[10px] text-slate-400 block font-semibold">item(s)</span>
                                </div>

                                <button
                                    type="button"
                                    @click="incrementQty()"
                                    class="w-11 h-11 rounded-xl bg-white hover:bg-slate-100 font-extrabold text-base text-slate-800 flex items-center justify-center shadow-xs transition cursor-pointer"
                                >
                                    +
                                </button>
                            </div>
                        </div>

                        <!-- Payment Method (With Split Pay Option) -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    Payment Method
                                </label>
                                <span class="text-[10px] text-slate-400 font-semibold" x-text="paymentMethod === 'split' ? 'Split Cash & Transfer' : 'Direct full charge'"></span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <!-- POS Card -->
                                <button
                                    type="button"
                                    @click="paymentMethod = 'pos_terminal'"
                                    :class="paymentMethod === 'pos_terminal' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/15' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 shadow-xs'"
                                    class="p-2.5 rounded-2xl text-center font-bold text-xs transition cursor-pointer flex flex-col items-center justify-center gap-1 min-h-[64px]"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    <span>POS Card</span>
                                </button>

                                <!-- Transfer -->
                                <button
                                    type="button"
                                    @click="paymentMethod = 'transfer'"
                                    :class="paymentMethod === 'transfer' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/15' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 shadow-xs'"
                                    class="p-2.5 rounded-2xl text-center font-bold text-xs transition cursor-pointer flex flex-col items-center justify-center gap-1 min-h-[64px]"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                    </svg>
                                    <span>Transfer</span>
                                </button>

                                <!-- Cash -->
                                <button
                                    type="button"
                                    @click="paymentMethod = 'cash'"
                                    :class="paymentMethod === 'cash' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/15' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 shadow-xs'"
                                    class="p-2.5 rounded-2xl text-center font-bold text-xs transition cursor-pointer flex flex-col items-center justify-center gap-1 min-h-[64px]"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>Cash</span>
                                </button>

                                <!-- Split Pay (Cash + Transfer) -->
                                <button
                                    type="button"
                                    @click="initSplit()"
                                    :class="paymentMethod === 'split' ? 'bg-indigo-900 text-white shadow-md shadow-indigo-900/20' : 'bg-indigo-50/70 hover:bg-indigo-100 text-indigo-900 shadow-xs'"
                                    class="p-2.5 rounded-2xl text-center font-bold text-xs transition cursor-pointer flex flex-col items-center justify-center gap-1 min-h-[64px]"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                    <span>Split Pay</span>
                                </button>
                            </div>

                            <!-- Split Amounts Inline Inputs -->
                            <div
                                x-show="paymentMethod === 'split'"
                                x-transition
                                class="mt-2.5 p-3.5 rounded-2xl bg-indigo-50/60 border border-indigo-100/60 space-y-2 text-xs"
                            >
                                <div class="flex items-center justify-between text-[11px] font-bold text-indigo-950">
                                    <span>Split Breakdown</span>
                                    <span>Total: ₦<span x-text="formatMoney(totalAmount)"></span></span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-600 mb-1">Cash Part (₦)</label>
                                        <input
                                            type="number"
                                            min="0"
                                            x-model="splitCashAmount"
                                            @input="onSplitCashInput()"
                                            placeholder="Cash"
                                            class="w-full px-3 py-2 rounded-xl bg-white text-xs font-bold text-slate-900 shadow-xs focus:outline-none focus:ring-2 focus:ring-indigo-600/30"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-600 mb-1">Transfer Part (₦)</label>
                                        <input
                                            type="number"
                                            min="0"
                                            x-model="splitTransferAmount"
                                            placeholder="Transfer"
                                            class="w-full px-3 py-2 rounded-xl bg-white text-xs font-bold text-slate-900 shadow-xs focus:outline-none focus:ring-2 focus:ring-indigo-600/30"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Phone Lookup, Name & Notes -->
                        <div class="space-y-2 pt-1 relative" @click.away="phoneMatches = []">
                            <!-- Customer Phone with Auto-Complete -->
                            <div class="relative">
                                <input
                                    type="tel"
                                    x-model="customerPhone"
                                    @input="onPhoneInput()"
                                    placeholder="Customer Phone (e.g. 08012345678) — WhatsApp autofill"
                                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 shadow-inner"
                                >
                                <!-- Quick Match Dropdown -->
                                <div
                                    x-show="phoneMatches.length > 0"
                                    class="absolute left-0 right-0 top-full mt-1 bg-white rounded-2xl shadow-xl z-30 p-2 space-y-1 max-h-40 overflow-y-auto"
                                    style="display: none;"
                                >
                                    <template x-for="c in phoneMatches" :key="c.phone">
                                        <button
                                            type="button"
                                            @click="selectCustomer(c)"
                                            class="w-full text-left p-2 rounded-xl hover:bg-slate-50 text-xs flex items-center justify-between transition cursor-pointer"
                                        >
                                            <span class="font-bold text-slate-800" x-text="c.name"></span>
                                            <span class="text-slate-400 font-mono text-[11px]" x-text="c.phone"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <input
                                type="text"
                                name="customer_name"
                                x-model="customerName"
                                placeholder="Customer Name (optional)"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 shadow-inner"
                            >

                            <input
                                type="text"
                                name="notes"
                                x-model="notes"
                                placeholder="Receipt notes / reference (optional)"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 shadow-inner"
                            >
                        </div>

                        <!-- Cashier Authorization Password -->
                        <div class="space-y-1.5 pt-1">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <span>Cashier Password</span>
                                </label>
                                <span class="text-[10px] text-slate-400 font-semibold">Required to charge</span>
                            </div>
                            <div class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    x-model="chargePassword"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Enter your password to authorize"
                                    class="w-full px-4 py-3 pr-11 rounded-xl bg-slate-50 text-xs font-semibold text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600/30 shadow-inner"
                                >
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-lg text-slate-400 hover:text-slate-600 transition cursor-pointer"
                                    tabindex="-1"
                                    title="Toggle password visibility"
                                >
                                    <template x-if="!showPassword">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </template>
                                    <template x-if="showPassword">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </template>
                                </button>
                            </div>
                        </div>

                        <!-- THE HUGE CHARGE BUTTON (Moniepoint Style) -->
                        <button
                            type="submit"
                            :disabled="!selectedId || totalAmount <= 0 || !chargePassword.trim()"
                            class="w-full py-4 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-extrabold text-sm sm:text-base uppercase tracking-wider transition shadow-xl shadow-emerald-600/25 flex items-center justify-center gap-2 cursor-pointer active:scale-[0.98]"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>CHARGE ₦</span><span x-text="formatMoney(totalAmount)"></span>
                            <span>&rarr;</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Walk-In Transactions Feed & Receipts -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="font-bold text-base text-slate-900">Recent Walk-In Transactions</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Click any record to print or view the customer receipt</p>
                </div>
                <span class="text-xs font-bold text-slate-400">Last 10 Records</span>
            </div>

            @if ($recentSales->count() > 0)
                <div class="divide-y divide-slate-100/80">
                    <template x-for="sale in recentSales" :key="sale.id">
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900" x-text="'#SR-' + sale.id"></span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="font-semibold text-slate-800 truncate" x-text="sale.item_description"></span>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    Qty: <strong x-text="sale.quantity"></strong> &bull;
                                    Recorded by <span x-text="sale.creator"></span> &bull;
                                    <span x-text="sale.date + ' ' + sale.time"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <span class="font-display font-extrabold text-sm sm:text-base text-slate-900">
                                    ₦<span x-text="formatMoney(sale.amount)"></span>
                                </span>

                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700" x-text="sale.payment_method"></span>

                                <!-- Print Receipt Trigger Button -->
                                <button
                                    type="button"
                                    @click="openReceipt(sale)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-bold transition shadow-xs cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    <span>Receipt</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-xs font-semibold text-slate-400">No walk-in sales recorded yet today.</div>
                </div>
            @endif
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- MONIEPOINT THERMAL POS RECEIPT MODAL                        -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div
            x-show="showReceipt"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div
                @click.away="showReceipt = false"
                class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl shadow-slate-950/20 space-y-5 animate-in fade-in zoom-in-95 duration-150"
            >
                <!-- Close Button & Status Pill -->
                <div class="flex items-center justify-between no-print">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-700">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Transaction Approved</span>
                    </div>
                    <button
                        type="button"
                        @click="showReceipt = false"
                        class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer"
                        title="Close"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Thermal Paper Slip Container (Optimized for 58mm & 80mm Mobile Thermal Printers) -->
                <div id="printable-receipt" class="bg-white rounded-2xl p-4 font-mono text-xs text-slate-900 space-y-3 shadow-md border-t-4 border-slate-900 mx-auto max-w-[280px]">
                    <!-- Store Header -->
                    <div class="text-center space-y-0.5">
                        <div class="font-extrabold text-sm uppercase tracking-wider text-black">{{ $storeName }}</div>
                        <div class="text-[10px] text-slate-500 font-sans print:text-black">{{ $tagline }}</div>
                        <div class="text-[9px] text-slate-400 print:text-black pt-0.5 tracking-tight">*** OFFICIAL STORE RECEIPT ***</div>
                    </div>

                    <!-- Monospace Dashed Divider -->
                    <div class="dashed-line border-b border-dashed border-slate-300 print:border-black my-2"></div>

                    <!-- Receipt Metadata -->
                    <div class="space-y-1 text-[11px] leading-tight">
                        <div class="flex justify-between">
                            <span class="text-slate-500 print:text-black">Receipt No:</span>
                            <strong class="font-bold text-black" x-text="'SR-' + (receiptData ? receiptData.id : '')"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 print:text-black">Date:</span>
                            <span class="text-black" x-text="receiptData ? receiptData.date : ''"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 print:text-black">Time:</span>
                            <span class="text-black" x-text="receiptData ? receiptData.time : ''"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 print:text-black">Cashier:</span>
                            <span class="text-black" x-text="receiptData ? receiptData.creator : 'Staff'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 print:text-black">Customer:</span>
                            <span class="text-black" x-text="receiptData?.notes && receiptData.notes.includes(' to ') ? receiptData.notes.split(' to ')[1].split(' |')[0] : 'Walk-in Customer'"></span>
                        </div>
                    </div>

                    <!-- Monospace Dashed Divider -->
                    <div class="dashed-line border-b border-dashed border-slate-300 print:border-black my-2"></div>

                    <!-- Purchased Item Details -->
                    <div class="space-y-1 leading-tight">
                        <div class="font-bold text-black uppercase" x-text="receiptData ? receiptData.item_description : ''"></div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-slate-600 print:text-black" x-text="receiptData ? (receiptData.quantity + ' x ₦' + formatMoney(receiptData.unit_price)) : ''"></span>
                            <strong class="font-bold text-black" x-text="'₦' + (receiptData ? formatMoney(receiptData.amount) : '')"></strong>
                        </div>
                    </div>

                    <!-- Monospace Dashed Divider -->
                    <div class="dashed-line border-b border-dashed border-slate-300 print:border-black my-2"></div>

                    <!-- Payment Summary -->
                    <div class="space-y-1 leading-tight">
                        <div class="flex justify-between items-baseline">
                            <span class="font-bold text-black text-xs">TOTAL PAID</span>
                            <span class="font-extrabold text-base text-black font-sans" x-text="'₦' + (receiptData ? formatMoney(receiptData.amount) : '')"></span>
                        </div>
                        <div class="flex justify-between text-[10px]">
                            <span class="text-slate-500 print:text-black">PAYMENT METHOD</span>
                            <span class="font-bold uppercase text-black" x-text="receiptData ? receiptData.payment_method : ''"></span>
                        </div>
                    </div>

                    <!-- Monospace Dashed Divider -->
                    <div class="dashed-line border-b border-dashed border-slate-300 print:border-black my-2"></div>

                    <!-- Footer -->
                    <div class="text-center text-[10px] text-slate-500 print:text-black space-y-0.5 pt-1 font-sans">
                        <p class="font-bold text-black">Thank you for your patronage!</p>
                        <p class="text-[9px]">Goods sold in good condition.</p>
                        <p class="text-[9px] pt-0.5 font-mono">*** {{ config('app.platform_name', 'ShopManager') }} POS ***</p>
                    </div>

                    <!-- Thermal Paper Feed Spacer (Prevents the tear blade from cutting the text on mobile Bluetooth printers) -->
                    <div class="thermal-feed-spacer hidden print:block" style="height: 18mm;"></div>
                </div>

                <!-- Receipt Modal Action Buttons (No Print) -->
                <div class="space-y-2 no-print pt-1">
                    <!-- Standard Print Slip (58mm / 80mm wireless printer) -->
                    <button
                        type="button"
                        @click="printReceipt()"
                        class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider transition shadow-md flex items-center justify-center gap-2 cursor-pointer active:scale-[0.99]"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Print Receipt (Wireless 58mm)</span>
                    </button>

                    <!-- Bluetooth Direct Link for Mobile (RawBT app on Android) -->
                    <a
                        :href="rawbtUrl"
                        class="w-full py-2.5 px-4 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 font-bold text-xs transition flex items-center justify-center gap-2"
                        title="Direct Bluetooth thermal print for mobile Android printers"
                    >
                        <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>Bluetooth Mobile Print (RawBT)</span>
                    </a>

                    <!-- Share on WhatsApp -->
                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        class="w-full py-2.5 px-4 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs transition flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                        </svg>
                        <span>Share on WhatsApp</span>
                    </a>

                    <button
                        type="button"
                        @click="showReceipt = false"
                        class="w-full py-2 px-3 text-slate-400 hover:text-slate-600 text-xs font-semibold text-center cursor-pointer"
                    >
                        Close &amp; Next Customer
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- DAILY CLOSEOUT / REGISTER TAKINGS MODAL (Z-REPORT)          -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div
            x-show="showTakingsModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div
                @click.away="showTakingsModal = false"
                class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl shadow-slate-950/20 space-y-5 animate-in fade-in zoom-in-95 duration-150"
            >
                <!-- Close Button & Header -->
                <div class="flex items-center justify-between no-print">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">Daily Closeout</h3>
                            <p class="text-[11px] text-slate-400">Shift register takings &amp; Z-Report</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="showTakingsModal = false"
                        class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer"
                        title="Close"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Printable Z-Report Slip Container (58mm Thermal Printer) -->
                <div id="printable-zreport" class="bg-white rounded-2xl p-4 font-mono text-xs text-slate-900 space-y-3 shadow-md border-t-4 border-slate-900 mx-auto max-w-[280px]">
                    <!-- Store Header -->
                    <div class="text-center space-y-0.5">
                        <div class="font-extrabold text-sm uppercase tracking-wider text-black">{{ $storeName }}</div>
                        <div class="text-[10px] text-slate-500 font-sans print:text-black">DAILY REGISTER TAKINGS</div>
                        <div class="text-[9px] text-slate-400 print:text-black pt-0.5 tracking-tight">*** OFFICIAL Z-REPORT ***</div>
                    </div>

                    <div class="dashed-line border-b border-dashed border-slate-300 print:border-black my-2"></div>

                    <!-- Metadata -->
                    <div class="space-y-1 text-[11px] leading-tight">
                        <div class="flex justify-between">
                            <span class="text-slate-500 print:text-black">Date:</span>
                            <span class="text-black">{{ $dailyTakings['date'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 print:text-black">Cashier:</span>
                            <span class="text-black">{{ Auth::user()->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 print:text-black">Sales Count:</span>
                            <strong class="text-black">{{ $dailyTakings['transactions_count'] }} transactions</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 print:text-black">Units Sold:</span>
                            <strong class="text-black">{{ $dailyTakings['units_sold'] }} items</strong>
                        </div>
                    </div>

                    <div class="dashed-line border-b border-dashed border-slate-300 print:border-black my-2"></div>

                    <!-- Payment Mode Breakdown -->
                    <div class="space-y-1.5 leading-tight">
                        <div class="font-bold text-black text-[11px] uppercase">Payment Breakdown</div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-slate-600 print:text-black">Cash Collected:</span>
                            <strong class="font-bold text-black">₦{{ number_format($dailyTakings['cash_total']) }}</strong>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-slate-600 print:text-black">Bank Transfers:</span>
                            <strong class="font-bold text-black">₦{{ number_format($dailyTakings['transfer_total']) }}</strong>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-slate-600 print:text-black">POS Terminal Cards:</span>
                            <strong class="font-bold text-black">₦{{ number_format($dailyTakings['card_total']) }}</strong>
                        </div>
                    </div>

                    <div class="dashed-line border-b border-dashed border-slate-300 print:border-black my-2"></div>

                    <!-- Total Takings -->
                    <div class="flex justify-between items-baseline pt-0.5 leading-tight">
                        <span class="font-bold text-black text-xs">TOTAL TAKINGS</span>
                        <span class="font-extrabold text-base text-black font-sans">₦{{ number_format($dailyTakings['total_amount']) }}</span>
                    </div>

                    <div class="dashed-line border-b border-dashed border-slate-300 print:border-black my-2"></div>

                    <!-- Footer -->
                    <div class="text-center text-[10px] text-slate-500 print:text-black space-y-0.5 pt-1 font-sans">
                        <p class="font-bold text-black">Register Balanced &amp; Closed</p>
                        <p class="text-[9px]">Shift timestamp: {{ now()->format('h:i A') }}</p>
                        <p class="text-[9px] pt-0.5 font-mono">*** {{ config('app.platform_name', 'ShopManager') }} POS ***</p>
                    </div>

                    <div class="thermal-feed-spacer hidden print:block" style="height: 18mm;"></div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2 no-print pt-1">
                    <button
                        type="button"
                        onclick="window.print()"
                        class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider transition shadow-md flex items-center justify-center gap-2 cursor-pointer active:scale-[0.99]"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Print Z-Report Slip (58mm)</span>
                    </button>

                    <button
                        type="button"
                        @click="showTakingsModal = false"
                        class="w-full py-2 px-3 text-slate-400 hover:text-slate-600 text-xs font-semibold text-center cursor-pointer"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Thermal Print Specific CSS for 58mm / 80mm Wireless Mobile Printers -->
    <style>
        @page {
            size: 58mm auto;
            margin: 0mm;
        }

        @media print {
            html, body {
                width: 58mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body * {
                visibility: hidden !important;
            }

            #printable-receipt, #printable-receipt *,
            #printable-zreport, #printable-zreport * {
                visibility: visible !important;
            }

            #printable-receipt,
            #printable-zreport {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 58mm !important;
                max-width: 58mm !important;
                padding: 2mm 3mm !important;
                margin: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
                font-family: 'Courier New', Courier, Monaco, monospace !important;
                font-size: 11px !important;
                line-height: 1.25 !important;
                box-shadow: none !important;
                border: none !important;
            }

            #printable-receipt .dashed-line,
            #printable-zreport .dashed-line {
                border-bottom: 1px dashed #000000 !important;
                margin: 4px 0 !important;
            }

            .thermal-feed-spacer {
                display: block !important;
                height: 18mm !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</x-admin-layout>
