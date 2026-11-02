@props(['class' => 'h-11 sm:h-12 md:h-13', 'showText' => true])

@php
    $tenant = app(\App\Services\CurrentTenant::class)->get();
    $platformName = config('app.platform_name', 'ShopManager');
    $logoUrl = $tenant ? $tenant->setting('branding.logo_url') : null;
    $brandName = $tenant ? $tenant->setting('branding.name', $tenant->name) : $platformName;
@endphp

@if ($tenant && $logoUrl)
    <div class="inline-flex items-center select-none">
        <img
            src="{{ asset($logoUrl) }}"
            alt="{{ $brandName }}"
            {{ $attributes->merge(['class' => "$class w-auto object-contain transition duration-200 group-hover:opacity-95 drop-shadow-sm"]) }}
        />
    </div>
@elseif ($tenant)
    <div class="inline-flex items-center gap-2.5 select-none">
        <div class="w-8 h-8 rounded-lg bg-neutral-900 text-white font-bold flex items-center justify-center text-xs shadow-sm">
            {{ strtoupper(substr($brandName, 0, 2)) }}
        </div>
        @if ($showText)
            <span class="font-display font-bold text-lg text-neutral-900 tracking-tight">{{ $brandName }}</span>
        @endif
    </div>
@else
    <div class="inline-flex items-center gap-2.5 select-none">
        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
        </div>
        @if ($showText)
            <span class="font-display font-extrabold text-lg text-neutral-900 tracking-tight">{{ $platformName }}</span>
        @endif
    </div>
@endif
