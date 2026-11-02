<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Product
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-mono">{{ $product->category?->name ?? 'Retail' }}</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-slate-900 mt-1">
                    Edit &bull; {{ $product->name }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Modify product attributes, media, tags, pricing, and view associated inventory variants.
                </p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Catalog</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl" x-data="{
        featuredPreview: '{{ $product->featured_image ? asset($product->featured_image) : '' }}',
        galleryPreviews: [],
        showUrlInput: false,
        handleFeaturedChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.featuredPreview = URL.createObjectURL(file);
            }
        },
        handleGalleryChange(e) {
            const files = Array.from(e.target.files);
            this.galleryPreviews = files.map(file => ({
                name: file.name,
                url: URL.createObjectURL(file)
            }));
        }
    }">
        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. General Information -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100">
                    1. General Information
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                        @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Category *</label>
                        <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Base Price (NGN ₦) *</label>
                        <input type="number" step="0.01" name="base_price" value="{{ old('base_price', $product->base_price) }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Publication Status *</label>
                        <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                            <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active (Visible)</option>
                            <option value="draft" {{ $product->status === 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                            <option value="archived" {{ $product->status === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Brand Tags (Comma-separated)</label>
                    <input type="text" name="brand_tags" value="{{ old('brand_tags', is_array($product->brand_tags) ? implode(', ', $product->brand_tags) : '') }}" class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Description</label>
                    <textarea name="description" rows="4" class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <!-- 2. Product Photography & Visuals -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                            2. Product Photography &amp; Visuals
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Replace or upload new pictures directly from your device</p>
                    </div>
                </div>

                <!-- Primary Featured Photo -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">
                        Primary Featured Picture
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                        {{-- Dropzone / selector --}}
                        <div class="md:col-span-2 space-y-2">
                            <label
                                class="relative flex flex-col items-center justify-center p-6 border-2 border-dashed border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/20 rounded-xl bg-slate-50/60 transition cursor-pointer group text-center"
                            >
                                <input
                                    type="file"
                                    name="featured_image_file"
                                    x-ref="featuredFile"
                                    @change="handleFeaturedChange($event)"
                                    accept="image/*"
                                    class="hidden"
                                >
                                <div class="w-10 h-10 rounded-xl bg-white group-hover:bg-indigo-600 group-hover:text-white text-slate-400 flex items-center justify-center shadow-xs border border-slate-200/60 transition duration-150 mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-800 block">
                                    Upload New Picture to Replace Current
                                </span>
                                <span class="text-[11px] text-slate-400 mt-0.5 block">
                                    PNG, JPG, WEBP up to 10MB
                                </span>
                            </label>
                            @error('featured_image_file') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror

                            {{-- Optional fallback URL input toggle --}}
                            <div class="text-right">
                                <button
                                    type="button"
                                    @click="showUrlInput = !showUrlInput"
                                    class="text-[11px] font-medium text-slate-500 hover:text-indigo-600 transition"
                                >
                                    <span x-text="showUrlInput ? 'Hide URL Input' : 'Or update using image URL instead &rarr;'"></span>
                                </button>
                            </div>

                            <div x-show="showUrlInput" class="mt-2" style="display: none;">
                                <input
                                    type="text"
                                    name="featured_image"
                                    value="{{ old('featured_image', $product->featured_image) }}"
                                    class="w-full px-3.5 py-2 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white"
                                    placeholder="https://... or /images/products/..."
                                >
                            </div>
                        </div>

                        {{-- Preview Box --}}
                        <div class="aspect-[3/4] max-h-48 md:max-h-none rounded-xl overflow-hidden border border-slate-200 bg-slate-50 relative">
                            <img :src="featuredPreview" class="w-full h-full object-cover">
                            <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-md bg-slate-900/80 backdrop-blur-md text-white text-[9px] font-semibold uppercase tracking-wider">
                                Current Picture
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Existing & Additional Gallery Pictures -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold text-slate-700">
                            Product Gallery Photos
                        </label>
                        <span class="text-[11px] text-slate-400 font-medium">Multiple angle shots</span>
                    </div>

                    {{-- Existing Gallery Photos with remove checkboxes --}}
                    @if ($product->images && count($product->images) > 0)
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">
                                Current Gallery Pictures (Check any to remove):
                            </span>
                            <div class="flex items-center gap-3 overflow-x-auto pb-2">
                                @foreach ($product->images as $img)
                                    <label class="w-20 h-24 rounded-xl overflow-hidden border-2 border-slate-200 hover:border-rose-400 shrink-0 relative bg-slate-100 group cursor-pointer">
                                        <img src="{{ asset($img) }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                            <input type="checkbox" name="remove_images[]" value="{{ $img }}" class="w-4 h-4 text-rose-600 rounded">
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Upload More Gallery Photos --}}
                    <label
                        class="flex items-center justify-center gap-3 p-4 border border-dashed border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/20 rounded-xl bg-slate-50/60 transition cursor-pointer text-center"
                    >
                        <input
                            type="file"
                            name="gallery_images[]"
                            multiple
                            accept="image/*"
                            @change="handleGalleryChange($event)"
                            class="hidden"
                        >
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span class="text-xs font-semibold text-slate-700">
                            + Upload Additional Gallery Photos
                        </span>
                    </label>

                    {{-- Live Gallery Previews --}}
                    <div x-show="galleryPreviews.length > 0" class="flex items-center gap-3 overflow-x-auto pb-2" style="display: none;">
                        <template x-for="(g, gIdx) in galleryPreviews" :key="gIdx">
                            <div class="w-20 h-24 rounded-xl overflow-hidden border border-indigo-400 shrink-0 relative bg-slate-100 group">
                                <img :src="g.url" class="w-full h-full object-cover">
                                <span class="absolute bottom-0 inset-x-0 bg-indigo-600 text-white text-[8px] truncate px-1 py-0.5 text-center" x-text="'New: ' + g.name"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Video Showcase URL -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Video Reel / Showcase URL (Optional)</label>
                    <input type="text" name="video_url" value="{{ old('video_url', $product->video_url) }}" class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white" placeholder="/videos/products/....mp4">
                    <p class="text-[11px] text-slate-400 mt-1">Local MP4 path or streaming link for customer video reels.</p>
                </div>
            </div>

            <!-- 3. Current Variants list -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                        3. Associated Variants &amp; Live Stock
                    </h3>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                        {{ $product->variants->count() }} Variants
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach ($product->variants as $variant)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900">{{ $variant->size }} &bull; {{ $variant->color }}</span>
                                <span class="font-mono text-slate-400 text-[11px] ml-2">({{ $variant->sku }})</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-slate-900 font-mono">₦{{ number_format($variant->effective_price, 0) }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $variant->stock_quantity <= 5 ? 'bg-rose-50 text-rose-700 border border-rose-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">
                                    {{ $variant->stock_quantity }} in stock
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-semibold text-xs tracking-wide transition shadow-xs cursor-pointer flex items-center justify-center gap-2">
                <span>Save Changes to Product &rarr;</span>
            </button>
        </form>
    </div>
</x-admin-layout>
