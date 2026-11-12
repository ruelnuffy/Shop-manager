<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @php
            $currentTenant = app(\App\Services\CurrentTenant::class)->get();
            $brandName = $currentTenant ? $currentTenant->setting('branding.name', $currentTenant->name) : "Jessi's ClassiQ Store";
            $brandTagline = $currentTenant ? $currentTenant->setting('branding.tagline', 'Curated Boutique & Ready-to-Wear') : 'Curated Boutique & Ready-to-Wear';
            $primaryColor = $currentTenant ? $currentTenant->setting('branding.palette.primary', '#c5a880') : '#c5a880';
            $secondaryColor = $currentTenant ? $currentTenant->setting('branding.palette.secondary', '#1a1a1a') : '#1a1a1a';
            $accentColor = $currentTenant ? $currentTenant->setting('branding.palette.accent', '#e8d8c8') : '#e8d8c8';
            $logoUrl = $currentTenant ? $currentTenant->setting('branding.logo_url') : null;
            $resolvedLogo = $logoUrl ? ((str_starts_with($logoUrl, 'http://') || str_starts_with($logoUrl, 'https://')) ? $logoUrl : asset($logoUrl)) : asset('images/logo.png');
        @endphp
        <title>{{ $brandName }} &mdash; {{ $brandTagline }}</title>

        <!-- Google Fonts: Plus Jakarta Sans & Poppins (Sans-serif) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ $resolvedLogo }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --brand-primary: {{ $primaryColor }};
                --brand-secondary: {{ $secondaryColor }};
                --brand-accent: {{ $accentColor }};
            }
            .text-brand-pink, a:hover.hover\:text-brand-pink, .hover\:text-brand-pink:hover {
                color: var(--brand-primary) !important;
            }
            .bg-brand-pink, a:hover.hover\:bg-brand-pink, button:hover.hover\:bg-brand-pink, .hover\:bg-brand-pink:hover {
                background-color: var(--brand-primary) !important;
            }
            .border-brand-pink, .hover\:border-brand-pink\/50:hover {
                border-color: var(--brand-primary) !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#fbfbfb] text-brand-charcoal selection:bg-brand-pink-100 selection:text-brand-pink-900 min-h-screen flex flex-col relative pb-20">

        <!-- Luxury Boutique Preloader with Typing Effect & Curtain Transition -->
        <x-preloader />

        <!-- Awwwards-Style Minimalist Top Header -->
        <header class="w-full bg-white/95 backdrop-blur-md border-b border-black/[0.06] sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 sm:h-20 gap-4">
                    <!-- Left: Logo & Micro Links -->
                    <div class="flex items-center gap-6 sm:gap-8">
                        <a href="{{ route('storefront') }}" class="group">
                            <x-application-logo />
                        </a>

                        <nav class="hidden md:flex items-center space-x-6 text-xs font-semibold tracking-wider uppercase text-brand-charcoal/70">
                            <a href="{{ route('products.index') }}" class="hover:text-black transition duration-150">
                                Catalog
                            </a>
                            <a href="#categories" class="hover:text-black transition duration-150 flex items-center gap-1">
                                Collections <span class="text-[9px]">▾</span>
                            </a>
                            <a href="{{ route('store.location') }}" class="hover:text-black transition duration-150">
                                Flagship Store
                            </a>
                            <a href="#values" class="hover:text-black transition duration-150">About</a>
                        </nav>
                    </div>

                    <!-- Center: Awwwards-Style Sleek Search Pill -->
                    <div class="hidden sm:flex flex-1 max-w-md mx-4">
                        <form action="{{ route('products.index') }}" method="GET" class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-charcoal/40">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" placeholder="Search silk gowns, leather bags, stilettos..." class="w-full pl-9 pr-4 py-2 bg-[#f0f0f0] hover:bg-[#eaeaea] focus:bg-white border-transparent focus:border-black/15 focus:ring-0 rounded-full text-xs font-medium placeholder-brand-charcoal/45 text-brand-charcoal transition duration-150">
                        </form>
                    </div>

                    <!-- Right: Auth & Cart Actions -->
                    <div class="flex items-center space-x-3">
                        <!-- Shopping Cart Button -->
                        @php
                            $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
                        @endphp
                        <a href="{{ route('cart.index') }}" class="relative p-2 rounded-full hover:bg-black/5 text-brand-charcoal transition duration-150" title="Shopping Bag">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            @if ($cartCount > 0)
                                <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-brand-pink text-white text-[10px] font-bold flex items-center justify-center">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>

                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-full bg-black text-white hover:bg-brand-pink hover:text-white text-xs font-bold tracking-wider uppercase transition duration-150 shadow-sm">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-xs font-bold tracking-wider uppercase text-brand-charcoal hover:text-black px-2 py-1 transition duration-150">
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="hidden sm:inline-block text-xs font-bold tracking-wider uppercase text-brand-charcoal/70 hover:text-black px-2 py-1 transition duration-150">
                                        Sign Up
                                    </a>
                                @endif
                                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 rounded-full bg-black text-white hover:bg-brand-pink hover:text-white text-xs font-bold tracking-wider uppercase transition duration-150 shadow-sm">
                                    Shop Now
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            <!-- HERO SECTION: Statement Typography (Inspired by "NOHO") -->
            <section class="pt-8 sm:pt-16 pb-12 sm:pb-24 border-b border-black/[0.06] bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <!-- Editorial Pill Badges -->
                    <div class="flex items-center justify-center flex-wrap gap-2 mb-4 sm:mb-6">
                        <span class="px-3 py-1 rounded-full text-[10px] sm:text-[11px] font-bold tracking-wider uppercase bg-brand-pink-50 text-brand-pink-800 border border-brand-pink-200">
                            SS / 2026 Collection
                        </span>
                        <span class="px-3 py-1 rounded-full text-[10px] sm:text-[11px] font-bold tracking-wider uppercase bg-[#f4f4f4] text-brand-charcoal/80 border border-black/5">
                            Flagship Atelier &bull; West of Mines, Jos
                        </span>
                    </div>

                    <!-- Statement Oversized Title -->
                    <h1 class="font-display font-extrabold text-4xl sm:text-6xl md:text-8xl lg:text-[104px] tracking-tight text-brand-charcoal leading-[1.05] sm:leading-[0.9] select-none hover:tracking-tighter transition-all duration-700">
                        <span class="block text-xs sm:text-xl md:text-2xl font-bold uppercase tracking-[0.25em] text-brand-charcoal/40 mb-1 sm:mb-3">{{ $brandTagline }}</span>
                        {{ $brandName }}
                    </h1>

                    <!-- Creator / Boutique Byline -->
                    <div class="flex items-center justify-center gap-3 sm:gap-4 mt-4 sm:mt-6 text-xs text-brand-charcoal/70">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-black text-white text-[10px] font-bold flex items-center justify-center">
                                J
                            </div>
                            <span class="font-semibold text-brand-charcoal">Jessi O.</span>
                            <span class="text-[9px] uppercase tracking-wider px-1.5 py-0.5 rounded bg-black/5 font-bold text-brand-charcoal/60">Lead</span>
                        </div>
                        <span class="text-black/20">&bull;</span>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="font-medium">Jos Atelier Open</span>
                        </div>
                    </div>

                    <!-- Hero Visual Showcase with Real Boutique Video Player -->
                    <div class="mt-8 sm:mt-14 max-w-5xl mx-auto" x-data="{
                        currentVideo: '{{ asset('videos/shop/boutique-shelves-showcase.mp4') }}',
                        videoTitle: 'Handcrafted Designer Bags & Atelier Showcase',
                        isPlaying: true,
                        isMuted: true,
                        togglePlay() {
                            const vid = this.$refs.heroVideo;
                            if (vid.paused) {
                                vid.play();
                                this.isPlaying = true;
                            } else {
                                vid.pause();
                                this.isPlaying = false;
                            }
                        },
                        toggleMute() {
                            const vid = this.$refs.heroVideo;
                            vid.muted = !vid.muted;
                            this.isMuted = vid.muted;
                        },
                        switchClip(url, title) {
                            this.currentVideo = url;
                            this.videoTitle = title;
                            this.$nextTick(() => {
                                const vid = this.$refs.heroVideo;
                                vid.load();
                                vid.play();
                                this.isPlaying = true;
                            });
                        }
                    }">
                        <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden bg-black border border-black/[0.08] shadow-2xl shadow-black/10 aspect-[16/10] sm:aspect-[21/9] group">
                            <!-- Background Video -->
                            <video
                                x-ref="heroVideo"
                                :src="currentVideo"
                                poster="{{ asset('images/shop/boutique-shelves-showcase.png') }}"
                                class="w-full h-full object-cover object-center"
                                autoplay
                                muted
                                loop
                                playsinline
                            ></video>

                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/15 to-transparent pointer-events-none"></div>

                            <!-- Top Controls & Status -->
                            <div class="absolute top-3 sm:top-4 left-3 sm:left-4 right-3 sm:right-4 flex items-center justify-between z-20">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full text-[9px] sm:text-[10px] font-bold uppercase tracking-wider bg-black/75 backdrop-blur-md text-white border border-white/15 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                        Real Boutique Footage
                                    </span>
                                </div>

                                <!-- Sound & Pause buttons -->
                                <div class="flex items-center gap-1.5 sm:gap-2">
                                    <button
                                        type="button"
                                        @click="togglePlay()"
                                        class="p-1.5 sm:p-2 rounded-full bg-black/70 hover:bg-black/90 text-white backdrop-blur-md border border-white/20 transition duration-150 text-xs flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8"
                                        :title="isPlaying ? 'Pause' : 'Play'"
                                    >
                                        <svg x-show="isPlaying" class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                                        </svg>
                                        <svg x-show="!isPlaying" class="w-3 h-3 sm:w-3.5 sm:h-3.5 translate-x-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        @click="toggleMute()"
                                        class="p-1.5 sm:p-2 rounded-full bg-black/70 hover:bg-black/90 text-white backdrop-blur-md border border-white/20 transition duration-150 text-xs flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8"
                                        :title="isMuted ? 'Unmute' : 'Mute'"
                                    >
                                        <svg x-show="isMuted" class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                                        </svg>
                                        <svg x-show="!isMuted" class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Bottom Title Overlay (Clean, Never Collides) -->
                            <div class="absolute bottom-3 sm:bottom-5 left-3 sm:left-6 right-3 sm:right-6 text-white text-left z-20 pointer-events-none">
                                <span class="text-[9px] sm:text-[10px] tracking-[0.2em] uppercase font-bold text-brand-pink-200 block mb-0.5 sm:mb-1">
                                    Physical Boutique Experience
                                </span>
                                <h3 class="font-display font-extrabold text-sm sm:text-xl uppercase tracking-tight text-white drop-shadow-md truncate" x-text="videoTitle">
                                    Handcrafted Designer Bags &amp; Atelier Showcase
                                </h3>
                            </div>
                        </div>

                        <!-- Clean Switcher Tabs Immediately Under Player -->
                        <div class="flex items-center justify-center gap-2 mt-3 sm:mt-4 flex-wrap">
                            <button
                                type="button"
                                @click="switchClip('{{ asset('videos/shop/boutique-shelves-showcase.mp4') }}', 'Handcrafted Designer Bags & Atelier Showcase')"
                                class="px-3.5 py-1.5 rounded-full text-[10px] sm:text-[11px] font-bold uppercase tracking-wider transition duration-150 border cursor-pointer"
                                :class="currentVideo.includes('boutique-shelves') ? 'bg-black text-white border-black shadow-sm' : 'bg-white text-brand-charcoal hover:bg-black/5 border-black/10'"
                            >
                                Boutique Shelves
                            </button>
                            <button
                                type="button"
                                @click="switchClip('{{ asset('videos/shop/boutique-interior-tour.mp4') }}', 'Flagship Studio & Display Salon')"
                                class="px-3.5 py-1.5 rounded-full text-[10px] sm:text-[11px] font-bold uppercase tracking-wider transition duration-150 border cursor-pointer"
                                :class="currentVideo.includes('interior-tour') ? 'bg-black text-white border-black shadow-sm' : 'bg-white text-brand-charcoal hover:bg-black/5 border-black/10'"
                            >
                                Studio Tour
                            </button>
                            <button
                                type="button"
                                @click="switchClip('{{ asset('videos/shop/boutique-lights-apparel.mp4') }}', 'Apparel Lounge & Evening Silks')"
                                class="px-3.5 py-1.5 rounded-full text-[10px] sm:text-[11px] font-bold uppercase tracking-wider transition duration-150 border cursor-pointer"
                                :class="currentVideo.includes('lights-apparel') ? 'bg-black text-white border-black shadow-sm' : 'bg-white text-brand-charcoal hover:bg-black/5 border-black/10'"
                            >
                                Apparel Lounge
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- BOUTIQUE VIDEO REELS (WATCH & SHOP) -->
            @php
                $videoProducts = \App\Models\Product::with(['category', 'variants'])
                    ->whereNotNull('video_url')
                    ->where('status', 'active')
                    ->take(8)
                    ->get();
            @endphp
            @if ($videoProducts->count() > 0)
                <section class="py-16 bg-[#111215] text-white border-b border-white/10 overflow-hidden">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-brand-pink animate-ping"></span>
                                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-brand-pink-400">ClassiQ In Motion</span>
                                </div>
                                <h2 class="font-display font-extrabold text-2xl sm:text-4xl lg:text-5xl uppercase tracking-tight">
                                    BOUTIQUE REELS &amp; REAL DROPS
                                </h2>
                            </div>
                            <p class="text-xs text-white/60 max-w-sm mt-2 sm:mt-0 font-medium">
                                Watch real video demonstrations of leather grain, stitching, padlock closures, and luxury gift suites filmed live in our Jos atelier.
                            </p>
                        </div>

                        <!-- Horizontal Reels Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                            @foreach ($videoProducts as $vp)
                                <div class="group relative rounded-2xl overflow-hidden bg-[#1d1f24] border border-white/10 hover:border-brand-pink/50 transition-all duration-300 flex flex-col justify-between aspect-[9/16]">
                                    <!-- Video Element with Hover Play -->
                                    <video
                                        src="{{ asset($vp->video_url) }}"
                                        poster="{{ asset($vp->featured_image) }}"
                                        class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700"
                                        autoplay
                                        muted
                                        loop
                                        playsinline
                                    ></video>

                                    <!-- Dark Gradient Overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/30 pointer-events-none"></div>

                                    <!-- Top Pill -->
                                    <div class="absolute top-3 left-3 right-3 flex items-center justify-between z-10">
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-black/60 backdrop-blur-md text-white border border-white/15">
                                            {{ $vp->category->name ?? 'Drop' }}
                                        </span>
                                        <span class="w-6 h-6 rounded-full bg-brand-pink/80 text-white flex items-center justify-center text-[10px] font-bold shadow-md">
                                            ▶
                                        </span>
                                    </div>

                                    <!-- Bottom Details & Quick Shop Button -->
                                    <div class="absolute bottom-0 inset-x-0 p-3 sm:p-4 z-10 flex flex-col justify-end space-y-2">
                                        <div>
                                            <h3 class="font-display font-bold text-xs sm:text-sm text-white line-clamp-2 leading-snug">
                                                {{ $vp->name }}
                                            </h3>
                                            <p class="text-xs font-extrabold text-brand-pink-400 mt-1">
                                                ₦{{ number_format($vp->base_price, 0) }}
                                            </p>
                                        </div>

                                        <a
                                            href="{{ route('products.show', $vp->slug) }}"
                                            class="w-full py-2 px-3 rounded-xl bg-white text-black hover:bg-brand-pink hover:text-white text-[11px] font-bold uppercase tracking-wider text-center transition duration-150 shadow-md block"
                                        >
                                            View Piece &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <!-- SECTION 2: "NOMINEES" STYLE ARRIVALS GRID -->
            <section id="arrivals" class="py-20 sm:py-28 bg-[#fafafa]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Editorial Header -->
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-brand-charcoal/50">Curated Drops</span>
                        <div class="flex items-center justify-center gap-3 mt-2">
                            <h2 class="font-display font-extrabold text-4xl sm:text-6xl md:text-7xl tracking-tightest uppercase text-brand-charcoal">
                                NEW DROPS
                            </h2>
                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-100/80 border border-amber-300 flex items-center justify-center text-amber-800 text-lg shadow-sm">
                                <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3l3.057 5.176a1 1 0 001.688.083L12 5.5l2.255 2.76a1 1 0 001.688-.084L19 3l1 15H4L5 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs sm:text-sm text-brand-charcoal/60 mt-3 font-medium">
                            Explore our latest boutique releases &bull; Every bag, suite, and gown filmed in-house at our Jos atelier
                        </p>
                    </div>

                    @php
                        $products = \App\Models\Product::with(['category', 'variants'])->where('status', 'active')->latest()->take(6)->get();
                    @endphp

                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 sm:gap-10">
                        @foreach ($products as $index => $prod)
                            <div class="group bg-white rounded-2xl overflow-hidden border border-black/[0.06] hover:border-black/20 hover:shadow-xl hover:shadow-black/5 transition-all duration-300 flex flex-col">
                                <!-- Image Frame with Video Indicator -->
                                <a href="{{ route('products.show', $prod->slug) }}" class="aspect-[4/5] relative overflow-hidden bg-[#f2f2f2] block">
                                    <img src="{{ asset($prod->featured_image) }}" alt="{{ $prod->name }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">

                                    <div class="absolute top-3 left-3 flex gap-2">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/95 text-brand-charcoal border border-black/5 shadow-sm">
                                            0{{ $index + 1 }} &bull; {{ $prod->category->name ?? 'ClassiQ' }}
                                        </span>
                                    </div>

                                    <div class="absolute top-3 right-3 flex items-center gap-1.5">
                                        @if ($prod->video_url)
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black/80 backdrop-blur-md text-brand-pink border border-white/20 shadow-sm flex items-center gap-1">
                                                <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                Video
                                            </span>
                                        @endif
                                        @if ($prod->inStock())
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black text-white shadow-sm">
                                                Stock: {{ $prod->totalStock() }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-brand-pink text-white shadow-sm">
                                                Sold Out
                                            </span>
                                        @endif
                                    </div>
                                </a>

                                <!-- Editorial Metadata Line (Awwwards Nominee style) -->
                                <div class="p-6 flex flex-col flex-grow">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <a href="{{ route('products.show', $prod->slug) }}">
                                            <h3 class="font-display font-bold text-lg text-brand-charcoal group-hover:underline underline-offset-4 transition duration-150 leading-snug">
                                                {{ $prod->name }}
                                            </h3>
                                        </a>
                                        <span class="font-bold text-base text-brand-charcoal shrink-0">
                                            ₦{{ number_format($prod->base_price, 0) }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-brand-charcoal/50 mb-4 flex items-center gap-1.5">
                                        <span>by</span>
                                        <span class="font-semibold text-brand-charcoal underline underline-offset-2">Jessi's ClassiQ Store</span>
                                        <span class="text-[9px] uppercase px-1 py-0.2 rounded bg-black/5 font-bold">Pro</span>
                                    </div>

                                    <p class="text-xs text-brand-charcoal/60 line-clamp-2 leading-relaxed mb-4 flex-grow">
                                        {{ $prod->description }}
                                    </p>

                                    <!-- Variant Tags -->
                                    <div class="flex flex-wrap gap-1 mb-5">
                                        @foreach ($prod->variants as $variant)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-[#f5f5f5] text-brand-charcoal/70 border border-black/[0.04]">
                                                {{ $variant->size }} &bull; {{ $variant->color }}
                                            </span>
                                        @endforeach
                                    </div>

                                    <div class="pt-4 border-t border-black/[0.06] flex items-center justify-between">
                                        <span class="text-[11px] font-semibold text-brand-charcoal/50 uppercase tracking-wider">
                                            Instant Paystack
                                        </span>
                                        <a href="{{ route('products.show', $prod->slug) }}" class="px-4 py-2 rounded-full bg-black hover:bg-brand-pink text-white text-xs font-bold uppercase tracking-wider transition duration-150">
                                            Select &amp; Order
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Call To Action Under Nominees -->
                    <div class="text-center mt-14">
                        <p class="text-xs font-medium text-brand-charcoal/60">
                            Check out all released creations &rarr; <a href="{{ route('products.index') }}" class="font-bold text-brand-charcoal underline underline-offset-4 hover:text-brand-pink transition duration-150">View Full Catalog ({{ \App\Models\Product::count() }} Pieces)</a>
                        </p>
                    </div>
                </div>
            </section>

            <!-- SECTION 3: CATEGORY EDITORIAL TILES -->
            <section id="categories" class="py-20 sm:py-28 bg-white border-t border-black/[0.06]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 border-b border-black/[0.08] pb-6">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-[0.2em] text-brand-charcoal/50">Departments</span>
                            <h2 class="font-display font-extrabold text-3xl sm:text-5xl uppercase tracking-tightest text-brand-charcoal mt-1">
                                THE COLLECTIONS
                            </h2>
                        </div>
                        <div class="mt-3 sm:mt-0 text-xs font-semibold text-brand-charcoal/60 uppercase tracking-wider">
                            {{ \App\Models\Category::count() }} Departments &bull; 100% Curated
                        </div>
                    </div>

                    @php
                        $categories = \App\Models\Category::withCount('products')->orderBy('sort_order')->get();
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        @foreach ($categories as $index => $cat)
                            <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="group relative rounded-2xl overflow-hidden bg-[#f4f4f4] border border-black/[0.06] p-5 hover:shadow-xl transition-all duration-300 flex flex-col justify-between min-h-[340px]">
                                <div class="flex items-center justify-between mb-3 z-10">
                                    <span class="font-display font-extrabold text-xl text-brand-charcoal/40 group-hover:text-black transition duration-200">
                                        0{{ $index + 1 }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/90 text-brand-charcoal border border-black/5 shadow-sm">
                                        {{ $cat->products_count }} {{ Str::plural('piece', $cat->products_count) }}
                                    </span>
                                </div>

                                <div class="my-auto overflow-hidden rounded-xl aspect-[4/3] bg-black/5 mb-3">
                                    <img src="{{ asset($cat->image) }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>

                                <div class="z-10">
                                    <h3 class="font-display font-extrabold text-lg text-brand-charcoal uppercase tracking-tight mb-1 group-hover:text-brand-pink transition duration-150">
                                        {{ $cat->name }}
                                    </h3>
                                    <p class="text-[11px] text-brand-charcoal/60 line-clamp-2 leading-relaxed">
                                        {{ $cat->description }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- SECTION 4: JOS BOUTIQUE PHYSICAL LOCATION & VIDEO WALKTHROUGH -->
            @php
                $location = \App\Models\ShopLocation::where('is_primary', true)->first();
            @endphp
            @if ($location)
                <section id="boutique" class="py-20 bg-[#fafafa] border-t border-black/[0.06]">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="bg-black text-white rounded-3xl p-6 sm:p-12 shadow-2xl relative overflow-hidden" x-data="{
                            walkthroughClip: '{{ asset('videos/shop/boutique-interior-tour.mp4') }}'
                        }">
                            <div class="absolute -right-20 -top-20 w-72 h-72 rounded-full bg-brand-pink/20 blur-3xl pointer-events-none"></div>

                            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                                <div class="lg:col-span-6 space-y-4">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-brand-pink-200 border border-white/15">
                                        <svg class="w-3 h-3 text-brand-pink" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Physical Flagship Boutique
                                    </div>
                                    <h2 class="font-display font-extrabold text-3xl sm:text-5xl uppercase tracking-tightest">
                                        {{ $location->name }}
                                    </h2>
                                    <p class="text-sm text-white/70 max-w-xl leading-relaxed">
                                        Step inside our flagship atelier across Chrisbest Plaza in West of Mines, Jos, Plateau State. Experience our illuminated display shelves, apparel styling suite, and exclusive handbag salon in person.
                                    </p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-white/80 pt-2">
                                        <div>
                                            <span class="text-white/40 uppercase tracking-wider block text-[10px]">Location</span>
                                            <span class="font-semibold">{{ $location->address }}, {{ $location->city }}</span>
                                        </div>
                                        <div>
                                            <span class="text-white/40 uppercase tracking-wider block text-[10px]">Hours</span>
                                            <span class="font-semibold">{{ $location->opening_hours }}</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-3 pt-4">
                                        <a href="{{ route('store.location') }}" class="px-6 py-3.5 rounded-full bg-brand-pink hover:bg-brand-pink-600 text-white font-bold text-xs uppercase tracking-wider transition duration-150 text-center shadow-lg shadow-brand-pink/30">
                                            Interactive GPS Map &amp; Tour &rarr;
                                        </a>
                                        <a href="tel:{{ $location->phone }}" class="px-6 py-3.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-xs uppercase tracking-wider border border-white/20 transition duration-150 text-center">
                                            Call Boutique
                                        </a>
                                    </div>
                                </div>

                                <!-- Video Walkthrough Showcase Embed -->
                                <div class="lg:col-span-6">
                                    <div class="rounded-2xl overflow-hidden border border-white/20 bg-neutral-900 shadow-2xl relative aspect-[16/10]">
                                        <video
                                            :src="walkthroughClip"
                                            class="w-full h-full object-cover"
                                            controls
                                            autoplay
                                            muted
                                            loop
                                            playsinline
                                        ></video>
                                    </div>
                                    <!-- Switcher tabs below video for zero overlay collision -->
                                    <div class="flex items-center gap-2 mt-3 flex-wrap">
                                        <button
                                            type="button"
                                            @click="walkthroughClip = '{{ asset('videos/shop/boutique-interior-tour.mp4') }}'"
                                            class="px-3 py-1.5 rounded-full text-[10px] sm:text-[11px] font-bold uppercase tracking-wider border transition cursor-pointer"
                                            :class="walkthroughClip.includes('interior-tour') ? 'bg-white text-black border-white shadow-sm' : 'bg-white/10 text-white/80 border-white/20 hover:bg-white/20'"
                                        >
                                            Walkthrough
                                        </button>
                                        <button
                                            type="button"
                                            @click="walkthroughClip = '{{ asset('videos/shop/boutique-lights-apparel.mp4') }}'"
                                            class="px-3 py-1.5 rounded-full text-[10px] sm:text-[11px] font-bold uppercase tracking-wider border transition cursor-pointer"
                                            :class="walkthroughClip.includes('lights-apparel') ? 'bg-white text-black border-white shadow-sm' : 'bg-white/10 text-white/80 border-white/20 hover:bg-white/20'"
                                        >
                                            Apparel Suite
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </main>

        <!-- LUXURY STORE MOBILE BOTTOM BAR (< 768px) -->
        <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-black/[0.08] z-40 px-3 py-2 flex items-center justify-around shadow-xl">
            <a href="/" class="flex flex-col items-center gap-0.5 text-brand-charcoal hover:text-brand-pink transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[9px] font-bold uppercase tracking-wider">Home</span>
            </a>
            <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-0.5 text-brand-charcoal/70 hover:text-brand-pink transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span class="text-[9px] font-bold uppercase tracking-wider">Catalog</span>
            </a>
            <a href="{{ route('store.location') }}" class="flex flex-col items-center gap-0.5 text-brand-charcoal/70 hover:text-brand-pink transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="text-[9px] font-bold uppercase tracking-wider">Atelier</span>
            </a>
            <a href="{{ route('cart.index') }}" class="flex flex-col items-center gap-0.5 text-brand-charcoal/70 hover:text-brand-pink transition relative">
                <div class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    @if ($cartCount > 0)
                        <span class="absolute -top-1 -right-2 px-1.5 py-0.2 rounded-full bg-brand-pink text-white text-[9px] font-bold leading-none">{{ $cartCount }}</span>
                    @endif
                </div>
                <span class="text-[9px] font-bold uppercase tracking-wider">Bag</span>
            </a>
            <a href="https://wa.me/2349069498376?text=Hello%20Jessi's%20ClassiQ,%20I'd%20like%20to%20inquire%20about%20your%20boutique%20collection" target="_blank" class="flex flex-col items-center gap-0.5 text-emerald-600 hover:text-emerald-700 transition">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.301-.15-1.78-.879-2.056-.98-.276-.1-.476-.15-.677.15-.2.301-.777.98-.953 1.18-.175.201-.351.226-.652.075-.301-.15-1.272-.469-2.423-1.496-.896-.799-1.501-1.786-1.677-2.087-.175-.301-.019-.464.132-.614.136-.135.301-.351.451-.527.15-.175.201-.301.301-.502.1-.2.05-.376-.025-.526-.075-.15-.677-1.631-.928-2.238-.244-.59-.492-.51-.677-.52l-.577-.01c-.2 0-.526.075-.802.376s-1.053 1.028-1.053 2.507c0 1.48 1.078 2.908 1.229 3.109.15.2 2.121 3.238 5.138 4.542.718.31 1.278.496 1.715.635.722.23 1.378.197 1.898.12.579-.087 1.78-.727 2.03-1.43.251-.703.251-1.304.176-1.43-.076-.126-.276-.201-.577-.351zM12.04 2C6.527 2 2.05 6.477 2.05 11.99c0 1.82.49 3.528 1.344 5.003L2 22l5.143-1.348A9.94 9.94 0 0012.04 22c5.513 0 9.99-4.477 9.99-9.99A9.99 9.99 0 0012.04 2z"/></svg>
                <span class="text-[9px] font-bold uppercase tracking-wider">WhatsApp</span>
            </a>
        </nav>

        <!-- DESKTOP CONCIERGE FLOATING BUTTONS (>= 768px) -->
        <aside class="hidden md:flex fixed bottom-8 right-8 z-40 items-center gap-3">
            <a href="https://wa.me/2349069498376?text=Hello%20Jessi's%20ClassiQ,%20I'd%20like%20to%20inquire%20about%20your%20boutique%20collection" target="_blank" class="px-4 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider flex items-center gap-2 shadow-2xl shadow-emerald-600/30 transition duration-150 group" title="Chat on WhatsApp (+234 906 949 8376)">
                <svg class="w-4 h-4 fill-current group-hover:scale-110 transition-transform" viewBox="0 0 24 24"><path d="M17.472 14.382c-.301-.15-1.78-.879-2.056-.98-.276-.1-.476-.15-.677.15-.2.301-.777.98-.953 1.18-.175.201-.351.226-.652.075-.301-.15-1.272-.469-2.423-1.496-.896-.799-1.501-1.786-1.677-2.087-.175-.301-.019-.464.132-.614.136-.135.301-.351.451-.527.15-.175.201-.301.301-.502.1-.2.05-.376-.025-.526-.075-.15-.677-1.631-.928-2.238-.244-.59-.492-.51-.677-.52l-.577-.01c-.2 0-.526.075-.802.376s-1.053 1.028-1.053 2.507c0 1.48 1.078 2.908 1.229 3.109.15.2 2.121 3.238 5.138 4.542.718.31 1.278.496 1.715.635.722.23 1.378.197 1.898.12.579-.087 1.78-.727 2.03-1.43.251-.703.251-1.304.176-1.43-.076-.126-.276-.201-.577-.351zM12.04 2C6.527 2 2.05 6.477 2.05 11.99c0 1.82.49 3.528 1.344 5.003L2 22l5.143-1.348A9.94 9.94 0 0012.04 22c5.513 0 9.99-4.477 9.99-9.99A9.99 9.99 0 0012.04 2z"/></svg>
                <span>Jos Concierge</span>
            </a>
            <a href="#" class="w-10 h-10 rounded-full bg-black/90 hover:bg-black text-white flex items-center justify-center transition duration-150 shadow-xl" title="Back to top">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
            </a>
        </aside>

        <!-- Minimalist Editorial Footer -->
        <footer class="bg-white border-t border-black/[0.08] text-brand-charcoal text-xs py-14">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-8 mb-12">
                    <div class="md:col-span-2 space-y-3">
                        <x-application-logo class="h-14 sm:h-16 md:h-18" />
                        <p class="text-xs text-brand-charcoal/60 max-w-sm leading-relaxed mt-3">
                            High-fashion atelier and boutique brand based across Chrisbest Plaza in West of Mines, Jos, Plateau State. Specializing in silk dresses, artisan leather handbags, and handcrafted footwear.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs uppercase tracking-widest text-brand-charcoal/50 mb-3">Explore</h4>
                        <ul class="space-y-2 font-medium">
                            <li><a href="#arrivals" class="hover:text-black transition">New Drops</a></li>
                            <li><a href="#categories" class="hover:text-black transition">Collections</a></li>
                            <li><a href="#boutique" class="hover:text-black transition">Flagship Store</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs uppercase tracking-widest text-brand-charcoal/50 mb-3">Account</h4>
                        <ul class="space-y-2 font-medium">
                            <li><a href="{{ route('login') }}" class="hover:text-black transition">Sign In</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-black transition">Create Account</a></li>
                            <li><a href="{{ url('/dashboard') }}" class="hover:text-black transition">My Orders</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs uppercase tracking-widest text-brand-charcoal/50 mb-3">Location & Care</h4>
                        <p class="text-brand-charcoal/60 leading-relaxed font-medium">
                            Across Chrisbest Plaza, West of Mines<br>
                            Jos, Plateau State, Nigeria<br>
                            Open Mon &ndash; Sat
                        </p>
                        <a
                            href="https://wa.me/2349069498376?text=Hello%20Jessi's%20ClassiQ,%20I'd%20like%20to%20inquire%20about%20your%20boutique%20collection"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 mt-2.5 text-emerald-700 hover:text-emerald-800 font-bold text-xs transition"
                        >
                            <svg class="w-3.5 h-3.5 fill-current shrink-0 text-emerald-600" viewBox="0 0 24 24"><path d="M17.472 14.382c-.301-.15-1.78-.879-2.056-.98-.276-.1-.476-.15-.677.15-.2.301-.777.98-.953 1.18-.175.201-.351.226-.652.075-.301-.15-1.272-.469-2.423-1.496-.896-.799-1.501-1.786-1.677-2.087-.175-.301-.019-.464.132-.614.136-.135.301-.351.451-.527.15-.175.201-.301.301-.502.1-.2.05-.376-.025-.526-.075-.15-.677-1.631-.928-2.238-.244-.59-.492-.51-.677-.52l-.577-.01c-.2 0-.526.075-.802.376s-1.053 1.028-1.053 2.507c0 1.48 1.078 2.908 1.229 3.109.15.2 2.121 3.238 5.138 4.542.718.31 1.278.496 1.715.635.722.23 1.378.197 1.898.12.579-.087 1.78-.727 2.03-1.43.251-.703.251-1.304.176-1.43-.076-.126-.276-.201-.577-.351zM12.04 2C6.527 2 2.05 6.477 2.05 11.99c0 1.82.49 3.528 1.344 5.003L2 22l5.143-1.348A9.94 9.94 0 0012.04 22c5.513 0 9.99-4.477 9.99-9.99A9.99 9.99 0 0012.04 2z"/></svg>
                            <span>WhatsApp: +234 906 949 8376</span>
                        </a>
                    </div>
                </div>

                <div class="pt-8 border-t border-black/[0.06] flex flex-col sm:flex-row items-center justify-between text-[11px] text-brand-charcoal/50">
                    <p>&copy; {{ date('Y') }} Jessi's ClassiQ Store. All rights reserved.</p>
                    <p class="mt-2 sm:mt-0 font-medium">Curated Boutique Platform &bull; Secured with Paystack</p>
                </div>
            </div>
        </footer>
    </body>
</html>
