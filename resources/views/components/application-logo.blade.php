@props(['class' => 'h-11 sm:h-12 md:h-13', 'showText' => true])

@php
    $tenant = app(\App\Services\CurrentTenant::class)->get();
    $platformName = config('app.platform_name', 'ShopManager');
    $logoUrl = $tenant ? $tenant->setting('branding.logo_url') : null;
    $brandName = $tenant ? $tenant->setting('branding.name', $tenant->name) : $platformName;
    $primaryColor = $tenant ? $tenant->setting('branding.palette.primary', '#1a1a1a') : '#1a1a1a';
    $resolvedLogo = null;
    if ($logoUrl) {
        $resolvedLogo = (str_starts_with($logoUrl, 'http://') || str_starts_with($logoUrl, 'https://')) ? $logoUrl : asset($logoUrl);
    }
@endphp

@if ($tenant && $resolvedLogo)
    <div class="inline-flex items-center select-none">
        <img
            src="{{ $resolvedLogo }}"
            alt="{{ $brandName }}"
            {{ $attributes->merge(['class' => "$class w-auto object-contain transition duration-200 group-hover:opacity-95 drop-shadow-sm"]) }}
        />
    </div>
@elseif ($tenant)
    <div class="inline-flex items-center gap-2.5 select-none">
        <div class="w-8 h-8 rounded-lg text-white font-bold flex items-center justify-center text-xs shadow-sm" style="background-color: {{ $primaryColor }}">
            {{ strtoupper(substr($brandName, 0, 2)) }}
        </div>
        @if ($showText)
            <span class="font-display font-bold text-lg text-neutral-900 tracking-tight">{{ $brandName }}</span>
        @endif
    </div>
@else
    <div class="inline-flex items-center select-none">
        <img
            src="{{ asset('images/shopmanager-logo.png') }}"
            alt="{{ $platformName }}"
            {{ $attributes->merge(['class' => "$class w-auto object-contain transition duration-200 group-hover:opacity-95"]) }}
        />
    </div>
@endif
