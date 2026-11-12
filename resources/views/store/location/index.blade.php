<x-app-layout>
    @php
        $tenant = app(\App\Services\CurrentTenant::class)->get();
        $tenantWhatsapp = $tenant ? $tenant->whatsappNumber() : '2349069498376';
        $tenantPhone = $tenant ? $tenant->customerCarePhone() : '+234 906 949 8376';
        $tenantBrand = $tenant ? $tenant->setting('branding.name', $tenant->name) : 'Boutique';
        $locationMsg = "Hello {$tenantBrand}, I'd like to inquire about visiting your store location.";
        $locationWhatsappUrl = 'https://wa.me/' . $tenantWhatsapp . '?text=' . rawurlencode($locationMsg);
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-charcoal/50 block mb-1">
                    Flagship Store Locator
                </span>
                <h1 class="font-display font-extrabold text-3xl sm:text-4xl uppercase tracking-tight text-brand-charcoal">
                    VISIT OUR BOUTIQUE
                </h1>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-bold uppercase tracking-wider text-brand-charcoal hover:text-brand-pink underline underline-offset-4">
                &larr; Shop Online Catalog
            </a>
        </div>
    </x-slot>

    <!-- Leaflet CSS & JS via CDN with robust fallback -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

    <script>
        function boutiqueLocator() {
            return {
                storeLat: {{ (float) ($primaryLocation->latitude ?? 9.9211) }},
                storeLng: {{ (float) ($primaryLocation->longitude ?? 8.8825) }},
                storeName: @js($primaryLocation->name ?? (app(\App\Services\CurrentTenant::class)->get()?->setting('branding.name') ?? config('app.platform_name', 'ShopManager'))),
                storeAddress: @js(($primaryLocation->address ?? 'Across Chrisbest Plaza, West of Mines') . ', ' . ($primaryLocation->city ?? 'Jos') . ', ' . ($primaryLocation->state ?? 'Plateau State')),
                userLat: null,
                userLng: null,
                distanceKm: null,
                locating: false,
                locationError: null,
                map: null,
                mapView: 'interactive',
                leafletLoaded: false,

                init() {
                    this.initMapWithRetry();
                },

                initMapWithRetry() {
                    if (typeof L !== 'undefined') {
                        this.setupLeaflet();
                    } else {
                        let attempts = 0;
                        const interval = setInterval(() => {
                            attempts++;
                            if (typeof L !== 'undefined') {
                                clearInterval(interval);
                                this.setupLeaflet();
                            } else if (attempts > 20) {
                                clearInterval(interval);
                                this.mapView = 'google';
                            }
                        }, 100);
                    }
                },

                setupLeaflet() {
                    try {
                        const mapEl = document.getElementById('store-map');
                        if (!mapEl || this.map) return;

                        this.map = L.map('store-map').setView([this.storeLat, this.storeLng], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                        }).addTo(this.map);

                        const storeMarker = L.marker([this.storeLat, this.storeLng]).addTo(this.map);
                        storeMarker.bindPopup(`<strong>${this.storeName}</strong><br>${this.storeAddress}`).openPopup();

                        this.leafletLoaded = true;

                        setTimeout(() => {
                            if (this.map) this.map.invalidateSize();
                        }, 300);
                    } catch (e) {
                        console.warn('Leaflet map error, switching to Google Maps:', e);
                        this.mapView = 'google';
                    }
                },

                locateUser() {
                    if (!navigator.geolocation) {
                        this.locationError = 'Geolocation is not supported by your browser.';
                        return;
                    }
                    this.locating = true;
                    this.locationError = null;

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.userLat = position.coords.latitude;
                            this.userLng = position.coords.longitude;
                            this.locating = false;

                            const R = 6371;
                            const dLat = (this.storeLat - this.userLat) * Math.PI / 180;
                            const dLon = (this.storeLng - this.userLng) * Math.PI / 180;
                            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                                      Math.cos(this.userLat * Math.PI / 180) * Math.cos(this.storeLat * Math.PI / 180) *
                                      Math.sin(dLon/2) * Math.sin(dLon/2);
                            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                            this.distanceKm = (R * c).toFixed(1);

                            if (this.map && typeof L !== 'undefined') {
                                const userMarker = L.marker([this.userLat, this.userLng]).addTo(this.map);
                                userMarker.bindPopup('<strong>Your Location</strong>').openPopup();

                                const group = new L.featureGroup([
                                    L.marker([this.storeLat, this.storeLng]),
                                    L.marker([this.userLat, this.userLng])
                                ]);
                                this.map.fitBounds(group.getBounds().pad(0.3));
                            }
                        },
                        (error) => {
                            this.locating = false;
                            this.locationError = 'Unable to retrieve your location. Please check browser permissions.';
                        },
                        { timeout: 10000, enableHighAccuracy: true }
                    );
                }
            };
        }
    </script>

    <div class="py-12" x-data="boutiqueLocator()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <!-- Boutique Location Info & Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-8 border border-black/[0.06] shadow-sm space-y-6">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black text-white">
                            Primary Flagship
                        </span>
                        <span class="text-xs text-emerald-600 font-bold">&bull; Walk-ins Welcome</span>
                    </div>

                    <h2 class="font-display font-extrabold text-3xl uppercase tracking-tight text-brand-charcoal">
                        {{ $primaryLocation->name }}
                    </h2>

                    <div class="space-y-3 text-xs text-brand-charcoal/70 leading-relaxed">
                        <p class="flex items-start gap-2.5">
                            <span class="p-1 rounded-md bg-black/5 text-brand-charcoal mt-0.5 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <span><strong class="text-brand-charcoal">Address:</strong><br>{{ $primaryLocation->address }}, {{ $primaryLocation->city }}, {{ $primaryLocation->state }}</span>
                        </p>
                        <p class="flex items-start gap-2.5">
                            <span class="p-1 rounded-md bg-black/5 text-brand-charcoal mt-0.5 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <span><strong class="text-brand-charcoal">Opening Hours:</strong><br>{{ $primaryLocation->opening_hours }}</span>
                        </p>
                        <p class="flex items-start gap-2.5">
                            <span class="p-1 rounded-md bg-black/5 text-brand-charcoal mt-0.5 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </span>
                            <span><strong class="text-brand-charcoal">Phone:</strong><br>{{ $primaryLocation->phone }}</span>
                        </p>
                    </div>

                    <!-- Geolocation Trigger & Calculated Distance -->
                    <div class="pt-4 border-t border-black/[0.06] space-y-3">
                        <button type="button" @click="locateUser()" :disabled="locating" class="w-full py-3 px-4 rounded-full bg-[#f4f4f4] hover:bg-black hover:text-white font-bold text-xs uppercase tracking-wider transition duration-150 flex items-center justify-center gap-2 cursor-pointer">
                            <span x-show="!locating" class="inline-flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                </svg>
                                Calculate Distance From My Location
                            </span>
                            <span x-show="locating">Calculating GPS coordinates...</span>
                        </button>

                        <div x-show="distanceKm" x-cloak class="p-3.5 rounded-2xl bg-brand-pink-50 border border-brand-pink-200 text-xs text-brand-pink-900 font-semibold text-center">
                            You are approximately <strong class="font-extrabold text-sm text-brand-pink-900" x-text="distanceKm"></strong> km away from our boutique!
                        </div>

                        <div x-show="locationError" x-cloak class="p-3 rounded-xl bg-red-50 text-red-700 text-xs font-medium">
                            <span x-text="locationError"></span>
                        </div>

                        <a href="{{ $locationWhatsappUrl }}" target="_blank" class="w-full py-3 px-4 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition shadow-sm">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12.01 2.002C6.49 2.002 2.01 6.48 2.01 12c0 1.8.48 3.55 1.39 5.09L2 22l5.06-1.33c1.49.82 3.17 1.25 4.95 1.25 5.52 0 10-4.48 10-10 0-5.52-4.48-9.998-10-9.998zm0 18.28c-1.57 0-3.1-.42-4.44-1.21l-.32-.19-3.3.87.88-3.21-.21-.33C3.78 14.84 3.34 13.45 3.34 12c0-4.78 3.89-8.67 8.67-8.67 4.78 0 8.67 3.89 8.67 8.67 0 4.78-3.89 8.67-8.67 8.67zm4.76-6.49c-.26-.13-1.54-.76-1.78-.85-.24-.09-.41-.13-.59.13-.17.26-.68.85-.83 1.02-.15.17-.31.2-.57.07-.26-.13-1.1-.41-2.1-1.3-.78-.7-1.3-1.56-1.45-1.82-.15-.26-.02-.4.11-.53.12-.12.26-.31.39-.46.13-.15.17-.26.26-.43.09-.17.04-.33-.02-.46-.07-.13-.59-1.42-.81-1.95-.21-.51-.43-.44-.59-.45h-.51c-.17 0-.46.07-.7.33-.24.26-.92.9-.92 2.2 0 1.3 1.13 2.56 1.28 2.74.15.17 2.19 3.34 5.3 4.69.74.32 1.32.51 1.77.65.74.24 1.42.2 1.96.12.6-.09 1.54-.63 1.76-1.24.22-.61.22-1.13.15-1.24-.07-.11-.24-.17-.5-.3z"/></svg>
                            <span>WhatsApp Support: {{ $tenantPhone }}</span>
                        </a>

                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <a :href="'https://maps.google.com/?daddr=' + storeLat + ',' + storeLng" target="_blank" class="py-3 px-4 rounded-full bg-black hover:bg-brand-pink text-white font-bold text-xs uppercase tracking-wider text-center transition">
                                Google Maps &rarr;
                            </a>
                            <a :href="'https://maps.apple.com/?daddr=' + storeLat + ',' + storeLng" target="_blank" class="py-3 px-4 rounded-full border border-black/15 hover:bg-black/5 text-brand-charcoal font-bold text-xs uppercase tracking-wider text-center transition">
                                Apple Maps &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Interactive Leaflet & Google Maps Frame -->
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-3xl overflow-hidden border border-black/[0.06] shadow-sm p-3 space-y-3">
                        <!-- Map Controls & Header -->
                        <div class="flex items-center justify-between px-2 pt-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-brand-charcoal">
                                    Jos Showroom Live Map
                                </span>
                            </div>
                            <div class="flex items-center bg-[#f4f4f4] rounded-full p-1 text-[11px] font-bold">
                                <button
                                    type="button"
                                    @click="mapView = 'interactive'; $nextTick(() => { if (map) map.invalidateSize(); })"
                                    :class="mapView === 'interactive' ? 'bg-black text-white shadow-sm' : 'text-brand-charcoal hover:text-black'"
                                    class="px-3 py-1 rounded-full transition cursor-pointer"
                                >
                                    Interactive
                                </button>
                                <button
                                    type="button"
                                    @click="mapView = 'google'"
                                    :class="mapView === 'google' ? 'bg-black text-white shadow-sm' : 'text-brand-charcoal hover:text-black'"
                                    class="px-3 py-1 rounded-full transition cursor-pointer"
                                >
                                    Google Maps
                                </button>
                            </div>
                        </div>

                        <!-- Interactive Leaflet Map Container -->
                        <div x-show="mapView === 'interactive'" class="relative">
                            <div id="store-map" class="w-full h-[500px] rounded-2xl z-10 bg-[#f4f4f4]"></div>
                            <div x-show="!leafletLoaded" class="absolute inset-0 bg-[#f4f4f4] rounded-2xl flex flex-col items-center justify-center gap-2 text-brand-charcoal/60 text-xs">
                                <svg class="w-6 h-6 animate-spin text-brand-charcoal" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Loading map view...</span>
                            </div>
                        </div>

                        <!-- Google Maps Embed Fallback -->
                        <div x-show="mapView === 'google'" class="w-full h-[500px] rounded-2xl overflow-hidden bg-[#f4f4f4]">
                            <iframe
                                class="w-full h-full border-0 rounded-2xl"
                                src="https://maps.google.com/maps?q={{ urlencode(($primaryLocation->address ?? '') . ', ' . ($primaryLocation->city ?? '') . ', ' . ($primaryLocation->state ?? '')) }}&t=&z=16&ie=UTF8&iwloc=&output=embed"
                                loading="lazy"
                                allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"
                            ></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Virtual Boutique Walkthrough Video Gallery -->
            <div class="bg-black text-white rounded-3xl p-6 sm:p-12 shadow-2xl space-y-8" x-data="{
                activeTourVideo: '{{ asset('videos/shop/boutique-interior-tour.mp4') }}',
                activeTourTitle: 'Flagship Studio & Luxury Handbag Salon Walkthrough',
                activeTourDesc: 'Take a virtual walk through our Jos showroom across Chrisbest Plaza in West of Mines, featuring hand-selected designer bags, ambient neon ceiling lighting, and private client mirror consoles.',
                switchTour(url, title, desc) {
                    this.activeTourVideo = url;
                    this.activeTourTitle = title;
                    this.activeTourDesc = desc;
                }
            }">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-white/10 pb-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-brand-pink-200 border border-white/15 mb-3">
                            <svg class="w-3 h-3 text-brand-pink" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Virtual Boutique Tour
                        </div>
                        <h2 class="font-display font-extrabold text-2xl sm:text-4xl tracking-tight text-white">
                            Step Inside Jessi's <span class="text-brand-pink">ClassiQ</span> Store
                        </h2>
                    </div>
                    <p class="text-xs text-white/60 max-w-md font-medium">
                        Preview the actual in-store atmosphere, lighting, and showroom displays before planning your private consultation or walk-in visit.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Video Player Display -->
                    <div class="lg:col-span-8">
                        <div class="aspect-[16/10] rounded-2xl overflow-hidden bg-neutral-900 border border-white/20 shadow-2xl relative">
                            <video
                                :src="activeTourVideo"
                                controls
                                autoplay
                                muted
                                loop
                                playsinline
                                class="w-full h-full object-cover"
                            ></video>
                        </div>
                    </div>

                    <!-- Tour Clip Navigation & Descriptions -->
                    <div class="lg:col-span-4 space-y-4">
                        <div class="space-y-2">
                            <h3 class="font-display font-bold text-lg text-white" x-text="activeTourTitle"></h3>
                            <p class="text-xs text-white/70 leading-relaxed" x-text="activeTourDesc"></p>
                        </div>

                        <div class="pt-4 space-y-3">
                            <button
                                type="button"
                                @click="switchTour('{{ asset('videos/shop/boutique-interior-tour.mp4') }}', 'Flagship Studio & Luxury Handbag Salon Walkthrough', 'Take a virtual walk through our Jos showroom across Chrisbest Plaza in West of Mines, featuring hand-selected designer bags, ambient neon ceiling lighting, and private client mirror consoles.')"
                                class="w-full p-3.5 rounded-2xl text-left border transition duration-150 flex items-center gap-3"
                                :class="activeTourVideo.includes('interior-tour') ? 'bg-white text-black border-white shadow-lg' : 'bg-white/5 text-white/80 border-white/10 hover:bg-white/10'"
                            >
                                <span class="w-8 h-8 rounded-full bg-brand-pink text-white flex items-center justify-center font-bold text-xs shrink-0">1</span>
                                <div>
                                    <div class="text-xs font-bold">Studio &amp; Salon Tour</div>
                                    <div class="text-[10px] opacity-70">360° Showroom overview</div>
                                </div>
                            </button>

                            <button
                                type="button"
                                @click="switchTour('{{ asset('videos/shop/boutique-shelves-showcase.mp4') }}', 'Artisan Handbag Showcase Shelves', 'High-definition close up of our luxury boutique shelves displaying croc totes, Birkin-inspired padlock bags, and quilted evening clutches.')"
                                class="w-full p-3.5 rounded-2xl text-left border transition duration-150 flex items-center gap-3"
                                :class="activeTourVideo.includes('shelves-showcase') ? 'bg-white text-black border-white shadow-lg' : 'bg-white/5 text-white/80 border-white/10 hover:bg-white/10'"
                            >
                                <span class="w-8 h-8 rounded-full bg-brand-pink text-white flex items-center justify-center font-bold text-xs shrink-0">2</span>
                                <div>
                                    <div class="text-xs font-bold">Designer Shelves</div>
                                    <div class="text-[10px] opacity-70">Handbag collection close-up</div>
                                </div>
                            </button>

                            <button
                                type="button"
                                @click="switchTour('{{ asset('videos/shop/boutique-lights-apparel.mp4') }}', 'Apparel Suite & Chandelier Ambience', 'Explore our clothing racks, silk evening gown displays, and boutique ceiling chandelier in full evening glory.')"
                                class="w-full p-3.5 rounded-2xl text-left border transition duration-150 flex items-center gap-3"
                                :class="activeTourVideo.includes('lights-apparel') ? 'bg-white text-black border-white shadow-lg' : 'bg-white/5 text-white/80 border-white/10 hover:bg-white/10'"
                            >
                                <span class="w-8 h-8 rounded-full bg-brand-pink text-white flex items-center justify-center font-bold text-xs shrink-0">3</span>
                                <div>
                                    <div class="text-xs font-bold">Apparel &amp; Chandelier Lounge</div>
                                    <div class="text-[10px] opacity-70">Silks, co-ords &amp; gowns</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
