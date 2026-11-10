<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h1 class="font-display font-extrabold text-xl sm:text-2xl text-slate-900 tracking-tight">
                    Direct In-Store POS Sale
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    Fast counter checkout &bull; Instant inventory deduction &amp; receipt
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>POS Terminal Online</span>
                </span>
            </div>
        </div>
    </x-slot>

    @php
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
    @endphp

    <div
        class="space-y-8"
        x-data="{
            variants: {{ json_encode($variantsJson) }},
            search: '',
            selectedId: {{ $variants->first()?->id ?? 'null' }},
            quantity: 1,
            paymentMethod: 'pos_terminal',
            customerName: '',
            notes: '',
            customAmount: null,

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
            }
        }"
    >
        <!-- Main Moniepoint POS Split Screen -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN: Quick Tap Catalog (7 Cols on desktop) -->
            <div class="lg:col-span-7 space-y-4">
                <!-- Search & Filters -->
                <div class="bg-white rounded-2xl p-4 shadow-xl shadow-slate-900/5 flex items-center gap-3">
                    <span class="text-slate-400 text-sm pl-1">🔍</span>
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
                        class="text-xs text-slate-400 hover:text-slate-600 font-bold"
                    >✕</button>
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
                                            class="w-5 h-5 rounded-full bg-emerald-400 text-slate-950 text-[10px] font-extrabold flex items-center justify-center shrink-0"
                                        >✓</span>
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
                        <span class="text-2xl mb-2 block">🔍</span>
                        <div class="font-bold text-sm text-slate-800">No matching items found</div>
                        <p class="text-xs text-slate-400 mt-1">Try a different search query or clear the filter.</p>
                    </div>
                @else
                    <div class="bg-white rounded-3xl p-10 text-center shadow-xl shadow-slate-900/5 space-y-3">
                        <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 text-xl flex items-center justify-center mx-auto">📦</div>
                        <div class="font-bold text-base text-slate-900">No active stock available to sell</div>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Add products with active inventory to begin ringing up sales at the counter.
                        </p>
                        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-900 text-white text-xs font-bold shadow-md">
                            + Add New Product
                        </a>
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

                        <!-- Payment Method (The 3 Big Moniepoint Buttons) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                                Payment Method
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <!-- POS Card -->
                                <button
                                    type="button"
                                    @click="paymentMethod = 'pos_terminal'"
                                    :class="paymentMethod === 'pos_terminal' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/15' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 shadow-xs'"
                                    class="p-3 rounded-2xl text-center font-bold text-xs transition cursor-pointer flex flex-col items-center justify-center gap-1 min-h-[64px]"
                                >
                                    <span class="text-base">💳</span>
                                    <span>POS Card</span>
                                </button>

                                <!-- Transfer -->
                                <button
                                    type="button"
                                    @click="paymentMethod = 'transfer'"
                                    :class="paymentMethod === 'transfer' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/15' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 shadow-xs'"
                                    class="p-3 rounded-2xl text-center font-bold text-xs transition cursor-pointer flex flex-col items-center justify-center gap-1 min-h-[64px]"
                                >
                                    <span class="text-base">🏦</span>
                                    <span>Transfer</span>
                                </button>

                                <!-- Cash -->
                                <button
                                    type="button"
                                    @click="paymentMethod = 'cash'"
                                    :class="paymentMethod === 'cash' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/15' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 shadow-xs'"
                                    class="p-3 rounded-2xl text-center font-bold text-xs transition cursor-pointer flex flex-col items-center justify-center gap-1 min-h-[64px]"
                                >
                                    <span class="text-base">💵</span>
                                    <span>Cash</span>
                                </button>
                            </div>
                        </div>

                        <!-- Customer Details (Clean & Minimal) -->
                        <div class="space-y-2 pt-1">
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

                        <!-- THE HUGE CHARGE BUTTON (Moniepoint Style) -->
                        <button
                            type="submit"
                            :disabled="!selectedId || totalAmount <= 0"
                            class="w-full py-4 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-extrabold text-sm sm:text-base uppercase tracking-wider transition shadow-xl shadow-emerald-600/25 flex items-center justify-center gap-2 cursor-pointer active:scale-[0.98]"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            <span>CHARGE ₦</span><span x-text="formatMoney(totalAmount)"></span>
                            <span>&rarr;</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Walk-In Transactions Slip Feed -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="font-bold text-base text-slate-900">Recent Walk-In Transactions</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Live register log of sales completed in store</p>
                </div>
                <span class="text-xs font-bold text-slate-400">Last 10 Records</span>
            </div>

            @if ($recentSales->count() > 0)
                <div class="divide-y divide-slate-100/80">
                    @foreach ($recentSales as $sale)
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900">#SR-{{ $sale->id }}</span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="font-semibold text-slate-800 truncate">{{ $sale->item_description }}</span>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    Qty: <strong>{{ $sale->quantity }}</strong> &bull;
                                    Recorded by {{ $sale->creator?->name ?? 'Staff' }} &bull;
                                    {{ $sale->created_at->diffForHumans() }}
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <span class="font-display font-extrabold text-sm sm:text-base text-slate-900">
                                    ₦{{ number_format($sale->amount, 0) }}
                                </span>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                                    {{ str_replace('_', ' ', $sale->notes ? explode(' |', explode('via ', $sale->notes)[1] ?? 'sale')[0] : 'POS') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <span class="text-2xl mb-1 block">🧾</span>
                    <div class="text-xs font-semibold text-slate-400">No walk-in sales recorded yet today.</div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
