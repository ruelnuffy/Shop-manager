<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-brand-charcoal hover:bg-black active:bg-brand-charcoal-900 text-white font-display font-bold text-xs uppercase tracking-widest rounded-xl shadow-lg shadow-black/10 hover:shadow-brand-pink/20 focus:outline-none focus:ring-2 focus:ring-brand-pink focus:ring-offset-2 transition-all duration-200 cursor-pointer disabled:opacity-50']) }}>
    {{ $slot }}
</button>
