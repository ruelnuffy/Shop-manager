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
                                <input type="text" name="shipping_city" value="{{ old('shipping_city', $user?->city ?? 'Jos') }}" required class="w-full px-4 py-2.5 bg-[#f4f4f4] border-transparent focus:border-black/20 focus:bg-white focus:ring-0 rounded-xl text-xs font-medium text-brand-charcoal" placeholder="e.g. West of Mines, Jos">
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
                        <div class="space-y-3 pt-3 border-t border-black/[0.06]" x-data="{ method: '{{ $user && $user->wallet_balance >= $total ? 'wallet' : 'paystack' }}' }">
                            <span class="text-xs font-bold uppercase tracking-wider text-brand-charcoal block">Choose Payment Method:</span>

                            <div class="space-y-2">
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

                                <label class="flex items-center justify-between p-3.5 rounded-2xl border transition duration-150 cursor-pointer" :class="method === 'paystack' ? 'border-brand-pink bg-brand-pink-50/50' : 'border-black/10 hover:border-black/20 bg-[#fafafa]'">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="payment_method" value="paystack" x-model="method" class="text-brand-pink focus:ring-brand-pink">
                                        <div>
                                            <div class="text-xs font-bold text-brand-charcoal flex items-center gap-2">
                                                <span>Paystack (Debit Card / Bank Transfer / USSD)</span>
                                            </div>
                                            <div class="text-[10px] text-brand-charcoal/50 mt-0.5">
                                                Secured 256-bit payment gateway
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Submit Trigger -->
                            <button
                                type="submit"
                                class="w-full py-4 px-6 rounded-full font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition duration-150 shadow-xl cursor-pointer"
                                :class="method === 'wallet' ? 'bg-brand-pink hover:bg-brand-pink-600 text-white shadow-brand-pink/25' : 'bg-black hover:bg-brand-pink text-white shadow-black/10'"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span x-show="method === 'wallet'">Pay with Boutique Wallet (₦{{ number_format($total, 0) }})</span>
                                <span x-show="method === 'paystack'">Pay with Paystack (₦{{ number_format($total, 0) }})</span>
                            </button>

                            <div class="text-center text-[11px] text-brand-charcoal/50 leading-relaxed pt-1">
                                Stock will be reserved and confirmed upon successful transaction.
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
