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

            // Contact & Concierge Chat
            $whatsappNumber = old('whatsapp_number', data_get($contact, 'whatsapp', $tenant->whatsappNumber()));
            $whatsappMessage = old('whatsapp_message', data_get($contact, 'whatsapp_message', $tenant->whatsappGreeting()));
            $customerCarePhone = old('customer_care_phone', data_get($contact, 'phone', $tenant->customerCarePhone()));
            $customerCareEmail = old('customer_care_email', data_get($contact, 'email', $tenant->customerCareEmail()));
            $chatLabel = old('chat_label', data_get($contact, 'chat_label', $tenant->conciergeChatLabel()));
            $chatEnabled = old('chat_enabled', data_get($contact, 'chat_enabled', $tenant->isConciergeChatEnabled()));
            $storeAddress = old('address', data_get($contact, 'address', $tenant->storefrontAddress()));
            $openingHours = old('opening_hours', data_get($contact, 'opening_hours', $tenant->storefrontOpeningHours()));

            // Editorial Badges & Storefront Lead
            $seasonBadge = old('season_badge', data_get($storefront, 'season_badge', $tenant->seasonBadge()));
            $locationBadge = old('location_badge', data_get($storefront, 'location_badge', $tenant->locationBadge()));
            $leadName = old('lead_name', data_get($storefront, 'lead_name', $tenant->leadName()));
            $leadRole = old('lead_role', data_get($storefront, 'lead_role', $tenant->leadRole()));
            $leadStatus = old('lead_status', data_get($storefront, 'lead_status', $tenant->leadStatus()));
            $aboutText = old('about_text', data_get($storefront, 'about_text', $tenant->aboutText()));
        @endphp

        <!-- Section 1: Live Concierge, WhatsApp & Customer Care -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span>1. Live Concierge, WhatsApp &amp; Customer Care</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Manage your customer messaging channels, instant WhatsApp order inquiries, and customer care lines.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                    <svg class="w-4 h-4 fill-current text-emerald-600 shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.301-.15-1.78-.879-2.056-.98-.276-.1-.476-.15-.677.15-.2.301-.777.98-.953 1.18-.175.201-.351.226-.652.075-.301-.15-1.272-.469-2.423-1.496-.896-.799-1.501-1.786-1.677-2.087-.175-.301-.019-.464.132-.614.136-.135.301-.351.451-.527.15-.175.201-.301.301-.502.1-.2.05-.376-.025-.526-.075-.15-.677-1.631-.928-2.238-.244-.59-.492-.51-.677-.52l-.577-.01c-.2 0-.526.075-.802.376s-1.053 1.028-1.053 2.507c0 1.48 1.078 2.908 1.229 3.109.15.2 2.121 3.238 5.138 4.542.718.31 1.278.496 1.715.635.722.23 1.378.197 1.898.12.579-.087 1.78-.727 2.03-1.43.251-.703.251-1.304.176-1.43-.076-.126-.276-.201-.577-.351zM12.04 2C6.527 2 2.05 6.477 2.05 11.99c0 1.82.49 3.528 1.344 5.003L2 22l5.143-1.348A9.94 9.94 0 0012.04 22c5.513 0 9.99-4.477 9.99-9.99A9.99 9.99 0 0012.04 2z"/></svg>
                    <span>Instant Customer Messaging</span>
                </div>
            </div>

            <!-- Floating Pill & Toggle -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            name="chat_enabled"
                            value="1"
                            {{ $chatEnabled ? 'checked' : '' }}
                            class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer"
                        />
                        <span class="text-xs font-bold text-slate-900">Enable Floating Concierge / WhatsApp Chat Pill</span>
                    </label>
                    <p class="text-[11px] text-slate-500">
                        When enabled, shoppers see a floating WhatsApp support pill in the bottom right corner of their screen.
                    </p>
                </div>

                <div class="sm:w-64">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Floating Pill Label</label>
                    <input
                        type="text"
                        name="chat_label"
                        value="{{ $chatLabel }}"
                        placeholder="e.g. Jos Concierge, Chat with Us"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- WhatsApp Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        WhatsApp Business Number
                        <span class="text-slate-400 font-normal">(for wa.me links &amp; instant orders)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4 text-emerald-600 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.301-.15-1.78-.879-2.056-.98-.276-.1-.476-.15-.677.15-.2.301-.777.98-.953 1.18-.175.201-.351.226-.652.075-.301-.15-1.272-.469-2.423-1.496-.896-.799-1.501-1.786-1.677-2.087-.175-.301-.019-.464.132-.614.136-.135.301-.351.451-.527.15-.175.201-.301.301-.502.1-.2.05-.376-.025-.526-.075-.15-.677-1.631-.928-2.238-.244-.59-.492-.51-.677-.52l-.577-.01c-.2 0-.526.075-.802.376s-1.053 1.028-1.053 2.507c0 1.48 1.078 2.908 1.229 3.109.15.2 2.121 3.238 5.138 4.542.718.31 1.278.496 1.715.635.722.23 1.378.197 1.898.12.579-.087 1.78-.727 2.03-1.43.251-.703.251-1.304.176-1.43-.076-.126-.276-.201-.577-.351zM12.04 2C6.527 2 2.05 6.477 2.05 11.99c0 1.82.49 3.528 1.344 5.003L2 22l5.143-1.348A9.94 9.94 0 0012.04 22c5.513 0 9.99-4.477 9.99-9.99A9.99 9.99 0 0012.04 2z"/></svg>
                        </div>
                        <input
                            type="text"
                            name="whatsapp_number"
                            value="{{ $whatsappNumber }}"
                            placeholder="e.g. +234 906 949 8376 or 09069498376"
                            class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500 font-mono"
                        />
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Accepts Nigerian local (090...) or international format (+234...). Cleaned automatically for wa.me links.</p>
                </div>

                <!-- Customer Care Phone -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Customer Care Phone Line
                        <span class="text-slate-400 font-normal">(displayed on footer &amp; location)</span>
                    </label>
                    <input
                        type="text"
                        name="customer_care_phone"
                        value="{{ $customerCarePhone }}"
                        placeholder="e.g. +234 906 949 8376"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500 font-mono"
                    />
                    <p class="text-[11px] text-slate-400 mt-1">Formatted phone number shown on the website footer and support badges.</p>
                </div>

                <!-- Customer Care Support Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Customer Care Support Email</label>
                    <input
                        type="email"
                        name="customer_care_email"
                        value="{{ $customerCareEmail }}"
                        placeholder="e.g. support@yourbrand.com"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500"
                    />
                </div>

                <!-- Store Opening Hours -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Store Opening Hours &amp; Days</label>
                    <input
                        type="text"
                        name="opening_hours"
                        value="{{ $openingHours }}"
                        placeholder="e.g. Open Mon – Sat, 9:00 AM – 7:00 PM"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500"
                    />
                </div>

                <!-- WhatsApp Default Message -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        WhatsApp Greeting / Default Inquiry Message
                    </label>
                    <input
                        type="text"
                        name="whatsapp_message"
                        value="{{ $whatsappMessage }}"
                        placeholder="e.g. Hello, I would like to inquire about your boutique collection."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500"
                    />
                    <p class="text-[11px] text-slate-400 mt-1">The pre-filled text in your shopper's WhatsApp app when they tap to chat with your store.</p>
                </div>

                <!-- Physical Store Address -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Physical Flagship Atelier / Store Address</label>
                    <input
                        type="text"
                        name="address"
                        value="{{ $storeAddress }}"
                        placeholder="e.g. Across Chrisbest Plaza, West of Mines, Jos, Plateau State, Nigeria"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500"
                    />
                    <p class="text-[11px] text-slate-400 mt-1">Displayed in the boutique footer and location guide for walk-in shoppers.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Editorial Badges & Boutique Lead -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                    <span>2. Editorial Badges &amp; Boutique Lead Byline</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Customize the luxury editorial badges, store pre-headline, and stylist/lead byline above your hero showcase.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Season Badge -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Season / Collection Badge
                        <span class="text-slate-400 font-normal">(Pink Pill Badge)</span>
                    </label>
                    <input
                        type="text"
                        name="season_badge"
                        value="{{ $seasonBadge }}"
                        placeholder="e.g. SS / 2026 Collection, Fall Atelier Drop"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-purple-500"
                    />
                </div>

                <!-- Location Badge -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Flagship / City Badge
                        <span class="text-slate-400 font-normal">(Grey Pill Badge)</span>
                    </label>
                    <input
                        type="text"
                        name="location_badge"
                        value="{{ $locationBadge }}"
                        placeholder="e.g. Flagship Atelier • West of Mines, Jos"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-purple-500"
                    />
                </div>

                <!-- Lead Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Boutique Lead / Stylist Name</label>
                    <input
                        type="text"
                        name="lead_name"
                        value="{{ $leadName }}"
                        placeholder="e.g. Jessi O., Amara K."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-purple-500"
                    />
                </div>

                <!-- Lead Role -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Lead Role / Designation</label>
                    <input
                        type="text"
                        name="lead_role"
                        value="{{ $leadRole }}"
                        placeholder="e.g. Lead, Creative Director, Founder"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-purple-500"
                    />
                </div>

                <!-- Live Status -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Live Atelier Status Line</label>
                    <input
                        type="text"
                        name="lead_status"
                        value="{{ $leadStatus }}"
                        placeholder="e.g. Jos Atelier Open, Walk-in Fitting Available, Online & Delivering"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-purple-500"
                    />
                    <p class="text-[11px] text-slate-400 mt-1">Displays alongside an animated pulsing indicator to show customers you are open for business.</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Hero Showcase & Visual Image -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <span>3. Main Hero Visual Showcase</span>
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

        <!-- Section 4: Featuring Images & Highlight Tiles -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <span>4. Featuring Images &amp; Promo Highlights</span>
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

        <!-- Section 5: Brand Identity & Boutique Narrative -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span>5. Boutique Narrative &amp; Brand Styling</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Colors and narrative story applied throughout your boutique footer, about sections, and checkout slips.
                </p>
            </div>

            <!-- About / Narrative Bio -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Store / Atelier Narrative Bio
                    <span class="text-slate-400 font-normal">(displayed in your website footer under the logo)</span>
                </label>
                <textarea
                    name="about_text"
                    rows="3"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-amber-500 leading-relaxed"
                    placeholder="Enter the craftsmanship story and bio of your brand..."
                >{{ $aboutText }}</textarea>
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
