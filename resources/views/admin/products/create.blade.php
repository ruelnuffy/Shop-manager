<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Product Catalog
                    </span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-slate-900 mt-1">
                    Create New Product
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Add new retail piece with images, pricing, categories, and multi-size inventory variants.
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
        featuredPreview: null,
        galleryPreviews: [],
        showUrlInput: false,
        variants: [
            { size: 'S', color: 'Midnight Black', sku: 'PRD-' + Math.floor(1000 + Math.random() * 9000) + '-S', stock: 10, price_override: '' },
            { size: 'M', color: 'Midnight Black', sku: 'PRD-' + Math.floor(1000 + Math.random() * 9000) + '-M', stock: 10, price_override: '' }
        ],
        addVariant() {
            this.variants.push({ size: 'L', color: 'Midnight Black', sku: 'PRD-' + Math.floor(1000 + Math.random() * 9000) + '-L', stock: 5, price_override: '' });
        },
        removeVariant(index) {
            if (this.variants.length > 1) {
                this.variants.splice(index, 1);
            }
        },
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
        },
        clearFeatured() {
            this.featuredPreview = null;
            if (this.$refs.featuredFile) {
                this.$refs.featuredFile.value = '';
            }
        }
    }">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- 1. Product Basic Information -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100">
                    1. General Information
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition" placeholder="e.g. Classic Tailored Blazer">
                        @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Category *</label>
                        <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                            <option value="">Select Category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Base Price (NGN ₦) *</label>
                        <input type="number" step="0.01" name="base_price" value="{{ old('base_price') }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition" placeholder="e.g. 45000">
                        @error('base_price') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Publication Status *</label>
                        <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                            <option value="active">Active (Visible in Catalog)</option>
                            <option value="draft">Draft (Hidden)</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Brand Tags (Comma-separated)</label>
                    <input type="text" name="brand_tags" value="{{ old('brand_tags') }}" class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition" placeholder="Cotton, Midnight, Premium, Summer">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition" placeholder="Details regarding fabric, styling, sizing guidance, and retail notes...">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- 2. Product Photography & Visuals -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                            2. Product Photography &amp; Visuals
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Upload high-resolution editorial photos directly from your device</p>
                    </div>
                </div>

                <!-- Primary Featured Photo -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">
                        Primary Featured Picture (Cover Photo)
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                        {{-- Drag & Drop Upload Zone --}}
                        <div class="md:col-span-2">
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
                                    Click or Drag &amp; Drop Featured Photo
                                </span>
                                <span class="text-[11px] text-slate-400 mt-0.5 block">
                                    PNG, JPG, WEBP up to 10MB
                                </span>
                            </label>
                            @error('featured_image_file') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror

                            {{-- Optional fallback URL input toggle --}}
                            <div class="mt-2 text-right">
                                <button
                                    type="button"
                                    @click="showUrlInput = !showUrlInput"
                                    class="text-[11px] font-medium text-slate-500 hover:text-indigo-600 transition"
                                >
                                    <span x-text="showUrlInput ? 'Hide URL Input' : 'Or paste an image URL instead &rarr;'"></span>
                                </button>
                            </div>

                            <div x-show="showUrlInput" class="mt-2" style="display: none;">
                                <input
                                    type="text"
                                    name="featured_image"
                                    value="{{ old('featured_image') }}"
                                    class="w-full px-3.5 py-2 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white"
                                    placeholder="https://... or /images/products/..."
                                >
                            </div>
                        </div>

                        {{-- Preview Box --}}
                        <div class="aspect-[3/4] max-h-48 md:max-h-none rounded-xl overflow-hidden border border-slate-200 bg-slate-50 flex flex-col items-center justify-center relative">
                            <template x-if="featuredPreview">
                                <div class="w-full h-full relative group">
                                    <img :src="featuredPreview" class="w-full h-full object-cover">
                                    <button
                                        type="button"
                                        @click="clearFeatured()"
                                        class="absolute top-2 right-2 w-6 h-6 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white text-xs flex items-center justify-center transition"
                                        title="Remove photo"
                                    >
                                        &times;
                                    </button>
                                </div>
                            </template>
                            <template x-if="!featuredPreview">
                                <div class="text-center p-3">
                                    <svg class="w-7 h-7 text-slate-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Preview Canvas</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Additional Gallery Pictures -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-semibold text-slate-700">
                            Additional Angle Photos (Gallery)
                        </label>
                        <span class="text-[11px] text-slate-400">Select multiple images</span>
                    </div>

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
                            Choose Gallery Photos (Back, Side, Detail Angles)
                        </span>
                    </label>

                    {{-- Live Gallery Previews --}}
                    <div x-show="galleryPreviews.length > 0" class="mt-3 flex items-center gap-3 overflow-x-auto pb-2" style="display: none;">
                        <template x-for="(g, gIdx) in galleryPreviews" :key="gIdx">
                            <div class="w-20 h-24 rounded-xl overflow-hidden border border-slate-200 shrink-0 relative bg-slate-100 group">
                                <img :src="g.url" class="w-full h-full object-cover">
                                <span class="absolute bottom-0 inset-x-0 bg-slate-900/80 text-white text-[8px] truncate px-1 py-0.5 text-center" x-text="g.name"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Video Showcase URL -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Video Reel / Showcase URL (Optional)</label>
                    <input type="text" name="video_url" value="{{ old('video_url') }}" class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white" placeholder="/videos/products/...mp4 or streaming URL">
                    <p class="text-[11px] text-slate-400 mt-1">Direct MP4 URL or streaming asset path for mobile video reels.</p>
                </div>
            </div>

            <!-- 3. Dynamic Product Variants Table -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                            3. Product Variants &amp; Initial Stock
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Define sizes, colors, unique SKUs, and starting warehouse inventory.</p>
                    </div>
                    <button type="button" @click="addVariant()" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Add Variant</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(v, index) in variants" :key="index">
                        <div class="grid grid-cols-12 gap-3 items-center p-3.5 rounded-xl bg-slate-50/60 border border-slate-200/80">
                            <div class="col-span-2">
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Size</label>
                                <input type="text" :name="'variants[' + index + '][size]'" x-model="v.size" required class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-800">
                            </div>
                            <div class="col-span-3">
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Color</label>
                                <input type="text" :name="'variants[' + index + '][color]'" x-model="v.color" required class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs text-slate-800">
                            </div>
                            <div class="col-span-3">
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">SKU</label>
                                <input type="text" :name="'variants[' + index + '][sku]'" x-model="v.sku" required class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono uppercase text-slate-800">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Stock Qty</label>
                                <input type="number" min="0" :name="'variants[' + index + '][stock]'" x-model="v.stock" required class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-900 font-mono">
                            </div>
                            <div class="col-span-2 flex items-center justify-between gap-2 pt-4">
                                <input type="number" step="0.01" :name="'variants[' + index + '][price_override]'" x-model="v.price_override" placeholder="Override ₦" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                <button type="button" @click="removeVariant(index)" class="text-rose-500 hover:text-rose-700 font-bold text-sm px-1.5">✕</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-semibold text-xs tracking-wide transition shadow-xs cursor-pointer flex items-center justify-center gap-2">
                <span>Save &amp; Publish Product to Catalog &rarr;</span>
            </button>
        </form>
    </div>
</x-admin-layout>
