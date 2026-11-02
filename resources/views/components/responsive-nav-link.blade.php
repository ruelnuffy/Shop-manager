@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand-pink text-start text-base font-medium text-brand-pink-800 bg-brand-pink-50 focus:outline-none focus:text-brand-pink-900 focus:bg-brand-pink-100 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-brand-charcoal/70 hover:text-brand-charcoal hover:bg-brand-pink-50/50 hover:border-brand-pink-200 focus:outline-none focus:text-brand-charcoal focus:bg-brand-pink-50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
