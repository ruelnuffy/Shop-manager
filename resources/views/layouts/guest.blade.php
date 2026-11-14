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
            <!-- Left Visual Showcase (Desktop) — Clean & Simple with 3D Mascot -->
            <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-between p-12 xl:p-16 text-white overflow-hidden bg-gradient-to-br from-[#1d4ed8] via-[#2563eb] to-[#1e40af]">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-400/20 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Top Back Link -->
                <div class="relative z-10 flex items-center justify-between">
                    <a href="{{ route('home') }}" class="group inline-flex items-center gap-2.5 text-xs font-bold uppercase tracking-wider text-white/85 hover:text-white transition">
                        <span class="w-9 h-9 rounded-full bg-white/15 border border-white/25 flex items-center justify-center group-hover:bg-white group-hover:text-blue-700 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </span>
                        <span>{{ $currentTenant ? 'Back to Store' : 'Back to Home' }}</span>
                    </a>
                </div>

                <!-- Center 3D Mascot Hero -->
                <div class="relative z-10 max-w-md mx-auto my-auto text-center space-y-6">
                    <div class="rounded-[2rem] overflow-hidden shadow-2xl shadow-blue-950/40 border border-white/20 bg-blue-800/30">
                        <img
                            src="{{ asset('images/hero-3d-mascot.jpg') }}"
                            alt="{{ $brandName }} Mascot"
                            class="w-full h-auto object-cover"
                        />
                    </div>
                    <h1 class="font-display font-extrabold text-3xl xl:text-4xl tracking-tight text-white leading-tight">
                        {{ $currentTenant ? $brandName : 'Sell anywhere. Keep it simple.' }}
                    </h1>
                </div>

                <!-- Bottom -->
                <div class="relative z-10 text-xs text-white/70 font-medium text-center">
                    {{ $brandName }}
                </div>
            </div>

            <!-- Right Column: Authentication Card -->
            <div class="w-full lg:w-1/2 min-h-screen flex flex-col justify-between p-6 sm:p-10 md:p-12 xl:p-16 bg-[#faf8f5] relative overflow-y-auto">
                <!-- Top Navigation Header -->
                <div class="flex items-center justify-between pb-4 lg:pb-0">
                    <a href="{{ route('home') }}" class="group inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-charcoal/70 hover:text-blue-600 transition-colors">
                        <span class="w-8 h-8 rounded-full bg-black/5 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center transition duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </span>
                        <span>{{ $currentTenant ? 'Storefront' : 'Home' }}</span>
                    </a>
                </div>

                <!-- Form Center Container -->
                <div class="w-full max-w-md mx-auto my-auto py-6">
                    <!-- Brand Logo Showcase -->
                    <div class="mb-6 text-center">
                        <a href="{{ route('home') }}" class="inline-block group transition transform hover:scale-[1.02] duration-200">
                            <x-application-logo class="h-10 sm:h-12" />
                        </a>
                    </div>

                    <!-- Inner Card -->
                    <div class="bg-white rounded-3xl p-7 sm:p-9 shadow-xl shadow-brand-charcoal/5 border border-black/[0.06]">
                        {{ $slot }}
                    </div>
                </div>

                <!-- Footer Copyright -->
                <div class="pt-4 text-center text-xs text-brand-charcoal/45">
                    <span>&copy; {{ date('Y') }} {{ $brandName }}</span>
                </div>
            </div>
        </div>
    </body>
</html>
