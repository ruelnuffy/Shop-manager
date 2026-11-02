<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        </svg>
                        Daybook Digitisation
                    </span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-slate-900 mt-1">
                    Upload Paper Ledger Sheet
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Photograph physical sales books or upload scans to automatically extract lines and decrement stock.
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

    <div class="max-w-3xl bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <form method="POST" action="{{ route('admin.ocr.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="{
            hasFile: false,
            fileName: '',
            previewUrl: null,
            handleFileSelect(e) {
                const file = e.target.files[0];
                if (file) {
                    this.hasFile = true;
                    this.fileName = file.name;
                    this.previewUrl = URL.createObjectURL(file);
                }
            }
        }">
            @csrf

            <!-- Upload Area -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-700">
                    Photograph of Physical Daybook / Sales Sheet *
                </label>

                <div class="border-2 border-dashed border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/20 rounded-2xl p-8 sm:p-12 text-center transition cursor-pointer bg-slate-50/50" @click="$refs.fileInput.click()">
                    <input type="file" x-ref="fileInput" name="ledger_image" accept="image/*,application/pdf" class="hidden" @change="handleFileSelect($event)">

                    <template x-if="!previewUrl">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto border border-indigo-100">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Click or drag paper ledger photo here</p>
                                <p class="text-[11px] text-slate-400 mt-1">High-contrast phone pictures or flatbed scans (PNG, JPG, WebP up to 10MB)</p>
                            </div>
                        </div>
                    </template>

                    <template x-if="previewUrl">
                        <div class="space-y-3">
                            <img :src="previewUrl" class="max-h-56 mx-auto rounded-xl object-contain border border-slate-200 shadow-xs">
                            <div class="text-xs font-semibold text-slate-800 font-mono" x-text="fileName"></div>
                            <span class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800">Click to choose different image</span>
                        </div>
                    </template>
                </div>
                @error('ledger_image') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Fallback Raw Text Accordion -->
            <div class="p-4 sm:p-5 bg-slate-50/70 rounded-xl border border-slate-200/80 space-y-3" x-data="{ showText: false }">
                <div class="flex items-center justify-between cursor-pointer select-none" @click="showText = !showText">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Manual Raw Ledger Text (Optional Fallback)</span>
                    </div>
                    <span class="text-xs font-mono text-slate-400" x-text="showText ? '▲ Collapse' : '▼ Expand'"></span>
                </div>

                <div x-show="showText" x-cloak class="pt-2">
                    <p class="text-[11px] text-slate-500 mb-2">
                        If camera OCR is unreadable due to handwriting, paste or type the raw text directly:
                    </p>
                    <textarea name="raw_text_override" rows="4" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-mono leading-relaxed text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="1. Blush Silk Evening Gown (S) | Qty: 1 | Price: 48500&#10;2. ClassiQ Quilted Leather Crossbody | Qty: 2 | Total: 76000"></textarea>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full py-3 px-6 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-semibold text-xs tracking-wide transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                <span>Scan &amp; Extract Tabular Data &rarr;</span>
            </button>
        </form>
    </div>
</x-admin-layout>
