<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Side-by-Side Reconciliation
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium">Batch #{{ $batch->id }}</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-brand-charcoal text-slate-900 mt-1">
                    Reconcile Paper Ledger &bull; Batch #{{ $batch->id }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Match extracted ledger lines to inventory variants and verify cash/POS totals.
                </p>
            </div>
            <a href="{{ route('admin.ocr.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Cancel &amp; Return</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        rows: {{ json_encode(old('rows', $batch->parsed_data ?? [])) }},
        variants: {{ json_encode($variants->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->product->name . ' • ' . $v->size . ' (' . $v->color . ') — Stock: ' . $v->stock_quantity,
            'price' => (float) ($v->price_override ?? $v->product->base_price)
        ])) }},
        addRow() {
            this.rows.push({
                date: '{{ now()->format('Y-m-d') }}',
                item_description: '',
                product_variant_id: '',
                quantity: 1,
                unit_price: 0,
                amount: 0,
                notes: 'Manual entry'
            });
        },
        removeRow(index) {
            if (this.rows.length > 1) {
                this.rows.splice(index, 1);
            }
        },
        onVariantSelected(index, event) {
            const vId = event.target.value;
            const found = this.variants.find(v => v.id == vId);
            if (found) {
                this.rows[index].unit_price = found.price;
                this.rows[index].amount = found.price * (this.rows[index].quantity || 1);
            }
        },
        recalcAmount(index) {
            const qty = this.rows[index].quantity || 1;
            const price = this.rows[index].unit_price || 0;
            this.rows[index].amount = qty * price;
        },
        totalAmount() {
            return this.rows.reduce((acc, r) => acc + (parseFloat(r.amount) || 0), 0);
        }
    }">
        <form method="POST" action="{{ route('admin.ocr.commit', $batch->id) }}" class="space-y-6">
            @csrf

        @if (in_array($batch->status, ['pending', 'processing']))
            <div class="bg-white rounded-2xl p-12 border border-slate-200/80 shadow-xs text-center max-w-xl mx-auto space-y-4"
                 x-data="{ timer: null }"
                 x-init="timer = setInterval(() => window.location.reload(), 3000)">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto border border-indigo-100">
                    <svg class="w-7 h-7 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h2 class="font-bold text-lg text-slate-900">
                    Processing Paper Ledger...
                </h2>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Our OCR engine is currently digitising and parsing the daybook sheet. This page will automatically update once processing is complete.
                </p>
                <div class="text-[11px] font-mono text-slate-400 pt-2">
                    Batch Status: <span class="uppercase font-bold text-indigo-600">{{ $batch->status }}</span>
                </div>
            </div>
        @elseif ($batch->status === 'failed')
            <div class="bg-white rounded-2xl p-12 border border-rose-200 shadow-xs text-center max-w-xl mx-auto space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto border border-rose-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="font-bold text-lg text-rose-900">
                    OCR Processing Failed
                </h2>
                <p class="text-xs text-slate-600 leading-relaxed">
                    {{ $batch->error_message ?? 'Unable to extract legible text from this paper ledger upload. Please try re-uploading a clearer photo or provide manual text input.' }}
                </p>
                <div class="pt-4 flex items-center justify-center gap-4">
                    <a href="{{ route('admin.ocr.create') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-indigo-600 transition shadow-xs">
                        Upload Another Sheet
                    </a>
                </div>
            </div>
        @else
            <!-- Side by Side Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Left: Original Scanned Image & Raw Text Inspector -->
                <div class="lg:col-span-4 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-4 sticky top-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-800">
                            Original Scanned Daybook
                        </h3>
                        <span class="text-[10px] uppercase font-mono text-slate-400">Raw Source</span>
                    </div>

                    <div class="rounded-xl overflow-hidden bg-slate-50 border border-slate-200 max-h-96 overflow-y-auto">
                        @if ($batch->image_path)
                            <img src="{{ route('admin.ocr.image', $batch->id) }}" alt="Scanned Ledger Sheet" class="w-full h-auto object-contain">
                        @else
                            <div class="p-8 text-center text-xs text-slate-400">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="font-semibold text-slate-700">Digital Daybook Sheet</p>
                                <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $batch->original_filename }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Raw OCR Output Transcript -->
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-semibold text-slate-600">
                            Extracted Raw Text:
                        </label>
                        <div class="p-3 bg-slate-50/70 rounded-xl text-[11px] font-mono max-h-40 overflow-y-auto whitespace-pre-wrap text-slate-700 border border-slate-200/80">
{{ $batch->raw_text }}
                        </div>
                    </div>
                </div>

                <!-- Right: Editable Reconciliation Table -->
                <div class="lg:col-span-8 bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="font-display font-bold text-lg uppercase tracking-tight text-brand-charcoal text-slate-900">
                                Extracted Ledger Lines
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Review, match to inventory variants, and verify quantities and totals.</p>
                        </div>
                        <button type="button" @click="addRow()" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition shadow-xs flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Add Line Item</span>
                        </button>
                    </div>

                    <!-- Rows list -->
                    <div class="space-y-3.5">
                        <template x-for="(row, idx) in rows" :key="idx">
                            <div class="p-4 rounded-xl bg-slate-50/60 border border-slate-200/80 space-y-3 relative hover:border-slate-300 transition">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400" x-text="'Line #' + (idx + 1)"></span>
                                    <button type="button" @click="removeRow(idx)" class="inline-flex items-center gap-1 text-xs text-rose-500 hover:text-rose-700 font-semibold" title="Delete Row">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>Remove</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                                    <!-- Date -->
                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-semibold text-slate-600 mb-1">Date *</label>
                                        <input type="date" :name="'rows[' + idx + '][date]'" x-model="row.date" required class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                                    </div>

                                    <!-- Description -->
                                    <div class="sm:col-span-5">
                                        <label class="block text-[10px] font-semibold text-slate-600 mb-1">Item Description *</label>
                                        <input type="text" :name="'rows[' + idx + '][item_description]'" x-model="row.item_description" required placeholder="Item name from ledger" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                                    </div>

                                    <!-- Inventory Variant Match -->
                                    <div class="sm:col-span-4">
                                        <label class="block text-[10px] font-semibold text-slate-600 mb-1">Inventory Match (Stock Link)</label>
                                        <select :name="'rows[' + idx + '][product_variant_id]'" x-model="row.product_variant_id" @change="onVariantSelected(idx, $event)" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
                                            <option value="">No Stock Decrement</option>
                                            <template x-for="v in variants" :key="v.id">
                                                <option :value="v.id" :selected="row.product_variant_id == v.id" x-text="v.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                                    <!-- Quantity -->
                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-semibold text-slate-600 mb-1">Quantity *</label>
                                        <input type="number" min="1" :name="'rows[' + idx + '][quantity]'" x-model="row.quantity" @input="recalcAmount(idx)" required class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800">
                                    </div>

                                    <!-- Unit Price -->
                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-semibold text-slate-600 mb-1">Unit Price (₦)</label>
                                        <input type="number" step="0.01" :name="'rows[' + idx + '][unit_price]'" x-model="row.unit_price" @input="recalcAmount(idx)" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 font-mono">
                                    </div>

                                    <!-- Total Amount -->
                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-semibold text-slate-600 mb-1">Total Amount (₦) *</label>
                                        <input type="number" step="0.01" :name="'rows[' + idx + '][amount]'" x-model="row.amount" required class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-extrabold text-slate-900 font-mono">
                                    </div>

                                    <!-- Notes -->
                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-semibold text-slate-600 mb-1">Notes / Method</label>
                                        <input type="text" :name="'rows[' + idx + '][notes]'" x-model="row.notes" placeholder="e.g. Paid POS" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-700">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Summary & Commit Button -->
                    <div class="pt-5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="text-[11px] font-semibold text-slate-500 block">Total Ledger Batch Sum</span>
                            <div class="font-display font-extrabold text-2xl text-slate-900 font-mono mt-0.5">
                                ₦<span x-text="Number(totalAmount()).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>
                            </div>
                        </div>

                        <button type="submit" class="py-2.5 px-6 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-semibold text-xs tracking-wide transition shadow-xs cursor-pointer flex items-center gap-1.5">
                            <span>Confirm &amp; Reconcile Into Inventory &rarr;</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
        </form>
    </div>
</x-admin-layout>
