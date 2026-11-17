<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-charcoal/50 block mb-1">
                    Secure Payment
                </span>
                <h1 class="font-display font-extrabold text-3xl sm:text-4xl uppercase tracking-tight text-brand-charcoal">
                    CHECKOUT
                </h1>
            </div>
            <a href="{{ route('cart.index') }}" class="text-xs font-bold uppercase tracking-wider text-brand-charcoal hover:text-brand-pink underline underline-offset-4">
                &larr; Back to Shopping Bag
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900">✕</button>
                </div>
            @endif

            <form method="POST" action="{{ route('checkout.process') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                @csrf

                <!-- Left: Shipping Information Form -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-black/[0.06] shadow-sm space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-black/[0.06]">
                            <h2 class="font-display font-extrabold text-xl uppercase tracking-tight text-brand-charcoal">
                                1. Delivery &amp; Contact Details
                            </h2>
                            <span class="text-[10px] font-bold uppercase text-brand-charcoal/40">Step 1 of 2</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1.5">
                                    Recipient Full Name *
                                </label>
                                <input type="text" name="shipping_name" value="{{ old('shipping_name', $user?->name) }}" required class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:border-black/20 focus:bg-white focus:ring-0 rounded-xl text-xs font-medium text-brand-charcoal" placeholder="e.g. Amaka Johnson">
                                @error('shipping_name') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1.5">
                                    Email Address (For Receipt) *
                                </label>
                                <input type="email" name="shipping_email" value="{{ old('shipping_email', $user?->email) }}" required class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:border-black/20 focus:bg-white focus:ring-0 rounded-xl text-xs font-medium text-brand-charcoal" placeholder="amaka@example.com">
                                @error('shipping_email') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1.5">
                                    Phone Number (For Courier) *
                                </label>
                                <input type="tel" name="shipping_phone" value="{{ old('shipping_phone', $user?->phone) }}" required class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:border-black/20 focus:bg-white focus:ring-0 rounded-xl text-xs font-medium text-brand-charcoal" placeholder="+234 801 234 5678">
                                @error('shipping_phone') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1.5">
                                    State / City *
                                </label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city', $user?->city ?? '') }}" required class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:border-black/20 focus:bg-white focus:ring-0 rounded-xl text-xs font-medium text-brand-charcoal" placeholder="e.g. Lagos, Abuja">
                                @error('shipping_city') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1.5">
                                Full Street Address *
                            </label>
                            <textarea name="shipping_address" rows="3" required class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:border-black/20 focus:bg-white focus:ring-0 rounded-xl text-xs font-medium text-brand-charcoal" placeholder="Plot number, street name, apartment or landmark...">{{ old('shipping_address', $user?->address) }}</textarea>
                            @error('shipping_address') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 mb-1.5">
                                Delivery Notes (Optional)
                            </label>
                            <input type="text" name="notes" value="{{ old('notes') }}" class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:border-black/20 focus:bg-white focus:ring-0 rounded-xl text-xs font-medium text-brand-charcoal" placeholder="Gate instructions, preferred drop-off time...">
                        </div>
                    </div>
                </div>

                <!-- Right: Order Review & Paystack Authorization -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-black/[0.06] shadow-sm space-y-6 sticky top-24">
                        <h2 class="font-display font-extrabold text-xl uppercase tracking-tight text-brand-charcoal pb-4 border-b border-black/[0.06]">
                            2. Review &amp; Pay
                        </h2>

                        <!-- Items preview list -->
                        <div class="max-h-60 overflow-y-auto divide-y divide-black/[0.04] pr-2">
                            @foreach ($items as $item)
                                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $checkItemImg = $item['image'] ?? null;
                                            $checkItemImgSrc = !empty($checkItemImg)
                                                ? (str_starts_with($checkItemImg, 'http://') || str_starts_with($checkItemImg, 'https://')
                                                    ? $checkItemImg
                                                    : asset(ltrim($checkItemImg, '/')))
                                                : null;
                                        @endphp
                                        <div class="w-10 h-12 rounded-lg bg-[#f3f3f3] overflow-hidden shrink-0 border border-black/[0.04] relative flex items-center justify-center">
                                            @if ($checkItemImgSrc)
                                                <img
                                                    src="{{ $checkItemImgSrc }}"
                                                    alt="{{ $item['product_name'] }}"
                                                    class="w-full h-full object-cover"
                                                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                                                >
                                                <div class="hidden w-full h-full flex items-center justify-center bg-[#f3f3f3] text-[8px] font-bold text-brand-charcoal/40">
                                                    BAG
                                                </div>
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-[#f3f3f3] text-[8px] font-bold text-brand-charcoal/40">
                                                    BAG
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-brand-charcoal">{{ $item['product_name'] }}</div>
                                            <div class="text-[11px] text-brand-charcoal/50">
                                                Qty: {{ $item['quantity'] }} &bull; {{ $item['size'] }} / {{ $item['color'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="font-bold text-brand-charcoal">
                                        ₦{{ number_format($item['price'] * $item['quantity'], 0) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-4 border-t border-black/[0.06] space-y-2 text-xs text-brand-charcoal/70">
                            <div class="flex justify-between">
                                <span>Bag Subtotal</span>
                                <span class="font-bold text-brand-charcoal">₦{{ number_format($subtotal, 0) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Flat Courier Fee</span>
                                <span class="font-bold text-brand-charcoal">₦{{ number_format($shippingFee, 0) }}</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-black/[0.06] text-sm">
                                <span class="font-display font-extrabold uppercase text-brand-charcoal">Total Amount</span>
                                <span class="font-display font-extrabold text-xl text-brand-charcoal">₦{{ number_format($total, 0) }}</span>
                            </div>
                        </div>

                        <!-- Payment Method Selector -->
                        <div class="space-y-3 pt-3 border-t border-black/[0.06]" x-data="{ method: '{{ $user && $user->wallet_balance >= $total ? 'wallet' : 'bank_transfer' }}' }">
                            <span class="text-xs font-bold uppercase tracking-wider text-brand-charcoal block">Choose Payment Method:</span>

                            <div class="space-y-2">
                                <label class="block p-3.5 rounded-2xl border transition duration-150 cursor-pointer" :class="method === 'bank_transfer' ? 'border-emerald-500 bg-emerald-50/40' : 'border-black/10 hover:border-black/20 bg-[#fafafa]'">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="payment_method" value="bank_transfer" x-model="method" class="text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <div class="text-xs font-bold text-brand-charcoal">
                                                Bank Transfer &amp; Confirm on WhatsApp
                                            </div>
                                            <div class="text-[10px] text-brand-charcoal/55 mt-0.5">
                                                Transfer to the store account below, then tap Confirm to message on WhatsApp
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="method === 'bank_transfer'" class="mt-3 p-3.5 rounded-xl bg-white border border-emerald-200/80 space-y-2 text-xs">
                                        @if (!empty($hasVerifiedBank) && $tenant)
                                            <div class="flex items-center justify-between">
                                                <span class="text-brand-charcoal/60">Bank Name</span>
                                                <span class="font-bold text-brand-charcoal">{{ $tenant->bank_name }}</span>
                                            </div>
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-brand-charcoal/60">Account Number</span>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-mono font-extrabold text-sm text-emerald-700">{{ $tenant->bank_account_number }}</span>
                                                    <button
                                                        type="button"
                                                        onclick="event.preventDefault(); navigator.clipboard?.writeText('{{ $tenant->bank_account_number }}'); this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy', 1500);"
                                                        class="px-2 py-0.5 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[10px] font-bold cursor-pointer"
                                                    >
                                                        Copy
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-brand-charcoal/60">Account Name</span>
                                                <span class="font-bold text-brand-charcoal">{{ $tenant->bank_account_name }}</span>
                                            </div>
                                        @else
                                            <div class="text-[11px] text-amber-700 font-medium">
                                                Place your order below and tap Confirm to receive the store's verified account details on WhatsApp.
                                            </div>
                                        @endif
                                    </div>
                                </label>

                                @auth
                                    <label class="flex items-center justify-between p-3.5 rounded-2xl border transition duration-150 cursor-pointer" :class="method === 'wallet' ? 'border-brand-pink bg-brand-pink-50/50' : 'border-black/10 hover:border-black/20 bg-[#fafafa]'">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="payment_method" value="wallet" x-model="method" class="text-brand-pink focus:ring-brand-pink" {{ ($user->wallet_balance < $total) ? 'disabled' : '' }}>
                                            <div>
                                                <div class="text-xs font-bold text-brand-charcoal flex items-center gap-2">
                                                    <span>Boutique Store Credit / Wallet</span>
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $user->wallet_balance >= $total ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                        Bal: ₦{{ number_format($user->wallet_balance, 0) }}
                                                    </span>
                                                </div>
                                                <div class="text-[10px] text-brand-charcoal/50 mt-0.5">
                                                    @if($user->wallet_balance >= $total)
                                                        Instant 1-click checkout from your funded balance
                                                    @else
                                                        Insufficient balance for this order (Needs ₦{{ number_format($total - $user->wallet_balance, 0) }} more)
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endauth
                            </div>

                            <!-- Submit Trigger -->
                            <button
                                type="submit"
                                class="w-full py-4 px-6 rounded-full font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition duration-150 shadow-xl cursor-pointer"
                                :class="method === 'wallet' ? 'bg-brand-pink hover:bg-brand-pink-600 text-white shadow-brand-pink/25' : 'bg-[#25D366] hover:bg-[#1ebe5d] text-white shadow-emerald-600/25'"
                            >
                                <span x-show="method === 'wallet'">Pay with Wallet (₦{{ number_format($total, 0) }})</span>
                                <span x-show="method === 'bank_transfer'">I've Transferred &mdash; Confirm on WhatsApp &rarr;</span>
                            </button>

                            <div class="text-center text-[11px] text-brand-charcoal/50 leading-relaxed pt-1">
                                Stock is reserved while you confirm your transfer with the store on WhatsApp.
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
