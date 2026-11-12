<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $currentTenant = app(\App\Services\CurrentTenant::class)->get();
            $platformName = config('app.platform_name', 'ShopManager');
            $brandName = $currentTenant ? $currentTenant->setting('branding.name', $currentTenant->name) : $platformName;
            $primaryLocation = $currentTenant ? $currentTenant->shopLocations()->where('is_primary', true)->first() : null;
        @endphp

        <title>{{ $brandName }} &bull; {{ $currentTenant ? ($currentTenant->setting('branding.tagline') ?: 'Store Portal') : 'Retail & Inventory Operating System' }}</title>

        <!-- Fonts: Plus Jakarta Sans & Poppins (Sans-serif) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-brand-charcoal antialiased bg-[#faf8f5] min-h-screen selection:bg-brand-pink-100 selection:text-brand-pink-900">
        <div class="min-h-screen flex flex-col lg:flex-row">
            <!-- Left Visual Showcase (Desktop) -->
            <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-between p-12 xl:p-16 text-white overflow-hidden {{ $currentTenant ? 'bg-brand-charcoal-900' : 'bg-slate-900' }}">
                @if ($currentTenant)
                    <!-- Tenant Background Image & Atmospheric Luxury Overlays -->
                    <div class="absolute inset-0 z-0">
                        <img
                            src="{{ asset('images/shop/boutique-interior-tour.png') }}"
                            alt="{{ $brandName }}"
                            class="w-full h-full object-cover object-center filter brightness-75 contrast-110 scale-105 transition-transform duration-1000 ease-out"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-charcoal-900 via-brand-charcoal-900/75 to-brand-charcoal-900/50 mix-blend-multiply"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-brand-charcoal-900/80 via-transparent to-brand-charcoal-900/60"></div>
                        <div class="absolute -top-24 -left-24 w-96 h-96 bg-brand-pink/25 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-brand-pink/20 rounded-full blur-3xl pointer-events-none"></div>
                    </div>

                    <!-- Left Top Badge -->
                    <div class="relative z-10 flex items-center justify-between">
                        <a href="{{ route('home') }}" class="group inline-flex items-center gap-3">
                            <span class="w-9 h-9 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-brand-pink-200 group-hover:bg-brand-pink group-hover:text-white transition duration-200">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold uppercase tracking-widest text-white/80 group-hover:text-white transition duration-200">
                                Return to Storefront
                            </span>
                        </a>

                        @if ($primaryLocation)
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[10px] font-bold uppercase tracking-wider text-brand-pink-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-pink animate-pulse"></span>
                                {{ $primaryLocation->city }} Boutique
                            </div>
                        @endif
                    </div>

                    <!-- Left Center Content -->
                    <div class="relative z-10 max-w-lg my-auto py-12">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-pink/20 border border-brand-pink/40 text-brand-pink-200 text-xs font-semibold mb-6 backdrop-blur-md">
                            <svg class="w-3.5 h-3.5 text-brand-pink" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span>{{ $brandName }} &bull; Boutique Collection</span>
                        </div>

                        <h1 class="font-display font-extrabold text-4xl xl:text-5xl uppercase tracking-tight text-white leading-[1.05] mb-4">
                            {{ $currentTenant->setting('branding.tagline', 'Classy Looks For Every You.') }}
                        </h1>

                        <p class="font-sans font-medium text-lg text-brand-pink-100/90 mb-6">
                            "Elegance is not about being noticed, it's about being remembered."
                        </p>

                        <div class="grid grid-cols-3 gap-3 pt-6 border-t border-white/10">
                            <div class="p-3 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-pink-300 block mb-1">01 / Craft</span>
                                <span class="text-xs font-semibold text-white block">Handcrafted Couture</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-pink-300 block mb-1">02 / Quality</span>
                                <span class="text-xs font-semibold text-white block">Curated Luxury Silhouettes</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-pink-300 block mb-1">03 / Speed</span>
                                <span class="text-xs font-semibold text-white block">Express Delivery</span>
                            </div>
                        </div>
                    </div>

                    <!-- Left Footer Location Snapshot -->
                    @if ($primaryLocation)
                        <div class="relative z-10 flex items-center justify-between p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-brand-pink/20 border border-brand-pink/30 flex items-center justify-center text-brand-pink-200 shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-white block">{{ $primaryLocation->address }}</span>
                                    <span class="text-[11px] text-white/60">{{ $primaryLocation->city }}, {{ $primaryLocation->state }} &bull; {{ $primaryLocation->opening_hours }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Neutral ShopManager Platform Identity Visuals -->
                    <div class="absolute inset-0 z-0">
                        <div class="absolute inset-0 bg-gradient-to-tr from-slate-950 via-slate-900 to-indigo-950"></div>
                        <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-purple-500/15 rounded-full blur-3xl pointer-events-none"></div>
                    </div>

                    <!-- Platform Top Link -->
                    <div class="relative z-10 flex items-center justify-between">
                        <a href="{{ route('home') }}" class="group inline-flex items-center gap-3">
                            <span class="w-9 h-9 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-indigo-300 group-hover:bg-indigo-600 group-hover:text-white transition duration-200">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold uppercase tracking-widest text-white/80 group-hover:text-white transition duration-200">
                                Platform Overview
                            </span>
                        </a>

                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[10px] font-bold uppercase tracking-wider text-indigo-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                            Multi-Tenant SaaS
                        </div>
                    </div>

                    <!-- Platform Center Content -->
                    <div class="relative z-10 max-w-lg my-auto py-12">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/20 border border-indigo-500/40 text-indigo-300 text-xs font-semibold mb-6 backdrop-blur-md">
                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11H9v-2h2v2zm0-4H9V5h2v4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $platformName }} &bull; Retail Infrastructure</span>
                        </div>

                        <h1 class="font-display font-extrabold text-4xl xl:text-5xl uppercase tracking-tight text-white leading-[1.05] mb-4">
                            Unified Retail, Inventory &amp; Store Operations.
                        </h1>

                        <p class="text-sm text-slate-300 font-light leading-relaxed mb-8">
                            Power independent merchant storefronts, manage multi-location physical inventory, disburse automated staff payroll, and synchronize physical ledger books into verified financial intelligence.
                        </p>

                        <!-- Platform Pillars Grid -->
                        <div class="grid grid-cols-3 gap-3 pt-6 border-t border-white/10">
                            <div class="p-3 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400 block mb-1">01 / Tenancy</span>
                                <span class="text-xs font-semibold text-white block">Isolated Data Stores</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block mb-1">02 / Payroll</span>
                                <span class="text-xs font-semibold text-white block">Automated Transfers</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 block mb-1">03 / Ledgers</span>
                                <span class="text-xs font-semibold text-white block">OCR Digitization</span>
                            </div>
                        </div>
                    </div>

                    <!-- Platform Left Footer Snapshot -->
                    <div class="relative z-10 flex items-center justify-between p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-300 shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-white block">{{ $platformName }} Cloud</span>
                                <span class="text-[11px] text-white/60">Independent Retail Operating System</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Authentication Card -->
            <div class="w-full lg:w-1/2 min-h-screen flex flex-col justify-between p-6 sm:p-10 md:p-12 xl:p-16 bg-[#faf8f5] relative overflow-y-auto">
                <!-- Mobile Top Navigation Header -->
                <div class="flex items-center justify-between pb-6 border-b border-black/5 lg:border-none">
                    <a href="{{ route('home') }}" class="group inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 hover:text-indigo-600 transition-colors">
                        <span class="w-7 h-7 rounded-full bg-black/5 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center transition duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </span>
                        <span>{{ $currentTenant ? 'Storefront' : 'Home' }}</span>
                    </a>

                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white border border-black/5 text-brand-charcoal/70 shadow-xs">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>256-Bit SSL Encrypted</span>
                    </div>
                </div>

                <!-- Form Center Container -->
                <div class="w-full max-w-md mx-auto my-auto py-8">
                    <!-- Brand Logo Showcase -->
                    <div class="mb-8 text-center sm:text-left">
                        <a href="{{ route('home') }}" class="inline-block group transition transform hover:scale-[1.02] duration-200">
                            <x-application-logo class="h-12 sm:h-14" />
                        </a>
                    </div>

                    <!-- Inner Card -->
                    <div class="bg-white rounded-3xl p-7 sm:p-9 shadow-xl shadow-brand-charcoal/5 border border-black/[0.06] backdrop-blur-sm">
                        {{ $slot }}
                    </div>
                </div>

                <!-- Footer Copyright & Info -->
                <div class="pt-6 border-t border-black/5 text-center text-xs text-brand-charcoal/50 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <span>&copy; {{ date('Y') }} {{ $brandName }}. All rights reserved.</span>
                    <span class="font-medium text-brand-charcoal/40">{{ $currentTenant ? 'Independent Boutique Merchant' : 'Multi-Store Retail Infrastructure' }}</span>
                </div>
            </div>
        </div>
    </body>
</html>
