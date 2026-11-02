<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Fulfillment Hub
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium">{{ $orders->total() }} Total Orders</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-slate-900 mt-1">
                    Order Management
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Track customer purchases, monitor Paystack payments, and dispatch couriers.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Toolbar -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-6 relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by order #, customer name, email, phone..."
                           class="w-full pl-9.5 pr-4 py-2 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                </div>

                <div class="sm:col-span-3">
                    <select name="status" class="w-full px-3.5 py-2 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                        <option value="">All Fulfillment Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped (In Transit)</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered (Complete)</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="sm:col-span-3 flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-semibold tracking-wide transition shadow-xs flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['q', 'status']))
                        <a href="{{ route('admin.orders.index') }}" title="Clear filters" class="p-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 pl-6">Order</th>
                            <th class="py-3.5 px-4">Customer</th>
                            <th class="py-3.5 px-4">Destination</th>
                            <th class="py-3.5 px-4">Total Amount</th>
                            <th class="py-3.5 px-4">Payment</th>
                            <th class="py-3.5 px-4">Fulfillment</th>
                            <th class="py-3.5 px-4 pr-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 pl-6">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition font-mono">
                                        #{{ $order->order_number }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $order->created_at->format('M d, Y &bull; H:i') }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800">{{ $order->shipping_name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $order->shipping_phone }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-medium">
                                    {{ $order->shipping_city }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-900 font-mono text-sm">₦{{ number_format($order->total_amount, 0) }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($order->payment_status === 'paid')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Paid
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $statusStyles = [
                                            'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'shipped' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-100',
                                        ];
                                        $currentStyle = $statusStyles[$order->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $currentStyle }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 pr-6 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                        <span>Manage</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800">No customer orders found</p>
                                    <p class="text-xs text-slate-400 mt-1">Try adjusting your filters or date range.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
