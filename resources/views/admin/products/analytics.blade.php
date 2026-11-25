<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900">
                    Product Clicks &amp; Best-Seller Suggestions
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Track how many times each product link is clicked and see which products to promote next.
                </p>
            </div>
            <a
                href="{{ route('admin.products.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-xs transition self-start sm:self-auto"
            >
                &larr; Back to Products
            </a>
        </div>

        <!-- KPI Summary -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Link Clicks</div>
                <div class="font-display font-extrabold text-2xl text-slate-900 mt-2">{{ number_format($totalClicks) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Across {{ $products->count() }} products</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Units Sold</div>
                <div class="font-display font-extrabold text-2xl text-emerald-600 mt-2">{{ number_format($totalUnitsSold) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">₦{{ number_format($totalRevenue, 0) }} order revenue</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Most Clicked</div>
                <div class="font-bold text-sm text-slate-900 mt-2 truncate">
                    {{ $topClickedProduct ? $topClickedProduct['product']->name : 'N/A' }}
                </div>
                <div class="text-[11px] text-indigo-600 font-semibold mt-1">
                    {{ $topClickedProduct ? number_format($topClickedProduct['clicks']).' clicks' : '0 clicks' }}
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Best Selling</div>
                <div class="font-bold text-sm text-slate-900 mt-2 truncate">
                    {{ $topSellingProduct ? $topSellingProduct['product']->name : 'N/A' }}
                </div>
                <div class="text-[11px] text-emerald-600 font-semibold mt-1">
                    {{ $topSellingProduct ? number_format($topSellingProduct['units_sold']).' sold' : '0 sold' }}
                </div>
            </div>
        </div>

        <!-- Suggested Best-Selling / Top-Clicked Products -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display font-bold text-base text-slate-900">Suggested Products to Promote</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Ranked by storefront link clicks and completed order volume.</p>
                </div>
            </div>

            @if ($suggestedProducts->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($suggestedProducts as $index => $row)
                        @php
                            $prod = $row['product'];
                            $productUrl = route('products.show', $prod->slug);
                        @endphp
                        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-4 flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-50 text-indigo-700">
                                        #{{ $index + 1 }} &bull; {{ $row['suggestion_label'] }}
                                    </span>
                                    <span class="text-xs font-extrabold text-slate-900">₦{{ number_format($prod->base_price, 0) }}</span>
                                </div>
                                <div class="font-display font-bold text-base text-slate-900 line-clamp-1">{{ $prod->name }}</div>
                                <div class="flex items-center gap-4 text-xs text-slate-600">
                                    <span><strong class="text-slate-900">{{ number_format($row['clicks']) }}</strong> link clicks</span>
                                    <span><strong class="text-emerald-700">{{ number_format($row['units_sold']) }}</strong> sold</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-2 border-t border-slate-200/70">
                                <button
                                    type="button"
                                    onclick="navigator.clipboard?.writeText('{{ $productUrl }}'); this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy Link', 1500);"
                                    class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-slate-400 text-[11px] font-bold text-slate-700 transition cursor-pointer"
                                >
                                    Copy Link
                                </button>
                                <a
                                    href="{{ $productUrl }}"
                                    target="_blank"
                                    class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-[11px] font-bold text-white transition"
                                >
                                    View &nearr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-xs text-slate-400 py-4">Add active products to see click and sales suggestions.</div>
            @endif
        </div>

        <!-- Full Product Clicks & Sales Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-display font-bold text-base text-slate-900">All Products Performance</h2>
                <span class="text-xs text-slate-400">Sorted by best-seller &amp; click score</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Product</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Link Clicks</th>
                            <th class="px-4 py-3">Units Sold</th>
                            <th class="px-4 py-3">Conv. Rate</th>
                            <th class="px-4 py-3">Suggestion</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($products as $row)
                            @php
                                $prod = $row['product'];
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-3.5 font-bold text-slate-900">{{ $prod->name }}</td>
                                <td class="px-4 py-3.5 text-slate-500">{{ $prod->category?->name ?? 'Uncategorized' }}</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-800">₦{{ number_format($prod->base_price, 0) }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-bold">
                                        {{ number_format($row['clicks']) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-bold text-emerald-700">{{ number_format($row['units_sold']) }}</td>
                                <td class="px-4 py-3.5 text-slate-600">{{ $row['conversion_rate'] }}%</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-semibold">
                                        {{ $row['suggestion_label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-2">
                                    <a href="{{ route('products.show', $prod->slug) }}" target="_blank" class="text-indigo-600 hover:underline font-semibold">View</a>
                                    @if (Auth::user()->isAdmin())
                                        <a href="{{ route('admin.products.edit', $prod->id) }}" class="text-slate-600 hover:underline font-semibold">Edit</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-slate-400">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
