<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Order Invoice &bull; {{ $currentTenant->name ?? 'ShopManager' }}
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-mono">{{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-slate-900 mt-1">
                    Order Details &bull; #{{ $order->order_number }}
                </h1>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Orders List</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left: Items & Financials -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Purchased Items Card -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                                Purchased Line Items
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Itemized products ordered by customer</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                            {{ $order->items->count() }} items
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach ($order->items as $item)
                            <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-3.5">
                                    @php
                                        $orderItemImg = $item->variant?->product?->featured_image_url ?? ($item->variant?->product?->featured_image ? asset(ltrim($item->variant->product->featured_image, '/')) : null);
                                    @endphp
                                    <div class="w-12 h-14 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200/60 relative flex items-center justify-center">
                                        @if ($orderItemImg)
                                            <img src="{{ $orderItemImg }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                            <div class="hidden w-full h-full flex items-center justify-center bg-slate-100 text-[9px] font-bold text-slate-400 font-mono">
                                                ITEM
                                            </div>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-slate-100 text-[9px] font-bold text-slate-400 font-mono">
                                                ITEM
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $item->product_name }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $item->variant_description }} &bull; Qty: <strong class="text-slate-700">{{ $item->quantity }}</strong></div>
                                    </div>
                                </div>
                                <div class="font-bold text-slate-900 font-mono text-sm">
                                    ₦{{ number_format($item->subtotal, 0) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-5 border-t border-slate-100 space-y-2.5 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-800 font-mono">₦{{ number_format($order->subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Courier Shipping Fee</span>
                            <span class="font-semibold text-slate-800 font-mono">₦{{ number_format($order->shipping_fee, 0) }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-slate-200/80 text-sm">
                            <span class="font-bold text-slate-900 uppercase tracking-wider">Total Amount Paid</span>
                            <span class="font-display font-extrabold text-xl text-slate-900 font-mono">₦{{ number_format($order->total_amount, 0) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Destination Details -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100">
                        Customer &amp; Shipping Destination
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-slate-600 leading-relaxed">
                        <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-100">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Recipient Details</span>
                            <strong class="text-slate-900 text-sm block mb-1">{{ $order->shipping_name }}</strong>
                            <div class="space-y-0.5">
                                <div><span class="text-slate-400">Phone:</span> <strong class="text-slate-700">{{ $order->shipping_phone }}</strong></div>
                                <div><span class="text-slate-400">Email:</span> <span class="text-slate-700 font-mono">{{ $order->shipping_email }}</span></div>
                            </div>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-100">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Delivery Address</span>
                            <div class="text-slate-800 font-medium">{{ $order->shipping_address }}</div>
                            <div class="text-slate-600 font-semibold mt-0.5">{{ $order->shipping_city }}</div>
                            @if($order->notes)
                                <div class="mt-3 p-2.5 rounded-lg bg-amber-50/80 border border-amber-200/60 text-[11px] text-amber-900">
                                    <strong>Customer Notes:</strong> {{ $order->notes }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Status Updater & Payment Audit -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Status Updater Form -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100">
                        Fulfillment Status
                    </h2>

                    <form method="POST" action="{{ route('admin.orders.status', $order->id) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1.5">
                                Update Status
                            </label>
                            <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped (In Transit)</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered (Complete)</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-semibold text-xs transition shadow-xs flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Save Status Update</span>
                        </button>
                    </form>
                </div>

                <!-- Payment Audit Details -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-3.5 text-xs">
                    <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100">
                        Paystack Transaction
                    </h2>

                    <div class="space-y-2.5 text-slate-600">
                        <div class="flex justify-between items-center">
                            <span>Payment Status:</span>
                            @if($order->payment_status === 'paid')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    {{ $order->payment_status }}
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-100">
                                    {{ $order->payment_status }}
                                </span>
                            @endif
                        </div>
                        <div class="flex justify-between">
                            <span>Channel:</span>
                            <span class="font-semibold text-slate-800 uppercase">{{ $order->latestPayment?->channel ?? 'Card / Online' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Reference:</span>
                            <code class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded text-slate-700">{{ $order->latestPayment?->paystack_reference ?? 'N/A' }}</code>
                        </div>
                        <div class="flex justify-between">
                            <span>Paid At:</span>
                            <span class="font-medium text-slate-800">{{ $order->latestPayment?->paid_at?->format('Y-m-d H:i') ?? 'Pending' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
