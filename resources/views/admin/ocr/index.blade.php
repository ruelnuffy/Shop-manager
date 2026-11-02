<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        </svg>
                        Paper-to-Cloud OCR
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium">{{ $totalBatches }} Batches</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-slate-900 mt-1">
                    Paper Ledger OCR Imports
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Digitize physical store daybooks, camera scans, and handwritten offline retail sales sheets.
                </p>
            </div>
            <a href="{{ route('admin.ocr.create') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-indigo-600 text-xs font-semibold transition shadow-xs inline-flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Upload Paper Ledger</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 block">Total Ledger Batches</span>
                <div class="font-display font-extrabold text-2xl text-slate-900 mt-1">{{ $totalBatches }}</div>
                <span class="text-[11px] text-slate-400 mt-1 block">Paper sheets scanned &amp; parsed</span>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 block">Digitized Sales Records</span>
                <div class="font-display font-extrabold text-2xl text-slate-900 mt-1">{{ $importedRecordsCount }}</div>
                <span class="text-[11px] text-slate-400 mt-1 block">Reconciled lines committed to system</span>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 block">Recovered Revenue</span>
                <div class="font-display font-extrabold text-2xl text-slate-900 mt-1 font-mono">₦{{ number_format($totalDigitizedRevenue, 0) }}</div>
                <span class="text-[11px] text-slate-400 mt-1 block">Offline retail revenue digitized</span>
            </div>
        </div>

        <!-- Batches Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                        Uploaded Ledger Batches
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Review and reconcile scanned physical daybooks and sales sheets.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 pl-6">Batch / Upload Date</th>
                            <th class="py-3.5 px-4">Filename</th>
                            <th class="py-3.5 px-4">Uploaded By</th>
                            <th class="py-3.5 px-4">Records Count</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($batches as $b)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 pl-6">
                                    <div class="font-bold text-slate-900">Batch #{{ $b->id }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $b->created_at->format('M d, Y H:i') }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs text-slate-700">
                                    {{ $b->original_filename ?? 'ledger_sheet.jpg' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    {{ $b->creator?->name ?? 'Staff Member' }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    @if ($b->status === 'imported')
                                        {{ $b->salesRecords->count() }} sales records
                                    @else
                                        {{ count($b->parsed_data ?? []) }} detected rows
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($b->status === 'imported')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Imported &amp; Reconciled
                                        </span>
                                    @elseif ($b->status === 'processed')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Awaiting Review
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ $b->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 pr-6 text-right space-x-2">
                                    @if ($b->status !== 'imported')
                                        <a href="{{ route('admin.ocr.review', $b->id) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-semibold transition shadow-xs">
                                            Reconcile &rarr;
                                        </a>
                                    @else
                                        <a href="{{ route('admin.ocr.show', $b->id) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                            View Details
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800">No paper ledgers uploaded yet</p>
                                    <p class="text-xs text-slate-400 mt-1">Click "Upload Paper Ledger" to scan physical books.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($batches->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $batches->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
