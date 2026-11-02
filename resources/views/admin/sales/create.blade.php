<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-brand-charcoal">
                Direct In-Store POS Sale &bull; Walk-In Boutique
            </h1>
        </div>
    </x-slot>

    <div class="space-y-8" x-data="{
        selectedVariant: null,
        variants: {{ $variants->toJson() }},
        quantity: 1,
        amount: 0,
        updateAmount() {
            if (this.selectedVariant) {
                const price = this.selectedVariant.price_override ? this.selectedVariant.price_override : this.selectedVariant.product.base_price;
                this.amount = price * this.quantity;
            }
        },
        onVariantChange(e) {
            const id = e.target.value;
            this.selectedVariant = this.variants.find(v => v.id == id);
            this.updateAmount();
        }
    }">
        <!-- Walk-in POS Form -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-black/[0.06] shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-black/[0.06]">
                <div>
                    <h2 class="font-display font-bold text-lg uppercase tracking-tight text-brand-charcoal">
                        Record In-Store Sale
                    </h2>
                    <p class="text-xs text-brand-charcoal/50">Decrements inventory and logs in offline sales ledger instantly.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-black text-white">
                    Flagship POS
                </span>
            </div>

            <form method="POST" action="{{ route('admin.sales.store') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-6">
                    <!-- Variant Selector -->
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1.5">
                            Product &amp; Variant *
                        </label>
                        <select name="variant_id" @change="onVariantChange($event)" required class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs font-medium">
                            <option value="">Select Product &amp; Variant</option>
                            @foreach ($variants as $v)
                                <option value="{{ $v->id }}">
                                    {{ $v->product->name }} &bull; {{ $v->size }} ({{ $v->color }}) &mdash; Stock: {{ $v->stock_quantity }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quantity -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1.5">
                            Quantity *
                        </label>
                        <input type="number" min="1" name="quantity" x-model="quantity" @input="updateAmount()" required class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs font-bold">
                    </div>

                    <!-- Sale Amount -->
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1.5">
                            Total Sale Amount (NGN ₦) *
                        </label>
                        <input type="number" step="0.01" name="amount" x-model="amount" required class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs font-extrabold text-base">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1.5">
                            Payment Method *
                        </label>
                        <select name="payment_method" required class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs font-semibold">
                            <option value="pos_terminal">In-Store POS Card Terminal</option>
                            <option value="cash">Cash in Hand</option>
                            <option value="transfer">Direct Bank Transfer</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1.5">
                            Customer Name (Optional)
                        </label>
                        <input type="text" name="customer_name" placeholder="e.g. VIP Walk-in Client" class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1.5">
                            Sales Note / Receipt Ref
                        </label>
                        <input type="text" name="notes" placeholder="e.g. 10% VIP in-store courtesy" class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 px-6 rounded-full bg-black hover:bg-brand-pink text-white font-bold text-xs uppercase tracking-wider transition shadow-xl shadow-black/10 cursor-pointer">
                    Confirm &amp; Record Walk-in Sale
                </button>
            </form>
        </div>

        <!-- Recent Walk-In Sales Table -->
        <div class="bg-white rounded-3xl border border-black/[0.06] shadow-sm overflow-hidden">
            <div class="p-6 border-b border-black/[0.06] flex items-center justify-between">
                <h2 class="font-display font-bold text-lg uppercase tracking-tight text-brand-charcoal">
                    Recent In-Store Sales Ledger
                </h2>
                <span class="text-xs text-brand-charcoal/50">Last 10 walk-in records</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#fafafa] border-b border-black/[0.06] text-[10px] font-bold uppercase tracking-widest text-brand-charcoal/50">
                        <tr>
                            <th class="p-4 pl-6">ID / Date</th>
                            <th class="p-4">Item &amp; Description</th>
                            <th class="p-4">Qty</th>
                            <th class="p-4">Amount</th>
                            <th class="p-4">Recorded By</th>
                            <th class="p-4 pr-6">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/[0.06]">
                        @forelse ($recentSales as $sale)
                            <tr class="hover:bg-black/[0.01]">
                                <td class="p-4 pl-6">
                                    <strong class="text-brand-charcoal">SR-{{ $sale->id }}</strong>
                                    <div class="text-[10px] text-brand-charcoal/50 font-mono">{{ $sale->date->format('Y-m-d') }}</div>
                                </td>
                                <td class="p-4 font-bold text-brand-charcoal">
                                    {{ $sale->item_description }}
                                </td>
                                <td class="p-4 font-bold">
                                    {{ $sale->quantity }}
                                </td>
                                <td class="p-4 font-bold text-brand-charcoal">
                                    ₦{{ number_format($sale->amount, 0) }}
                                </td>
                                <td class="p-4 text-brand-charcoal/70">
                                    {{ $sale->creator?->name ?? 'Staff' }}
                                </td>
                                <td class="p-4 pr-6 text-brand-charcoal/60 text-[11px]">
                                    {{ $sale->notes }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-brand-charcoal/50">
                                    No offline walk-in sales recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
