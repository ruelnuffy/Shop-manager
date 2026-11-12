<x-admin-layout>
<div class="space-y-6 max-w-6xl mx-auto pb-12">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100 mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                <span>Storefront Template #1 Customizer</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Storefront &amp; Hero Customization
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                Personalize your customer boutique. Change your main hero visual showcase, featuring images, banner headlines, and brand accents.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('storefront') }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold shadow-md shadow-slate-900/10 transition"
            >
                <span>Preview Storefront</span>
                <span class="text-slate-400">&nearr;</span>
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
            <a href="{{ route('storefront') }}" target="_blank" class="underline font-bold hover:text-emerald-950">
                View Live &rarr;
            </a>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.storefront.update') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        @php
            $currentHeroImage = data_get($storefront, 'hero_image') ?: asset('images/shop/boutique-shelves-showcase.png');
            $currentHeroTitle = data_get($storefront, 'hero_title', data_get($branding, 'name', $tenant->name));
            $currentHeroSubtitle = data_get($storefront, 'hero_subtitle', data_get($branding, 'tagline', 'Curated Boutique & Ready-to-Wear'));
            $currentHeroBadge = data_get($storefront, 'hero_badge', 'Physical Boutique Experience');

            $featuring = data_get($storefront, 'featuring', []);
            $feat1 = $featuring[0] ?? [];
            $feat2 = $featuring[1] ?? [];
            $feat3 = $featuring[2] ?? [];
        @endphp

        <!-- Section 1: Hero Showcase & Visual Image -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <span>1. Main Hero Visual Showcase</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    This is the centerpiece hero visual your shoppers see first at the top of your boutique homepage.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Preview Canvas -->
                <div class="lg:col-span-5 space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Current Hero Image Preview</label>
                    <div class="relative rounded-2xl overflow-hidden aspect-[16/10] bg-slate-900 border border-slate-200 shadow-md group">
                        <img
                            src="{{ $currentHeroImage }}"
                            alt="Storefront Hero"
                            id="heroPreviewImg"
                            class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none"></div>
                        <div class="absolute top-3 left-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black/70 backdrop-blur-md text-white border border-white/20">
                                {{ $currentHeroBadge }}
                            </span>
                        </div>
                        <div class="absolute bottom-3 left-3 right-3 text-white">
                            <div class="text-[10px] uppercase font-bold tracking-wider text-blue-200 truncate">{{ $currentHeroSubtitle }}</div>
                            <div class="text-sm font-extrabold uppercase tracking-tight truncate">{{ $currentHeroTitle }}</div>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400">Recommended dimensions: 1920 &times; 1080px (16:9 or 21:9 ratio). High-res photography looks stunning.</p>
                </div>

                <!-- Input Controls -->
                <div class="lg:col-span-7 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Upload New Hero Image</label>
                        <input
                            type="file"
                            name="hero_image_file"
                            accept="image/*"
                            onchange="previewImage(this, 'heroPreviewImg')"
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Or Enter Hero Image URL</label>
                        <input
                            type="text"
                            name="hero_image_url"
                            value="{{ old('hero_image_url', is_string($currentHeroImage) && str_starts_with($currentHeroImage, 'http') ? $currentHeroImage : '') }}"
                            placeholder="https://images.unsplash.com/... or cloud image URL"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition"
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Hero Headline / Title</label>
                            <input
                                type="text"
                                name="hero_title"
                                value="{{ old('hero_title', $currentHeroTitle) }}"
                                placeholder="Store name or main headline"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-blue-600"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Hero Subtitle / Tagline</label>
                            <input
                                type="text"
                                name="hero_subtitle"
                                value="{{ old('hero_subtitle', $currentHeroSubtitle) }}"
                                placeholder="Boutique statement or tagline"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-blue-600"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Hero Floating Badge Label</label>
                        <input
                            type="text"
                            name="hero_badge"
                            value="{{ old('hero_badge', $currentHeroBadge) }}"
                            placeholder="e.g. Flagship Atelier Open, New 2026 Collection"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-blue-600"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Featuring Images & Highlight Tiles -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <span>2. Featuring Images &amp; Promo Highlights</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Showcase 3 signature promotional features, category spotlights, or seasonal drops directly on your storefront.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                @php
                    $c1Img = data_get($feat1, 'image') ?: asset('images/categories/bags.png');
                    $c1Title = data_get($feat1, 'title', 'Handcrafted Bags');
                    $c1Sub = data_get($feat1, 'subtitle', 'Artisanal leather & gold padlocks');
                    $c1Badge = data_get($feat1, 'badge', 'Spotlight 01');
                    $c1Link = data_get($feat1, 'link', route('products.index'));
                @endphp
                <div class="bg-slate-50/80 rounded-2xl p-4.5 border border-slate-200/80 space-y-3.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Feature Card 1</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-slate-600 border border-slate-200">#01</span>
                    </div>

                    <div class="relative rounded-xl overflow-hidden aspect-[4/3] bg-slate-900 border border-slate-200 shadow-xs">
                        <img src="{{ $c1Img }}" alt="Feature 1" id="feat1PreviewImg" class="w-full h-full object-cover">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Upload Card Image</label>
                        <input
                            type="file"
                            name="featuring_1_image_file"
                            accept="image/*"
                            onchange="previewImage(this, 'feat1PreviewImg')"
                            class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-white file:text-slate-800 hover:file:bg-slate-100 cursor-pointer"
                        />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Or Image URL</label>
                        <input
                            type="text"
                            name="featuring_1_image_url"
                            value="{{ old('featuring_1_image_url', is_string($c1Img) && str_starts_with($c1Img, 'http') ? $c1Img : '') }}"
                            placeholder="https://..."
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                        />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Title</label>
                        <input
                            type="text"
                            name="featuring_1_title"
                            value="{{ old('featuring_1_title', $c1Title) }}"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                        />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Subtitle</label>
                        <input
                            type="text"
                            name="featuring_1_subtitle"
                            value="{{ old('featuring_1_subtitle', $c1Sub) }}"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Badge</label>
                            <input
                                type="text"
                                name="featuring_1_badge"
                                value="{{ old('featuring_1_badge', $c1Badge) }}"
                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                            />
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Link URL</label>
                            <input
                                type="text"
                                name="featuring_1_link"
                                value="{{ old('featuring_1_link', $c1Link) }}"
                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                            />
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                @php
                    $c2Img = data_get($feat2, 'image') ?: asset('images/categories/clothes.png');
                    $c2Title = data_get($feat2, 'title', 'Apparel & Gowns');
                    $c2Sub = data_get($feat2, 'subtitle', 'Jos atelier hand-finished silks');
                    $c2Badge = data_get($feat2, 'badge', 'Spotlight 02');
                    $c2Link = data_get($feat2, 'link', route('products.index'));
                @endphp
                <div class="bg-slate-50/80 rounded-2xl p-4.5 border border-slate-200/80 space-y-3.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Feature Card 2</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-slate-600 border border-slate-200">#02</span>
                    </div>

                    <div class="relative rounded-xl overflow-hidden aspect-[4/3] bg-slate-900 border border-slate-200 shadow-xs">
                        <img src="{{ $c2Img }}" alt="Feature 2" id="feat2PreviewImg" class="w-full h-full object-cover">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Upload Card Image</label>
                        <input
                            type="file"
                            name="featuring_2_image_file"
                            accept="image/*"
                            onchange="previewImage(this, 'feat2PreviewImg')"
                            class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-white file:text-slate-800 hover:file:bg-slate-100 cursor-pointer"
                        />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Or Image URL</label>
                        <input
                            type="text"
                            name="featuring_2_image_url"
                            value="{{ old('featuring_2_image_url', is_string($c2Img) && str_starts_with($c2Img, 'http') ? $c2Img : '') }}"
                            placeholder="https://..."
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                        />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Title</label>
                        <input
                            type="text"
                            name="featuring_2_title"
                            value="{{ old('featuring_2_title', $c2Title) }}"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                        />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Subtitle</label>
                        <input
                            type="text"
                            name="featuring_2_subtitle"
                            value="{{ old('featuring_2_subtitle', $c2Sub) }}"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Badge</label>
                            <input
                                type="text"
                                name="featuring_2_badge"
                                value="{{ old('featuring_2_badge', $c2Badge) }}"
                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                            />
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Link URL</label>
                            <input
                                type="text"
                                name="featuring_2_link"
                                value="{{ old('featuring_2_link', $c2Link) }}"
                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                            />
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                @php
                    $c3Img = data_get($feat3, 'image') ?: asset('images/categories/shoes.png');
                    $c3Title = data_get($feat3, 'title', 'Luxury Footwear');
                    $c3Sub = data_get($feat3, 'subtitle', 'Italian silhouette stilettos');
                    $c3Badge = data_get($feat3, 'badge', 'Spotlight 03');
                    $c3Link = data_get($feat3, 'link', route('products.index'));
                @endphp
                <div class="bg-slate-50/80 rounded-2xl p-4.5 border border-slate-200/80 space-y-3.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Feature Card 3</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-slate-600 border border-slate-200">#03</span>
                    </div>

                    <div class="relative rounded-xl overflow-hidden aspect-[4/3] bg-slate-900 border border-slate-200 shadow-xs">
                        <img src="{{ $c3Img }}" alt="Feature 3" id="feat3PreviewImg" class="w-full h-full object-cover">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Upload Card Image</label>
                        <input
                            type="file"
                            name="featuring_3_image_file"
                            accept="image/*"
                            onchange="previewImage(this, 'feat3PreviewImg')"
                            class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-white file:text-slate-800 hover:file:bg-slate-100 cursor-pointer"
                        />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Or Image URL</label>
                        <input
                            type="text"
                            name="featuring_3_image_url"
                            value="{{ old('featuring_3_image_url', is_string($c3Img) && str_starts_with($c3Img, 'http') ? $c3Img : '') }}"
                            placeholder="https://..."
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                        />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Title</label>
                        <input
                            type="text"
                            name="featuring_3_title"
                            value="{{ old('featuring_3_title', $c3Title) }}"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                        />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Subtitle</label>
                        <input
                            type="text"
                            name="featuring_3_subtitle"
                            value="{{ old('featuring_3_subtitle', $c3Sub) }}"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Badge</label>
                            <input
                                type="text"
                                name="featuring_3_badge"
                                value="{{ old('featuring_3_badge', $c3Badge) }}"
                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                            />
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-0.5">Link URL</label>
                            <input
                                type="text"
                                name="featuring_3_link"
                                value="{{ old('featuring_3_link', $c3Link) }}"
                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Brand Identity & Palette -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span>3. Brand Identity &amp; Accent Palette</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Colors and typography accents applied throughout your storefront template and order slips.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Brand Name</label>
                    <input
                        type="text"
                        name="brand_name"
                        value="{{ old('brand_name', data_get($branding, 'name', $tenant->name)) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-amber-500"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Boutique Tagline</label>
                    <input
                        type="text"
                        name="tagline"
                        value="{{ old('tagline', data_get($branding, 'tagline', 'Curated Boutique & Ready-to-Wear')) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-amber-500"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Brand Logo File</label>
                    <input
                        type="file"
                        name="logo_file"
                        accept="image/*"
                        class="block w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100 cursor-pointer"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Primary Color</label>
                    <div class="flex items-center gap-2">
                        <input
                            type="color"
                            name="primary_color"
                            value="{{ old('primary_color', data_get($branding, 'palette.primary', '#c5a880')) }}"
                            class="w-10 h-10 rounded-lg border border-slate-200 p-0.5 cursor-pointer"
                        />
                        <input
                            type="text"
                            value="{{ old('primary_color', data_get($branding, 'palette.primary', '#c5a880')) }}"
                            class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono"
                            readonly
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Secondary Color</label>
                    <div class="flex items-center gap-2">
                        <input
                            type="color"
                            name="secondary_color"
                            value="{{ old('secondary_color', data_get($branding, 'palette.secondary', '#1a1a1a')) }}"
                            class="w-10 h-10 rounded-lg border border-slate-200 p-0.5 cursor-pointer"
                        />
                        <input
                            type="text"
                            value="{{ old('secondary_color', data_get($branding, 'palette.secondary', '#1a1a1a')) }}"
                            class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono"
                            readonly
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Accent Accent Color</label>
                    <div class="flex items-center gap-2">
                        <input
                            type="color"
                            name="accent_color"
                            value="{{ old('accent_color', data_get($branding, 'palette.accent', '#e8d8c8')) }}"
                            class="w-10 h-10 rounded-lg border border-slate-200 p-0.5 cursor-pointer"
                        />
                        <input
                            type="text"
                            value="{{ old('accent_color', data_get($branding, 'palette.accent', '#e8d8c8')) }}"
                            class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono"
                            readonly
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Footer Save Bar -->
        <div class="sticky bottom-4 z-40 bg-slate-900/95 backdrop-blur-md rounded-2xl p-4 text-white flex items-center justify-between shadow-2xl border border-slate-800">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-xs font-medium text-slate-300">Changes reflect instantly on your live boutique storefront.</span>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('storefront') }}"
                    target="_blank"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition"
                >
                    Preview
                </a>
                <button
                    type="submit"
                    class="px-6 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold uppercase tracking-wider transition shadow-md shadow-blue-600/30 cursor-pointer active:scale-95"
                >
                    Save Storefront Changes
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(input, targetId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(targetId);
            if (img) {
                img.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</x-admin-layout>
