<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @php
            $currentTenant = app(\App\Services\CurrentTenant::class)->get();
            $pageBrandTitle = $currentTenant ? $currentTenant->setting('branding.name', $currentTenant->name) : config('app.platform_name', 'ShopManager');
            $primaryColor = $currentTenant ? $currentTenant->setting('branding.palette.primary', '#4f46e5') : '#4f46e5';
            $secondaryColor = $currentTenant ? $currentTenant->setting('branding.palette.secondary', '#0f172a') : '#0f172a';
            $accentColor = $currentTenant ? $currentTenant->setting('branding.palette.accent', '#e0e7ff') : '#e0e7ff';
            $resolvedFavicon = $currentTenant && $currentTenant->setting('branding.logo_url')
                ? (str_starts_with($currentTenant->setting('branding.logo_url'), 'http') ? $currentTenant->setting('branding.logo_url') : asset($currentTenant->setting('branding.logo_url')))
                : asset('images/shopmanager-icon.png').'?v=2';
        @endphp

        <title>{{ $pageBrandTitle }}</title>

        <!-- Fonts: Plus Jakarta Sans & Poppins (Sans-serif) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ $resolvedFavicon }}">

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
            .bg-brand-pink, a:hover.hover\:bg-brand-pink, button:hover.hover\:bg-brand-pink, .hover\:bg-brand-pink:hover, .bg-brand-pink-600, .hover\:bg-brand-pink-600:hover {
                background-color: var(--brand-primary) !important;
            }
            .border-brand-pink, .hover\:border-brand-pink\/50:hover, .focus\:ring-brand-pink:focus {
                border-color: var(--brand-primary) !important;
            }
            .bg-brand-primary {
                background-color: var(--brand-primary) !important;
            }
            .text-brand-primary {
                color: var(--brand-primary) !important;
            }
            .bg-brand-secondary {
                background-color: var(--brand-secondary) !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#fafafa] text-brand-charcoal min-h-screen selection:bg-brand-pink-100 selection:text-brand-pink-900">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-black/[0.06]">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-grow">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
