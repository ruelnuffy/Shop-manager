<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-display font-bold text-xs uppercase tracking-widest rounded-xl shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-all duration-200 cursor-pointer disabled:opacity-50']) }}>
    {{ $slot }}
</button>
