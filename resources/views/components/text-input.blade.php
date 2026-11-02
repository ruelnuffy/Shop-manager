@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full px-4 py-3 bg-white border border-black/15 focus:border-brand-pink focus:ring-4 focus:ring-brand-pink/15 rounded-xl shadow-xs text-brand-charcoal placeholder:text-brand-charcoal/30 text-sm transition duration-150 ease-in-out disabled:opacity-50 disabled:bg-gray-100']) }}>
