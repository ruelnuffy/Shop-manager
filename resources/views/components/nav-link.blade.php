@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-brand-pink text-sm font-semibold leading-5 text-brand-pink-700 focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-brand-charcoal/70 hover:text-brand-pink hover:border-brand-pink-300 focus:outline-none focus:text-brand-pink focus:border-brand-pink transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
