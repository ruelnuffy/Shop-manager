<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-brand-charcoal">
                    Curated Catalog &amp; Collections ({{ $products->total() }})
                </h1>
            </div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.products.create') }}" class="px-5 py-2.5 rounded-full bg-slate-900 text-white hover:bg-indigo-600 text-xs font-bold uppercase tracking-wider transition shadow-sm">
                    + Add New Product
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Toolbar -->
        <div class="bg-white rounded-3xl p-6 border border-black/[0.06] shadow-sm">
            <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name..." class="px-4 py-2 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs">

                <select name="category_id" class="px-4 py-2 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="px-4 py-2 bg-[#f4f4f4] border-transparent focus:bg-white focus:border-black/20 rounded-xl text-xs">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="w-full py-2 px-4 rounded-xl bg-black text-white text-xs font-bold uppercase tracking-wider">
                        Filter
                    </button>
                    @if(request()->hasAny(['q', 'category_id', 'status']))
                        <a href="{{ route('admin.products.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition" title="Clear filters">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-3xl border border-black/[0.06] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#fafafa] border-b border-black/[0.06] text-[10px] font-bold uppercase tracking-widest text-brand-charcoal/50">
                        <tr>
                            <th class="p-4 pl-6">Product</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Base Price</th>
                            <th class="p-4">Total Stock</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/[0.06]">
                        @forelse ($products as $prod)
                            <tr class="hover:bg-black/[0.01] transition">
                                <td class="p-4 pl-6">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset($prod->featured_image) }}" class="w-10 h-12 object-cover rounded-lg bg-[#f3f3f3]">
                                        <div>
                                            @if(Auth::user()->isAdmin())
                                                <a href="{{ route('admin.products.edit', $prod->id) }}" class="font-bold text-brand-charcoal hover:underline">
                                                    {{ $prod->name }}
                                                </a>
                                            @else
                                                <span class="font-bold text-brand-charcoal">{{ $prod->name }}</span>
                                            @endif
                                            <div class="text-[11px] text-brand-charcoal/50">{{ $prod->variants->count() }} variants</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-medium text-brand-charcoal">{{ $prod->category->name ?? 'None' }}</td>
                                <td class="p-4 font-bold text-brand-charcoal">₦{{ number_format($prod->base_price, 0) }}</td>
                                <td class="p-4">
                                    @php $stock = $prod->totalStock(); @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $stock <= 5 ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $stock }} units
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $prod->status === 'active' ? 'bg-black text-white' : 'bg-[#f4f4f4] text-brand-charcoal' }}">
                                        {{ $prod->status }}
                                    </span>
                                </td>
                                <td class="p-4 pr-6 text-right space-x-2">
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('admin.products.edit', $prod->id) }}" class="text-xs font-bold text-brand-charcoal hover:underline">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $prod->id) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-red-600 hover:underline">
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-slate-400 font-medium">Read Only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-brand-charcoal/50">
                                    No products found in the database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-black/[0.06]">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
