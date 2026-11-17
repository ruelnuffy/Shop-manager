<x-app-layout>
    <div class="py-12 bg-[#fafafa] min-h-screen">
        <div class="max-w-xl mx-auto px-4 sm:px-6">
            <div class="bg-white rounded-3xl p-8 border border-black/[0.06] shadow-xl text-center space-y-6">
                <div class="mx-auto w-14 h-14 rounded-2xl bg-emerald-50 text-[#25D366] flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <div>
                    <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-700">Order #{{ $order->order_number }}</span>
                    <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 mt-1">
                        Confirm Your Transfer on WhatsApp
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2">
                        Total Amount: <strong class="text-slate-900">₦{{ number_format((float) $order->total_amount, 0) }}</strong>
                    </p>
                </div>

                @if ($tenant && $tenant->hasVerifiedBankDetails())
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-left space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Bank Name</span>
                            <span class="font-bold text-slate-900">{{ $tenant->bank_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Account Number</span>
                            <span class="font-mono font-extrabold text-emerald-700">{{ $tenant->bank_account_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Account Name</span>
                            <span class="font-bold text-slate-900">{{ $tenant->bank_account_name }}</span>
                        </div>
                    </div>
                @endif

                <div class="space-y-3 pt-2">
                    @if ($whatsappConfirmUrl !== '')
                        <a
                            href="{{ $whatsappConfirmUrl }}"
                            class="w-full py-4 px-6 rounded-full bg-[#25D366] hover:bg-[#1ebe5d] text-white font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/25 transition"
                        >
                            <span>Send Confirmation on WhatsApp &rarr;</span>
                        </a>
                    @endif

                    <a
                        href="{{ $tenant ? route('store.share', $tenant->slug) : route('products.index') }}"
                        class="block w-full py-3 px-6 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider transition"
                    >
                        Back to {{ $storeName }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($whatsappConfirmUrl !== '')
        <script>
            setTimeout(function () {
                window.location.href = @json($whatsappConfirmUrl);
            }, 1200);
        </script>
    @endif
</x-app-layout>
