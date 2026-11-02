<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-brand-charcoal">
                    Stock Audit &amp; Movements
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Quick Stock Adjustment Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-black/[0.06] shadow-sm space-y-6">
            <h2 class="font-display font-bold text-lg uppercase tracking-tight text-brand-charcoal pb-4 border-b border-black/[0.06]">
                Record Stock Adjustment
            </h2>

            <form method="POST" action="{{ route('admin.stock.adjust') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                @csrf
                <div class="sm:col-span-4">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1">Product Variant *</label>
                    <select name="variant_id" required class="w-full px-3 py-2 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs">
                        <option value="">Select Variant (Current Stock)</option>
                        @foreach ($allVariants as $v)
                            <option value="{{ $v->id }}">
                                {{ $v->product->name }} &bull; {{ $v->size }}/{{ $v->color }} (Stock: {{ $v->stock_quantity }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1">Action *</label>
                    <select name="type" required class="w-full px-3 py-2 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs">
                        <option value="in">Add (+ Restock)</option>
                        <option value="out">Remove (- Damage/Loss)</option>
                        <option value="adjustment">Set Exact Stock</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1">Quantity *</label>
                    <input type="number" min="1" name="quantity" required placeholder="Qty" class="w-full px-3 py-2 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1">Reason *</label>
                    <select name="reason" required class="w-full px-3 py-2 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs">
                        <option value="restock">New Stock Delivery</option>
                        <option value="manual_audit">Inventory Audit Correction</option>
                        <option value="damage">Damaged Item</option>
                        <option value="return">Customer Return</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider transition">
                        Apply Adjust
                    </button>
                </div>
            </form>
        </div>

        <!-- Audit Movements Log Table -->
        <div class="bg-white rounded-3xl border border-black/[0.06] shadow-sm overflow-hidden">
            <div class="p-6 border-b border-black/[0.06] flex items-center justify-between">
                <h2 class="font-display font-bold text-lg uppercase tracking-tight text-brand-charcoal">
                    Immutable Audit Trail
                </h2>
                <span class="text-xs text-brand-charcoal/50 font-medium">
                    Total Movements: {{ $movements->total() }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#fafafa] border-b border-black/[0.06] text-[10px] font-bold uppercase tracking-widest text-brand-charcoal/50">
                        <tr>
                            <th class="p-4 pl-6">Date / Time</th>
                            <th class="p-4">Item &amp; Variant</th>
                            <th class="p-4">Movement</th>
                            <th class="p-4">Qty</th>
                            <th class="p-4">Prev &rarr; New</th>
                            <th class="p-4">Reason</th>
                            <th class="p-4 pr-6">Logged By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/[0.06]">
                        @forelse ($movements as $m)
                            <tr class="hover:bg-black/[0.01]">
                                <td class="p-4 pl-6 font-mono text-[11px] text-brand-charcoal/60">
                                    {{ $m->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="p-4 font-semibold text-brand-charcoal">
                                    {{ $m->variant?->product?->name }}
                                    <div class="text-[10px] text-brand-charcoal/50 font-mono">
                                        {{ $m->variant?->sku }} ({{ $m->variant?->size }} / {{ $m->variant?->color }})
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $m->type === 'in' ? 'bg-emerald-100 text-emerald-800' : ($m->type === 'out' ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800') }}">
                                        {{ $m->type }}
                                    </span>
                                </td>
                                <td class="p-4 font-bold font-mono">
                                    {{ $m->type === 'in' ? '+' : '-' }}{{ $m->quantity }}
                                </td>
                                <td class="p-4 font-mono text-brand-charcoal/70">
                                    {{ $m->previous_stock }} &rarr; <strong class="text-brand-charcoal">{{ $m->new_stock }}</strong>
                                </td>
                                <td class="p-4 text-brand-charcoal/80">
                                    {{ str_replace('_', ' ', $m->reason) }}
                                </td>
                                <td class="p-4 pr-6 text-brand-charcoal/60">
                                    {{ $m->creator?->name ?? 'System' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-brand-charcoal/50">
                                    No stock movements recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-black/[0.06]">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
