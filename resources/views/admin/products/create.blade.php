<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-800 transition">
                    &larr; Products
                </a>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xs font-bold text-slate-900">Add New Product</span>
            </div>

            <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 transition">
                Cancel
            </a>
        </div>
    </x-slot>

    <div
        class="max-w-2xl mx-auto pt-4 pb-12"
        x-data="{
            step: 1,
            totalSteps: 5,
            name: '{{ old('name', '') }}',
            category_id: '{{ old('category_id', '') }}',
            category_name: '',
            categoriesList: {{ json_encode($categories->map(fn($c) => ['id' => (string) $c->id, 'name' => $c->name])->values()) }},
            customCategoryInput: '',
            showCustomInput: false,
            base_price: '{{ old('base_price', '') }}',
            description: '{{ old('description', '') }}',
            featuredPreview: null,
            variants: [
                { size: 'M', color: 'Standard', sku: 'PRD-' + Math.floor(1000 + Math.random() * 9000) + '-M', stock: 10, price_override: '' }
            ],
            commonSizes: ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size'],

            init() {
                if (this.categoriesList.length === 0) {
                    this.categoriesList = [
                        { id: 'new:General', name: 'General' },
                        { id: 'new:Clothing', name: 'Clothing' },
                        { id: 'new:Shoes', name: 'Shoes' },
                        { id: 'new:Accessories', name: 'Accessories' },
                        { id: 'new:Bags', name: 'Bags' }
                    ];
                }
            },

            addCustomCategory() {
                if (!this.customCategoryInput.trim()) return;
                const newName = this.customCategoryInput.trim();
                const existing = this.categoriesList.find(c => c.name.toLowerCase() === newName.toLowerCase());
                if (existing) {
                    this.selectCategory(existing.id, existing.name);
                } else {
                    const tempId = 'new:' + newName;
                    this.categoriesList.push({ id: tempId, name: newName });
                    this.selectCategory(tempId, newName);
                }
                this.customCategoryInput = '';
                this.showCustomInput = false;
            },

            hasSize(size) {
                return this.variants.some(v => v.size === size);
            },

            toggleSize(size) {
                const idx = this.variants.findIndex(v => v.size === size);
                if (idx > -1) {
                    if (this.variants.length > 1) {
                        this.variants.splice(idx, 1);
                    }
                } else {
                    const cleanCode = (this.name ? this.name.substring(0, 3).toUpperCase().replace(/[^A-Z]/g, '') : 'PRD') || 'PRD';
                    const randomNum = Math.floor(1000 + Math.random() * 9000);
                    const cleanSize = size.replace(/\s+/g, '').toUpperCase();
                    this.variants.push({
                        size: size,
                        color: this.variants[0]?.color || 'Standard',
                        sku: `${cleanCode}-${randomNum}-${cleanSize}`,
                        stock: 10,
                        price_override: ''
                    });
                }
            },

            addCustomVariant() {
                const randomNum = Math.floor(1000 + Math.random() * 9000);
                this.variants.push({
                    size: 'Custom',
                    color: 'Standard',
                    sku: 'VAR-' + randomNum,
                    stock: 5,
                    price_override: ''
                });
            },

            removeVariant(idx) {
                if (this.variants.length > 1) {
                    this.variants.splice(idx, 1);
                }
            },

            totalStock() {
                return this.variants.reduce((sum, v) => sum + (parseInt(v.stock) || 0), 0);
            },

            selectCategory(id, name) {
                this.category_id = id;
                this.category_name = name;
                this.nextStep();
            },

            nextStep() {
                if (this.step === 1 && !this.name.trim()) return;
                if (this.step === 2 && !this.category_id) return;
                if (this.step === 3 && (!this.base_price || parseFloat(this.base_price) <= 0)) return;
                if (this.step < this.totalSteps) {
                    this.step++;
                }
            },

            prevStep() {
                if (this.step > 1) {
                    this.step--;
                }
            },

            handleFeaturedChange(e) {
                const file = e.target.files[0];
                if (file) {
                    this.featuredPreview = URL.createObjectURL(file);
                }
            }
        }"
    >
        <!-- Top Progress Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-400 mb-2">
                <span x-text="'Question ' + step + ' of ' + totalSteps"></span>
                <span x-text="Math.round((step / totalSteps) * 100) + '% completed'"></span>
            </div>

            <!-- Soft Gradient Progress Bar (Borderless) -->
            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                <div
                    class="h-full bg-slate-900 rounded-full transition-all duration-300 ease-out"
                    :style="'width: ' + ((step / totalSteps) * 100) + '%'"
                ></div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Card Container (Borderless with Soft Shadow) -->
            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xl shadow-slate-900/5 min-h-[380px] flex flex-col justify-between">

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- QUESTION 1: PRODUCT NAME                                -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <div
                    x-show="step === 1"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 translate-y-3"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-3"
                    class="space-y-6"
                >
                    <div class="space-y-1">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-600">Step 1 &bull; Identification</span>
                        <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                            What is your product's name?
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-400">
                            Give your item a clear name that your customers and cashiers will easily recognize.
                        </p>
                    </div>

                    <div class="pt-2">
                        <input
                            type="text"
                            name="name"
                            x-model="name"
                            required
                            autofocus
                            placeholder="e.g. Classic Linen Shirt"
                            @keydown.enter.prevent="nextStep()"
                            class="w-full text-xl sm:text-2xl font-bold text-slate-900 placeholder-slate-300 bg-slate-50/80 rounded-2xl px-5 py-4 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 shadow-inner transition"
                        >
                        @error('name') <p class="text-rose-600 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-2">
                        <span class="text-xs text-slate-400">Tip: Press <kbd class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-mono text-[10px]">Enter ↵</kbd> or click Continue.</span>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- QUESTION 2: CATEGORY                                    -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <div
                    x-show="step === 2"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 translate-y-3"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-3"
                    style="display: none;"
                    class="space-y-6"
                >
                    <div class="space-y-1">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">Step 2 &bull; Classification</span>
                        <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                            Which category does this belong to?
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-400">
                            Select an existing category or create a new one.
                        </p>
                    </div>

                    <input type="hidden" name="category_id" :value="category_id">

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2">
                        <template x-for="cat in categoriesList" :key="cat.id">
                            <button
                                type="button"
                                @click="selectCategory(cat.id, cat.name)"
                                :class="category_id == cat.id ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/15' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 shadow-xs'"
                                class="p-4 rounded-2xl text-left font-bold text-xs sm:text-sm transition flex flex-col justify-between h-24 cursor-pointer"
                            >
                                <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                <span class="truncate" x-text="cat.name"></span>
                            </button>
                        </template>

                        <!-- + Add Custom Category Button Card -->
                        <button
                            type="button"
                            @click="showCustomInput = true; $nextTick(() => $refs.customCatInput?.focus())"
                            x-show="!showCustomInput"
                            class="p-4 rounded-2xl text-left font-bold text-xs sm:text-sm transition flex flex-col justify-between h-24 cursor-pointer bg-slate-50/80 hover:bg-slate-100 text-indigo-600 shadow-xs"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>+ New Category</span>
                        </button>
                    </div>

                    <!-- Inline Custom Category Creator -->
                    <div x-show="showCustomInput" class="pt-2" style="display: none;">
                        <div class="flex items-center gap-2">
                            <input
                                type="text"
                                x-ref="customCatInput"
                                x-model="customCategoryInput"
                                placeholder="Type category name (e.g. Watches, Perfumes)..."
                                @keydown.enter.prevent="addCustomCategory()"
                                class="flex-1 px-4 py-3 rounded-2xl bg-slate-50 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 shadow-inner"
                            >
                            <button
                                type="button"
                                @click="addCustomCategory()"
                                class="px-5 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md cursor-pointer shrink-0"
                            >
                                Add &bull; Select
                            </button>
                            <button
                                type="button"
                                @click="showCustomInput = false"
                                class="p-3 rounded-2xl text-slate-400 hover:text-slate-600 transition shrink-0 cursor-pointer"
                                title="Cancel"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    @error('category_id') <p class="text-rose-600 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- QUESTION 3: PRICE                                       -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <div
                    x-show="step === 3"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 translate-y-3"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-3"
                    style="display: none;"
                    class="space-y-6"
                >
                    <div class="space-y-1">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-600">Step 3 &bull; Pricing</span>
                        <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                            How much does it sell for?
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-400">
                            Enter the regular retail selling price in Nigerian Naira (₦).
                        </p>
                    </div>

                    <div class="pt-2">
                        <div class="relative flex items-center">
                            <span class="absolute left-5 text-2xl sm:text-3xl font-extrabold text-slate-400">₦</span>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="base_price"
                                x-model="base_price"
                                required
                                placeholder="0"
                                @keydown.enter.prevent="nextStep()"
                                class="w-full text-2xl sm:text-3xl font-extrabold text-slate-900 placeholder-slate-300 bg-slate-50/80 rounded-2xl pl-12 pr-5 py-4 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 shadow-inner transition"
                            >
                        </div>
                        @error('base_price') <p class="text-rose-600 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>

                    <p class="text-xs text-slate-400">
                        You can also set custom prices for specific sizes in the next step.
                    </p>
                </div>

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- QUESTION 4: SIZES & STOCK                               -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <div
                    x-show="step === 4"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 translate-y-3"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-3"
                    style="display: none;"
                    class="space-y-6"
                >
                    <div class="space-y-1">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-purple-600">Step 4 &bull; Stock &amp; Sizes</span>
                        <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                            What sizes and quantities do you have?
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-400">
                            Tap to toggle sizes. SKUs are generated automatically in the background.
                        </p>
                    </div>

                    <!-- Common Size Toggles -->
                    <div class="flex flex-wrap gap-2 pt-1">
                        <template x-for="s in commonSizes" :key="s">
                            <button
                                type="button"
                                @click="toggleSize(s)"
                                :class="hasSize(s) ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer"
                                x-text="s"
                            ></button>
                        </template>
                    </div>

                    <!-- Configured Variants List -->
                    <div class="space-y-2.5 max-h-56 overflow-y-auto pr-1">
                        <template x-for="(v, index) in variants" :key="index">
                            <div class="p-3.5 rounded-2xl bg-slate-50 flex items-center justify-between gap-3 shadow-2xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-9 h-9 rounded-xl bg-white text-slate-900 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs" x-text="v.size"></span>
                                    <div class="min-w-0">
                                        <input
                                            type="text"
                                            :name="'variants[' + index + '][color]'"
                                            x-model="v.color"
                                            placeholder="Color (e.g. Black)"
                                            class="text-xs font-semibold text-slate-800 bg-transparent border-0 p-0 focus:ring-0 placeholder-slate-400 w-28"
                                        >
                                        <div class="text-[10px] text-slate-400 font-mono truncate" x-text="v.sku"></div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[11px] font-semibold text-slate-400">Qty:</span>
                                        <input
                                            type="number"
                                            min="0"
                                            :name="'variants[' + index + '][stock]'"
                                            x-model="v.stock"
                                            class="w-16 px-2 py-1 rounded-lg bg-white text-xs font-bold text-slate-900 text-center shadow-xs"
                                        >
                                    </div>

                                    <!-- Hidden Inputs Required by Controller -->
                                    <input type="hidden" :name="'variants[' + index + '][size]'" :value="v.size">
                                    <input type="hidden" :name="'variants[' + index + '][sku]'" :value="v.sku">
                                    <input type="hidden" :name="'variants[' + index + '][price_override]'" :value="v.price_override">

                                    <button
                                        type="button"
                                        x-show="variants.length > 1"
                                        @click="removeVariant(index)"
                                        class="text-slate-400 hover:text-rose-600 text-xs font-bold p-1"
                                    >&times;</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="text-xs text-slate-500 font-medium flex items-center justify-between">
                        <span>Total starting inventory: <strong class="text-slate-900" x-text="totalStock() + ' items'"></strong></span>
                        <button type="button" @click="addCustomVariant()" class="text-indigo-600 hover:text-indigo-800 font-bold text-xs">+ Custom variant</button>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- QUESTION 5: PHOTO & REVIEW                              -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <div
                    x-show="step === 5"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 translate-y-3"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-3"
                    style="display: none;"
                    class="space-y-6"
                >
                    <div class="space-y-1">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-600">Step 5 &bull; Finishing Touches</span>
                        <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                            Add a photo &amp; description
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-400">
                            Optional &mdash; you can upload a photo now or add one later.
                        </p>
                    </div>

                    <!-- Photo Upload Area -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                        <label class="sm:col-span-2 p-5 rounded-2xl bg-slate-50 hover:bg-slate-100 transition cursor-pointer text-center flex flex-col items-center justify-center min-h-[140px] shadow-inner group">
                            <input
                                type="file"
                                name="featured_image_file"
                                accept="image/*"
                                @change="handleFeaturedChange($event)"
                                class="hidden"
                            >
                            <div class="w-10 h-10 rounded-xl bg-slate-100 group-hover:bg-slate-200 text-slate-600 flex items-center justify-center mb-1 group-hover:scale-105 transition-all">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-slate-800">Click to choose product image</span>
                            <span class="text-[10px] text-slate-400 mt-0.5">PNG, JPG, WEBP up to 10MB</span>
                        </label>

                        <!-- Preview Canvas -->
                        <div class="aspect-square rounded-2xl bg-slate-100 overflow-hidden flex items-center justify-center shadow-xs">
                            <template x-if="featuredPreview">
                                <img :src="featuredPreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!featuredPreview">
                                <span class="text-[11px] font-semibold text-slate-400">No photo</span>
                            </template>
                        </div>
                    </div>

                    <!-- Short Description -->
                    <div>
                        <textarea
                            name="description"
                            x-model="description"
                            rows="2"
                            placeholder="Add a short description or styling notes (optional)..."
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 shadow-inner transition"
                        ></textarea>
                    </div>

                    <!-- Final Summary Pill -->
                    <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-950 text-xs flex items-center justify-between shadow-2xs">
                        <div>
                            <div class="font-bold text-sm" x-text="name"></div>
                            <div class="text-[11px] text-emerald-700 mt-0.5">
                                <span x-text="category_name || 'Category'"></span> &bull;
                                ₦<span x-text="Number(base_price || 0).toLocaleString()"></span> &bull;
                                <span x-text="variants.length + ' variants (' + totalStock() + ' in stock)'"></span>
                            </div>
                        </div>
                        <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- NAVIGATION BUTTONS (Next / Back / Save)                 -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <div class="pt-8 flex items-center justify-between gap-4">
                    <button
                        type="button"
                        x-show="step > 1"
                        @click="prevStep()"
                        class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer"
                    >
                        &larr; Back
                    </button>

                    <div x-show="step === 1"></div>

                    <!-- Continue Button (Steps 1 to 4) -->
                    <button
                        type="button"
                        x-show="step < totalSteps"
                        @click="nextStep()"
                        class="px-6 py-3 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-md shadow-slate-900/10 cursor-pointer flex items-center gap-1.5 ml-auto"
                    >
                        <span>Continue</span>
                        <span>&rarr;</span>
                    </button>

                    <!-- Save & Publish Button (Step 5) -->
                    <button
                        type="submit"
                        x-show="step === totalSteps"
                        class="px-7 py-3 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold uppercase tracking-wider transition shadow-lg shadow-emerald-600/20 cursor-pointer flex items-center gap-2 ml-auto"
                    >
                        <span>Save &amp; Publish Product</span>
                        <span>&rarr;</span>
                    </button>
                </div>

            </div>
        </form>
    </div>
</x-admin-layout>
