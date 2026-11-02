<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paystack Checkout Simulation &bull; Jessi's ClassiQ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0f172a] text-white font-sans min-h-screen flex items-center justify-center p-4 selection:bg-brand-pink selection:text-white">
    <div class="max-w-md w-full bg-white text-brand-charcoal rounded-3xl p-8 shadow-2xl space-y-6 relative overflow-hidden">
        <!-- Paystack Brand Header -->
        <div class="flex items-center justify-between pb-4 border-b border-black/[0.08]">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-[#0aa564] text-white flex items-center justify-center font-bold text-base">
                    P
                </div>
                <div>
                    <div class="font-bold text-sm text-brand-charcoal">Paystack Checkout</div>
                    <div class="text-[10px] text-brand-charcoal/50 uppercase tracking-wider font-semibold">Paystack Sandbox Environment</div>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">
                Simulated
            </span>
        </div>

        <!-- Transaction Details -->
        <div class="bg-[#f8fafc] rounded-2xl p-5 border border-black/[0.05] space-y-2.5 text-xs">
            <div class="flex justify-between">
                <span class="text-brand-charcoal/60">Merchant:</span>
                <strong class="text-brand-charcoal">Jessi's ClassiQ Store</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-brand-charcoal/60">Customer Email:</span>
                <strong class="text-brand-charcoal">{{ $email }}</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-brand-charcoal/60">Transaction Reference:</span>
                <code class="text-[11px] font-mono bg-white px-2 py-0.5 rounded border border-black/[0.06] text-brand-charcoal">{{ $reference }}</code>
            </div>
            <div class="flex justify-between pt-2 border-t border-black/[0.06] text-sm">
                <span class="font-bold text-brand-charcoal">Amount Due:</span>
                <span class="font-extrabold text-xl text-brand-charcoal">₦{{ number_format($amount, 2) }}</span>
            </div>
        </div>

        <p class="text-xs text-brand-charcoal/60 leading-relaxed text-center">
            You are testing in Paystack simulation mode. Click below to simulate an approved payment callback. Server-side verification and automated stock decrement will execute.
        </p>

        <!-- Simulation Actions -->
        <div class="space-y-3 pt-2">
            <a href="{{ $callbackUrl }}" class="w-full py-3.5 px-6 rounded-full bg-[#0aa564] hover:bg-[#088c54] text-white font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition duration-150 shadow-lg shadow-[#0aa564]/20 text-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Authorize &amp; Pay ₦{{ number_format($amount, 0) }}</span>
            </a>

            <a href="{{ route('cart.index') }}" class="w-full py-3 px-6 rounded-full bg-[#f1f5f9] hover:bg-[#e2e8f0] text-brand-charcoal/70 font-bold text-xs uppercase tracking-wider flex items-center justify-center transition duration-150 text-center">
                Cancel Transaction
            </a>
        </div>

        <div class="pt-2 text-center text-[10px] text-brand-charcoal/40">
            Paystack API Integration &bull; 256-Bit SSL Encryption
        </div>
    </div>
</body>
</html>
