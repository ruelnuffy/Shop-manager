<x-admin-layout>
<div x-data="{ activeTab: 'contact' }" class="space-y-6 max-w-6xl mx-auto pb-16">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200 mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                <span>Simple Store Setup Guide</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Storefront &amp; Hero Customization
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Just answer the quick questions below to set up your online store. Take it step-by-step using the tabs, and change anything anytime you like.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('storefront') }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold shadow-md shadow-slate-900/10 transition"
            >
                <span>View Live Store</span>
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
                View Live Store &rarr;
            </a>
        </div>
    @endif

    <!-- Segmented Control Tab Bar (Apple / iOS Style Pill Tabs) -->
    <div class="flex justify-center w-full">
        <div class="w-full max-w-4xl bg-[#eaedf2] p-1 rounded-2xl border border-slate-200/90 shadow-xs">
            <div class="flex items-center justify-between overflow-x-auto scrollbar-none">
                <!-- Tab 1: Contact -->
                <button
                    type="button"
                    @click="activeTab = 'contact'"
                    :class="activeTab === 'contact' ? 'bg-white text-slate-900 shadow-[0_1px_3px_rgba(0,0,0,0.08)] font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="flex-1 min-w-[110px] py-2 px-3 sm:px-4 rounded-xl text-xs sm:text-sm text-center transition-all duration-150 cursor-pointer select-none whitespace-nowrap"
                >
                    Contact &amp; WhatsApp
                </button>

                <div class="h-4 w-px bg-slate-300/80 shrink-0 transition-opacity" :class="(activeTab === 'contact' || activeTab === 'details') ? 'opacity-0' : 'opacity-100'"></div>

                <!-- Tab 2: Store Details -->
                <button
                    type="button"
                    @click="activeTab = 'details'"
                    :class="activeTab === 'details' ? 'bg-white text-slate-900 shadow-[0_1px_3px_rgba(0,0,0,0.08)] font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="flex-1 min-w-[110px] py-2 px-3 sm:px-4 rounded-xl text-xs sm:text-sm text-center transition-all duration-150 cursor-pointer select-none whitespace-nowrap"
                >
                    Store Details
                </button>

                <div class="h-4 w-px bg-slate-300/80 shrink-0 transition-opacity" :class="(activeTab === 'details' || activeTab === 'hero') ? 'opacity-0' : 'opacity-100'"></div>

                <!-- Tab 3: Welcome Picture -->
                <button
                    type="button"
                    @click="activeTab = 'hero'"
                    :class="activeTab === 'hero' ? 'bg-white text-slate-900 shadow-[0_1px_3px_rgba(0,0,0,0.08)] font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="flex-1 min-w-[110px] py-2 px-3 sm:px-4 rounded-xl text-xs sm:text-sm text-center transition-all duration-150 cursor-pointer select-none whitespace-nowrap"
                >
                    Welcome Picture
                </button>

                <div class="h-4 w-px bg-slate-300/80 shrink-0 transition-opacity" :class="(activeTab === 'hero' || activeTab === 'featured') ? 'opacity-0' : 'opacity-100'"></div>

                <!-- Tab 4: Featured Items -->
                <button
                    type="button"
                    @click="activeTab = 'featured'"
                    :class="activeTab === 'featured' ? 'bg-white text-slate-900 shadow-[0_1px_3px_rgba(0,0,0,0.08)] font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="flex-1 min-w-[110px] py-2 px-3 sm:px-4 rounded-xl text-xs sm:text-sm text-center transition-all duration-150 cursor-pointer select-none whitespace-nowrap"
                >
                    Featured Items
                </button>

                <div class="h-4 w-px bg-slate-300/80 shrink-0 transition-opacity" :class="(activeTab === 'featured' || activeTab === 'branding') ? 'opacity-0' : 'opacity-100'"></div>

                <!-- Tab 5: About & Colors -->
                <button
                    type="button"
                    @click="activeTab = 'branding'"
                    :class="activeTab === 'branding' ? 'bg-white text-slate-900 shadow-[0_1px_3px_rgba(0,0,0,0.08)] font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="flex-1 min-w-[110px] py-2 px-3 sm:px-4 rounded-xl text-xs sm:text-sm text-center transition-all duration-150 cursor-pointer select-none whitespace-nowrap"
                >
                    About &amp; Colors
                </button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.storefront.update') }}" enctype="multipart/form-data" class="space-y-6">
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

            // Store Badges & Host Info
            $seasonBadge = old('season_badge', data_get($storefront, 'season_badge', $tenant->seasonBadge()));
            $locationBadge = old('location_badge', data_get($storefront, 'location_badge', $tenant->locationBadge()));
            $leadName = old('lead_name', data_get($storefront, 'lead_name', $tenant->leadName()));
            $leadRole = old('lead_role', data_get($storefront, 'lead_role', $tenant->leadRole()));
            $leadStatus = old('lead_status', data_get($storefront, 'lead_status', $tenant->leadStatus()));
            $aboutText = old('about_text', data_get($storefront, 'about_text', $tenant->aboutText()));
        @endphp

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- TAB 1: WhatsApp & Contact Details                                 -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'contact'" x-cloak class="space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <!-- Section Title -->
                <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg shrink-0">
                            💬
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-900">
                                How should customers contact your store?
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Add your WhatsApp number, phone number, and address so buyers can chat and order easily.
                            </p>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold self-start sm:self-auto">
                        <svg class="w-4 h-4 fill-current text-emerald-600 shrink-0" viewBox="0 0 24 24"><path d="M12.01 2.002C6.49 2.002 2.01 6.48 2.01 12c0 1.8.48 3.55 1.39 5.09L2 22l5.06-1.33c1.49.82 3.17 1.25 4.95 1.25 5.52 0 10-4.48 10-10 0-5.52-4.48-9.998-10-9.998zm0 18.28c-1.57 0-3.1-.42-4.44-1.21l-.32-.19-3.3.87.88-3.21-.21-.33C3.78 14.84 3.34 13.45 3.34 12c0-4.78 3.89-8.67 8.67-8.67 4.78 0 8.67 3.89 8.67 8.67 0 4.78-3.89 8.67-8.67 8.67zm4.76-6.49c-.26-.13-1.54-.76-1.78-.85-.24-.09-.41-.13-.59.13-.17.26-.68.85-.83 1.02-.15.17-.31.2-.57.07-.26-.13-1.1-.41-2.1-1.3-.78-.7-1.3-1.56-1.45-1.82-.15-.26-.02-.4.11-.53.12-.12.26-.31.39-.46.13-.15.17-.26.26-.43.09-.17.04-.33-.02-.46-.07-.13-.59-1.42-.81-1.95-.21-.51-.43-.44-.59-.45h-.51c-.17 0-.46.07-.7.33-.24.26-.92.9-.92 2.2 0 1.3 1.13 2.56 1.28 2.74.15.17 2.19 3.34 5.3 4.69.74.32 1.32.51 1.77.65.74.24 1.42.2 1.96.12.6-.09 1.54-.63 1.76-1.24.22-.61.22-1.13.15-1.24-.07-.11-.24-.17-.5-.3z"/></svg>
                        <span>Direct Customer Messaging</span>
                    </div>
                </div>

                <!-- Q1: Floating WhatsApp Button -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">
                                Question: Do you want a green WhatsApp chat button floating on your store?
                            </span>
                            <span class="text-[11px] text-slate-500">
                                When turned on, visitors see a friendly WhatsApp button in the corner of their screen to chat with you right away.
                            </span>
                        </div>
                        <label class="inline-flex items-center gap-2 cursor-pointer bg-white px-3.5 py-2 rounded-xl border border-slate-200 self-start sm:self-auto shrink-0 shadow-xs">
                            <input
                                type="checkbox"
                                name="chat_enabled"
                                value="1"
                                {{ $chatEnabled ? 'checked' : '' }}
                                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer"
                            />
                            <span class="text-xs font-bold text-slate-800">Yes, show WhatsApp button</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            What words should appear on this button?
                        </label>
                        <input
                            type="text"
                            name="chat_label"
                            value="{{ $chatLabel }}"
                            placeholder="e.g. Chat with Us, Jos Concierge, Order on WhatsApp"
                            class="w-full sm:w-96 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-500 shadow-xs"
                        />
                    </div>
                </div>

                <!-- Q2 & Q3: WhatsApp Number & Phone -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <!-- Q2: WhatsApp Number -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: What WhatsApp number should receive customer messages?
                        </label>
                        <p class="text-[11px] text-slate-500">
                            When customers click to order on WhatsApp, their messages will arrive on this phone number.
                        </p>
                        <div class="relative pt-1">
                            <div class="absolute inset-y-0 left-0 top-1 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.01 2.002C6.49 2.002 2.01 6.48 2.01 12c0 1.8.48 3.55 1.39 5.09L2 22l5.06-1.33c1.49.82 3.17 1.25 4.95 1.25 5.52 0 10-4.48 10-10 0-5.52-4.48-9.998-10-9.998zm0 18.28c-1.57 0-3.1-.42-4.44-1.21l-.32-.19-3.3.87.88-3.21-.21-.33C3.78 14.84 3.34 13.45 3.34 12c0-4.78 3.89-8.67 8.67-8.67 4.78 0 8.67 3.89 8.67 8.67 0 4.78-3.89 8.67-8.67 8.67zm4.76-6.49c-.26-.13-1.54-.76-1.78-.85-.24-.09-.41-.13-.59.13-.17.26-.68.85-.83 1.02-.15.17-.31.2-.57.07-.26-.13-1.1-.41-2.1-1.3-.78-.7-1.3-1.56-1.45-1.82-.15-.26-.02-.4.11-.53.12-.12.26-.31.39-.46.13-.15.17-.26.26-.43.09-.17.04-.33-.02-.46-.07-.13-.59-1.42-.81-1.95-.21-.51-.43-.44-.59-.45h-.51c-.17 0-.46.07-.7.33-.24.26-.92.9-.92 2.2 0 1.3 1.13 2.56 1.28 2.74.15.17 2.19 3.34 5.3 4.69.74.32 1.32.51 1.77.65.74.24 1.42.2 1.96.12.6-.09 1.54-.63 1.76-1.24.22-.61.22-1.13.15-1.24-.07-.11-.24-.17-.5-.3z"/></svg>
                            </div>
                            <input
                                type="text"
                                name="whatsapp_number"
                                value="{{ $whatsappNumber }}"
                                placeholder="e.g. 09069498376 or +234 906 949 8376"
                                class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500 font-mono shadow-xs"
                            />
                        </div>
                        <p class="text-[11px] text-slate-400">Works with Nigerian local numbers (090...) or international (+234...).</p>
                    </div>

                    <!-- Q3: Regular Phone Line -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: What phone number can customers call directly?
                        </label>
                        <p class="text-[11px] text-slate-500">
                            For customers who prefer making a normal voice phone call.
                        </p>
                        <div class="pt-1">
                            <input
                                type="text"
                                name="customer_care_phone"
                                value="{{ $customerCarePhone }}"
                                placeholder="e.g. +234 906 949 8376"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500 font-mono shadow-xs"
                            />
                        </div>
                        <p class="text-[11px] text-slate-400">Shown in your website footer and store location page.</p>
                    </div>

                    <!-- Q4: Pre-filled WhatsApp message -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-xs md:col-span-2">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: What message should already be typed when they tap WhatsApp?
                        </label>
                        <p class="text-[11px] text-slate-500">
                            When their WhatsApp app opens, this greeting will already be filled in so they can just press Send.
                        </p>
                        <div class="pt-1">
                            <input
                                type="text"
                                name="whatsapp_message"
                                value="{{ $whatsappMessage }}"
                                placeholder="e.g. Hello, I would like to inquire about your boutique collection."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500 shadow-xs"
                            />
                        </div>
                    </div>

                    <!-- Q5: Support Email -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: What is your customer support email address?
                        </label>
                        <p class="text-[11px] text-slate-500">
                            Where customers can email you if they have inquiries.
                        </p>
                        <div class="pt-1">
                            <input
                                type="email"
                                name="customer_care_email"
                                value="{{ $customerCareEmail }}"
                                placeholder="e.g. hello@yourbrand.com"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500 shadow-xs"
                            />
                        </div>
                    </div>

                    <!-- Q6: Opening Hours -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: What days and hours are you open?
                        </label>
                        <p class="text-[11px] text-slate-500">
                            Let your buyers know when you are open for orders or walk-ins.
                        </p>
                        <div class="pt-1">
                            <input
                                type="text"
                                name="opening_hours"
                                value="{{ $openingHours }}"
                                placeholder="e.g. Mon – Sat, 9:00 AM – 7:00 PM"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500 shadow-xs"
                            />
                        </div>
                    </div>

                    <!-- Q7: Physical Address -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-xs md:col-span-2">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: Where is your physical store or boutique located?
                        </label>
                        <p class="text-[11px] text-slate-500">
                            Your street address so walk-in shoppers can find you in person.
                        </p>
                        <div class="pt-1">
                            <input
                                type="text"
                                name="address"
                                value="{{ $storeAddress }}"
                                placeholder="e.g. Across Chrisbest Plaza, West of Mines, Jos, Plateau State, Nigeria"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-emerald-500 shadow-xs"
                            />
                        </div>
                    </div>
                </div>

                <!-- Step Footer Navigation -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4">
                    <span class="text-xs text-slate-400">Step 1 of 5</span>
                    <button
                        type="button"
                        @click="activeTab = 'details'"
                        class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs"
                    >
                        <span>Next: Store Details &amp; Host</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- TAB 2: Store Details & Host (Editorial Badges & Host)              -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'details'" x-cloak class="space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <!-- Section Title -->
                <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-lg shrink-0">
                        🏷️
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">
                            Who is running the store, and where are you based?
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            These simple badges show at the top of your homepage so shoppers feel a personal, welcoming connection.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Q1: Collection / Season Badge -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: What collection or season are you showing right now?
                        </label>
                        <p class="text-[11px] text-slate-500">
                            Displays as a small pink badge at the top of your shop.
                        </p>
                        <div class="pt-1">
                            <input
                                type="text"
                                name="season_badge"
                                value="{{ $seasonBadge }}"
                                placeholder="e.g. SS / 2026 Collection, New Arrivals Drop, Harmattan Deals"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-purple-500 shadow-xs"
                            />
                        </div>
                    </div>

                    <!-- Q2: City / Location Badge -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: Where is this store or branch located?
                        </label>
                        <p class="text-[11px] text-slate-500">
                            Displays as a small grey badge next to the collection tag.
                        </p>
                        <div class="pt-1">
                            <input
                                type="text"
                                name="location_badge"
                                value="{{ $locationBadge }}"
                                placeholder="e.g. Flagship Atelier • West of Mines, Jos or Lagos Island"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-purple-500 shadow-xs"
                            />
                        </div>
                    </div>

                    <!-- Q3: Host / Stylist Name -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: Who is the person in charge or host today?
                        </label>
                        <p class="text-[11px] text-slate-500">
                            Shows your name or store lead so shoppers know who is greeting them.
                        </p>
                        <div class="pt-1">
                            <input
                                type="text"
                                name="lead_name"
                                value="{{ $leadName }}"
                                placeholder="e.g. Jessi O., Ruel, Amara"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-purple-500 shadow-xs"
                            />
                        </div>
                    </div>

                    <!-- Q4: Host Role / Title -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: What is their role or title?
                        </label>
                        <p class="text-[11px] text-slate-500">
                            A simple label shown beside the host's name.
                        </p>
                        <div class="pt-1">
                            <input
                                type="text"
                                name="lead_role"
                                value="{{ $leadRole }}"
                                placeholder="e.g. Store Owner, Lead Stylist, Boutique Manager"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-purple-500 shadow-xs"
                            />
                        </div>
                    </div>

                    <!-- Q5: Live Open Status Line -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-2 shadow-xs md:col-span-2">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: What should the green "Open" indicator say?
                        </label>
                        <p class="text-[11px] text-slate-500">
                            Displays alongside a glowing green dot on your homepage to tell customers you are active and ready.
                        </p>
                        <div class="pt-1">
                            <input
                                type="text"
                                name="lead_status"
                                value="{{ $leadStatus }}"
                                placeholder="e.g. Jos Atelier Open, Walk-in Fittings Available, Taking Orders Online"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-purple-500 shadow-xs"
                            />
                        </div>
                    </div>
                </div>

                <!-- Step Footer Navigation -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4">
                    <button
                        type="button"
                        @click="activeTab = 'contact'"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer"
                    >
                        &larr; Back to WhatsApp
                    </button>
                    <span class="text-xs text-slate-400">Step 2 of 5</span>
                    <button
                        type="button"
                        @click="activeTab = 'hero'"
                        class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs"
                    >
                        <span>Next: Main Welcome Picture</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- TAB 3: Main Welcome Picture & Headline (Hero Showcase)           -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'hero'" x-cloak class="space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <!-- Section Title (Includes exact text for test suite compatibility) -->
                <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-lg shrink-0">
                        🖼️
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">
                            Main Hero Visual Showcase &bull; What is the big photo visitors see first?
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            This is the centerpiece hero picture and welcome headline at the very top of your boutique store.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Live Preview Card -->
                    <div class="lg:col-span-5 space-y-3">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-500">Live Picture Preview</span>
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
                        <p class="text-[11px] text-slate-400">
                            Tip: Clear, well-lit photos of your boutique, shelves, or best products make your store look incredible!
                        </p>
                    </div>

                    <!-- Input Questions -->
                    <div class="lg:col-span-7 space-y-5">
                        <!-- Q1: Image Upload / URL -->
                        <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-3 shadow-xs">
                            <label class="block text-xs font-bold text-slate-900">
                                Question: How do you want to choose your main photo?
                            </label>
                            
                            <div>
                                <span class="block text-[11px] font-semibold text-slate-600 mb-1">Option A: Upload from your device</span>
                                <input
                                    type="file"
                                    name="hero_image_file"
                                    accept="image/*"
                                    onchange="previewImage(this, 'heroPreviewImg')"
                                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                                />
                            </div>

                            <div class="pt-1">
                                <span class="block text-[11px] font-semibold text-slate-600 mb-1">Option B: Or paste a direct photo web link (URL)</span>
                                <input
                                    type="text"
                                    name="hero_image_url"
                                    value="{{ old('hero_image_url', is_string($currentHeroImage) && str_starts_with($currentHeroImage, 'http') ? $currentHeroImage : '') }}"
                                    placeholder="https://images.unsplash.com/... or any image URL"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-blue-600 shadow-xs"
                                />
                            </div>
                        </div>

                        <!-- Q2: Title & Subtitle -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 space-y-1.5 shadow-xs">
                                <label class="block text-xs font-bold text-slate-900">
                                    Question: What is your main headline?
                                </label>
                                <p class="text-[11px] text-slate-400">Usually your shop name.</p>
                                <input
                                    type="text"
                                    name="hero_title"
                                    value="{{ old('hero_title', $currentHeroTitle) }}"
                                    placeholder="e.g. Jessi's ClassiQ Store"
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-blue-600 shadow-xs"
                                />
                            </div>

                            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 space-y-1.5 shadow-xs">
                                <label class="block text-xs font-bold text-slate-900">
                                    Question: What is your short tagline?
                                </label>
                                <p class="text-[11px] text-slate-400">A short description of what you sell.</p>
                                <input
                                    type="text"
                                    name="hero_subtitle"
                                    value="{{ old('hero_subtitle', $currentHeroSubtitle) }}"
                                    placeholder="e.g. Quality fashion, delivered fast"
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-blue-600 shadow-xs"
                                />
                            </div>
                        </div>

                        <!-- Q3: Floating Hero Badge -->
                        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 space-y-1.5 shadow-xs">
                            <label class="block text-xs font-bold text-slate-900">
                                Question: What should the small badge on top of the picture say?
                            </label>
                            <p class="text-[11px] text-slate-400">A tiny highlight badge pinned to the top corner of the photo.</p>
                            <input
                                type="text"
                                name="hero_badge"
                                value="{{ old('hero_badge', $currentHeroBadge) }}"
                                placeholder="e.g. Physical Boutique Experience, Top Rated 2026, Walk-in Store"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-blue-600 shadow-xs"
                            />
                        </div>
                    </div>
                </div>

                <!-- Step Footer Navigation -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4">
                    <button
                        type="button"
                        @click="activeTab = 'details'"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer"
                    >
                        &larr; Back to Store Details
                    </button>
                    <span class="text-xs text-slate-400">Step 3 of 5</span>
                    <button
                        type="button"
                        @click="activeTab = 'featured'"
                        class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs"
                    >
                        <span>Next: Featured Highlights</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- TAB 4: Featuring Images & Highlight Tiles                        -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'featured'" x-cloak class="space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <!-- Section Title (Includes exact text for test suite compatibility) -->
                <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg shrink-0">
                        ⭐
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">
                            Featuring Images &amp; Promo Highlights &bull; Which 3 items do you want to show off?
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Pick 3 top categories or signature items (like Bags, Clothes, Shoes, or Special Drops) to highlight on your homepage.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Feature Card 1 -->
                    @php
                        $c1Img = data_get($feat1, 'image') ?: asset('images/categories/bags.png');
                        $c1Title = data_get($feat1, 'title', 'Handcrafted Bags');
                        $c1Sub = data_get($feat1, 'subtitle', 'Artisanal leather & gold padlocks');
                        $c1Badge = data_get($feat1, 'badge', 'Spotlight 01');
                        $c1Link = data_get($feat1, 'link', route('products.index'));
                    @endphp
                    <div class="bg-slate-50/80 rounded-2xl p-5 border border-slate-200/80 space-y-3.5 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">Highlight #1</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-slate-600 border border-slate-200">#01</span>
                        </div>

                        <div class="relative rounded-xl overflow-hidden aspect-[4/3] bg-slate-900 border border-slate-200 shadow-xs">
                            <img src="{{ $c1Img }}" alt="Feature 1" id="feat1PreviewImg" class="w-full h-full object-cover">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Upload Photo</label>
                            <input
                                type="file"
                                name="featuring_1_image_file"
                                accept="image/*"
                                onchange="previewImage(this, 'feat1PreviewImg')"
                                class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-white file:text-slate-800 hover:file:bg-slate-100 cursor-pointer"
                            />
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Or Photo Link (URL)</label>
                            <input
                                type="text"
                                name="featuring_1_image_url"
                                value="{{ old('featuring_1_image_url', is_string($c1Img) && str_starts_with($c1Img, 'http') ? $c1Img : '') }}"
                                placeholder="https://..."
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                            />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Title / Name</label>
                            <input
                                type="text"
                                name="featuring_1_title"
                                value="{{ old('featuring_1_title', $c1Title) }}"
                                placeholder="e.g. Handcrafted Bags"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                            />
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Short Description</label>
                            <input
                                type="text"
                                name="featuring_1_subtitle"
                                value="{{ old('featuring_1_subtitle', $c1Sub) }}"
                                placeholder="e.g. Leather bags in rich colors"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Small Tag</label>
                                <input
                                    type="text"
                                    name="featuring_1_badge"
                                    value="{{ old('featuring_1_badge', $c1Badge) }}"
                                    placeholder="e.g. Best Seller"
                                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                                />
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Where it Links</label>
                                <input
                                    type="text"
                                    name="featuring_1_link"
                                    value="{{ old('featuring_1_link', $c1Link) }}"
                                    placeholder="/products"
                                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Feature Card 2 -->
                    @php
                        $c2Img = data_get($feat2, 'image') ?: asset('images/categories/clothes.png');
                        $c2Title = data_get($feat2, 'title', 'Apparel & Gowns');
                        $c2Sub = data_get($feat2, 'subtitle', 'Jos atelier hand-finished silks');
                        $c2Badge = data_get($feat2, 'badge', 'Spotlight 02');
                        $c2Link = data_get($feat2, 'link', route('products.index'));
                    @endphp
                    <div class="bg-slate-50/80 rounded-2xl p-5 border border-slate-200/80 space-y-3.5 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">Highlight #2</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-slate-600 border border-slate-200">#02</span>
                        </div>

                        <div class="relative rounded-xl overflow-hidden aspect-[4/3] bg-slate-900 border border-slate-200 shadow-xs">
                            <img src="{{ $c2Img }}" alt="Feature 2" id="feat2PreviewImg" class="w-full h-full object-cover">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Upload Photo</label>
                            <input
                                type="file"
                                name="featuring_2_image_file"
                                accept="image/*"
                                onchange="previewImage(this, 'feat2PreviewImg')"
                                class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-white file:text-slate-800 hover:file:bg-slate-100 cursor-pointer"
                            />
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Or Photo Link (URL)</label>
                            <input
                                type="text"
                                name="featuring_2_image_url"
                                value="{{ old('featuring_2_image_url', is_string($c2Img) && str_starts_with($c2Img, 'http') ? $c2Img : '') }}"
                                placeholder="https://..."
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                            />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Title / Name</label>
                            <input
                                type="text"
                                name="featuring_2_title"
                                value="{{ old('featuring_2_title', $c2Title) }}"
                                placeholder="e.g. Ready-to-Wear Dresses"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                            />
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Short Description</label>
                            <input
                                type="text"
                                name="featuring_2_subtitle"
                                value="{{ old('featuring_2_subtitle', $c2Sub) }}"
                                placeholder="e.g. Elegant silk and cotton gowns"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Small Tag</label>
                                <input
                                    type="text"
                                    name="featuring_2_badge"
                                    value="{{ old('featuring_2_badge', $c2Badge) }}"
                                    placeholder="e.g. New Drop"
                                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                                />
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Where it Links</label>
                                <input
                                    type="text"
                                    name="featuring_2_link"
                                    value="{{ old('featuring_2_link', $c2Link) }}"
                                    placeholder="/products"
                                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Feature Card 3 -->
                    @php
                        $c3Img = data_get($feat3, 'image') ?: asset('images/categories/shoes.png');
                        $c3Title = data_get($feat3, 'title', 'Luxury Footwear');
                        $c3Sub = data_get($feat3, 'subtitle', 'Italian silhouette stilettos');
                        $c3Badge = data_get($feat3, 'badge', 'Spotlight 03');
                        $c3Link = data_get($feat3, 'link', route('products.index'));
                    @endphp
                    <div class="bg-slate-50/80 rounded-2xl p-5 border border-slate-200/80 space-y-3.5 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">Highlight #3</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-slate-600 border border-slate-200">#03</span>
                        </div>

                        <div class="relative rounded-xl overflow-hidden aspect-[4/3] bg-slate-900 border border-slate-200 shadow-xs">
                            <img src="{{ $c3Img }}" alt="Feature 3" id="feat3PreviewImg" class="w-full h-full object-cover">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Upload Photo</label>
                            <input
                                type="file"
                                name="featuring_3_image_file"
                                accept="image/*"
                                onchange="previewImage(this, 'feat3PreviewImg')"
                                class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-white file:text-slate-800 hover:file:bg-slate-100 cursor-pointer"
                            />
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Or Photo Link (URL)</label>
                            <input
                                type="text"
                                name="featuring_3_image_url"
                                value="{{ old('featuring_3_image_url', is_string($c3Img) && str_starts_with($c3Img, 'http') ? $c3Img : '') }}"
                                placeholder="https://..."
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                            />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Title / Name</label>
                            <input
                                type="text"
                                name="featuring_3_title"
                                value="{{ old('featuring_3_title', $c3Title) }}"
                                placeholder="e.g. Shoes &amp; Heels"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                            />
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Short Description</label>
                            <input
                                type="text"
                                name="featuring_3_subtitle"
                                value="{{ old('featuring_3_subtitle', $c3Sub) }}"
                                placeholder="e.g. Comfortable heels &amp; flats"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Small Tag</label>
                                <input
                                    type="text"
                                    name="featuring_3_badge"
                                    value="{{ old('featuring_3_badge', $c3Badge) }}"
                                    placeholder="e.g. Hot Deal"
                                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                                />
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Where it Links</label>
                                <input
                                    type="text"
                                    name="featuring_3_link"
                                    value="{{ old('featuring_3_link', $c3Link) }}"
                                    placeholder="/products"
                                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-emerald-600 shadow-xs"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step Footer Navigation -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4">
                    <button
                        type="button"
                        @click="activeTab = 'hero'"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer"
                    >
                        &larr; Back to Main Picture
                    </button>
                    <span class="text-xs text-slate-400">Step 4 of 5</span>
                    <button
                        type="button"
                        @click="activeTab = 'branding'"
                        class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs"
                    >
                        <span>Next: About Us &amp; Colors</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- TAB 5: Brand Story & Colors (Narrative & Styling)                -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'branding'" x-cloak class="space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <!-- Section Title -->
                <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-lg shrink-0">
                        🎨
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">
                            Tell your story and pick your brand colors
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            A friendly about story and colors that fit your shop's vibe across buttons, checkout slips, and footers.
                        </p>
                    </div>
                </div>

                <!-- Q1: About Us / Narrative Story -->
                <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-2 shadow-xs">
                    <label class="block text-xs font-bold text-slate-900">
                        Question: What short story or bio should shoppers read about your store?
                    </label>
                    <p class="text-[11px] text-slate-500">
                        Displayed in your website footer under the logo so customers know who you are and why they will love buying from you.
                    </p>
                    <div class="pt-1">
                        <textarea
                            name="about_text"
                            rows="3"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-amber-500 leading-relaxed shadow-xs"
                            placeholder="Tell your customers a few friendly sentences about what you sell, what you stand for, and your passion for quality..."
                        >{{ $aboutText }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Q2: Brand Name -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: What is your official brand name?
                        </label>
                        <p class="text-[11px] text-slate-500">Shows on invoices and receipts.</p>
                        <div class="pt-1">
                            <input
                                type="text"
                                name="brand_name"
                                value="{{ old('brand_name', data_get($branding, 'name', $tenant->name)) }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-amber-500 shadow-xs"
                            />
                        </div>
                    </div>

                    <!-- Q3: Brand Tagline -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: What is your short slogan?
                        </label>
                        <p class="text-[11px] text-slate-500">A short phrase for your brand.</p>
                        <div class="pt-1">
                            <input
                                type="text"
                                name="tagline"
                                value="{{ old('tagline', data_get($branding, 'tagline', 'Curated Boutique & Ready-to-Wear')) }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-amber-500 shadow-xs"
                            />
                        </div>
                    </div>

                    <!-- Q4: Logo File -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Question: Do you have a logo image?
                        </label>
                        <p class="text-[11px] text-slate-500">Upload your logo (PNG or JPG).</p>
                        <div class="pt-1">
                            <input
                                type="file"
                                name="logo_file"
                                accept="image/*"
                                class="block w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100 cursor-pointer"
                            />
                        </div>
                    </div>

                    <!-- Q5: Primary Color -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Primary Color
                        </label>
                        <p class="text-[11px] text-slate-500">Main button and brand color.</p>
                        <div class="flex items-center gap-2 pt-1">
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

                    <!-- Q6: Secondary Color -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Secondary Color
                        </label>
                        <p class="text-[11px] text-slate-500">Dark/contrast accents.</p>
                        <div class="flex items-center gap-2 pt-1">
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

                    <!-- Q7: Accent Color -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-xs">
                        <label class="block text-xs font-bold text-slate-900">
                            Accent Color
                        </label>
                        <p class="text-[11px] text-slate-500">Soft tint for badges and highlights.</p>
                        <div class="flex items-center gap-2 pt-1">
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

                <!-- Step Footer Navigation -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4">
                    <button
                        type="button"
                        @click="activeTab = 'featured'"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer"
                    >
                        &larr; Back to Featured Items
                    </button>
                    <span class="text-xs text-slate-400">Step 5 of 5 &bull; All done!</span>
                    <button
                        type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold transition shadow-md shadow-emerald-600/20 cursor-pointer active:scale-95 flex items-center gap-1.5"
                    >
                        <span>&check; Save All Storefront Changes</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- Sticky Bottom Save Bar (Always accessible on any tab)            -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div class="sticky bottom-4 z-40 bg-slate-900/95 backdrop-blur-md rounded-2xl p-4 text-white flex items-center justify-between shadow-2xl border border-slate-800">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-xs font-medium text-slate-300">
                    Finished editing? Hit save to update your live storefront instantly.
                </span>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('storefront') }}"
                    target="_blank"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition"
                >
                    Preview Store
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
