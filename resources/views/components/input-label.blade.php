@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-xs uppercase tracking-wider text-brand-charcoal/80 mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
