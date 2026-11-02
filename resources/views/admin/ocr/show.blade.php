<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Committed Daybook Batch
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-mono">Batch #{{ $batch->id }}</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-slate-900 mt-1">
                    Batch #{{ $batch->id }} &bull; {{ $batch->original_filename }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Digitized and reconciled paper ledger records committed to inventory and sales ledger.
                </p>
            </div>
            <a href="{{ route('admin.ocr.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Batches</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Status Card -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $batch->status }}
                    </span>
                    <span class="text-xs text-slate-400">Uploaded on {{ $batch->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="text-sm font-semibold text-slate-800">
                    Uploaded by <span class="text-indigo-600">{{ $batch->creator?->name ?? 'Staff Member' }}</span>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="text-[11px] font-semibold text-slate-500 block">Total Digitized Batch Value</span>
                <div class="font-display font-extrabold text-2xl text-slate-900 font-mono mt-0.5">
                    ₦{{ number_format($batch->salesRecords->sum('amount'), 2) }}
                </div>
            </div>
        </div>

        <!-- Committed Sales Records Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                        Reconciled Sales Records ({{ $batch->salesRecords->count() }})
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">These lines have been added to the master sales ledger and adjusted in inventory.</p>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                    View in Financial Reports &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 pl-6">ID / Date</th>
                            <th class="py-3.5 px-4">Item Description</th>
                            <th class="py-3.5 px-4">Matched Inventory Variant</th>
                            <th class="py-3.5 px-4">Qty</th>
                            <th class="py-3.5 px-4">Unit Price</th>
                            <th class="py-3.5 px-4">Total Amount</th>
                            <th class="py-3.5 px-4 pr-6">Status / Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($batch->salesRecords as $record)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 pl-6">
                                    <div class="font-bold text-slate-900 font-mono">#{{ $record->id }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $record->date->format('M d, Y') }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $record->item_description }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($record->variant)
                                        <div class="font-semibold text-slate-800">{{ $record->variant->product->name }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            {{ $record->variant->size }} &bull; {{ $record->variant->color }} ({{ $record->variant->sku }})
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Unlinked item</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $record->quantity }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-mono">
                                    ₦{{ number_format($record->unit_price, 2) }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-sm text-slate-900 font-mono">
                                    ₦{{ number_format($record->amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 pr-6">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        {{ $record->status }}
                                    </span>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $record->notes }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    No records created for this batch yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
