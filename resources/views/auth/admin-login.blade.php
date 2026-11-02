@php
    $currentTenant = app(\App\Services\CurrentTenant::class)->get();
    $platformName = config('app.platform_name', 'ShopManager');
    $brandName = $currentTenant ? $currentTenant->setting('branding.name', $currentTenant->name) : $platformName;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Admin Portal &bull; {{ $brandName }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen bg-brand-charcoal text-white selection:bg-brand-pink/30">

        {{-- Dark full-bleed admin portal layout --}}
        <div class="min-h-screen flex flex-col lg:flex-row">

            {{-- Left: Dark Atelier Identity Panel --}}
            <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-between p-12 xl:p-16 overflow-hidden bg-[#0d0b0c]">
                {{-- Background --}}
                <div class="absolute inset-0">
                    <img src="{{ asset('images/shop/boutique-shelves-showcase.png') }}" alt="" class="w-full h-full object-cover object-center opacity-20" style="filter: saturate(0.3);" />
                    <div class="absolute inset-0 bg-gradient-to-tr from-[#0d0b0c] via-[#0d0b0c]/90 to-[#1a0a10]/80"></div>
                </div>
                <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-brand-pink/10 rounded-full blur-3xl pointer-events-none"></div>

                {{-- Top: Logo + Label --}}
                <div class="relative z-10 flex items-center gap-3">
                    <x-application-logo :showText="false" class="h-8" />
                    <div class="h-5 w-px bg-white/15"></div>
                    <span class="text-[10px] uppercase font-bold tracking-[0.4em] text-white/40">Admin Portal</span>
                </div>

                {{-- Center --}}
                <div class="relative z-10 my-auto">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-pink/15 border border-brand-pink/25 text-brand-pink-200 text-[10px] font-bold uppercase tracking-widest mb-5">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-pink animate-pulse"></span>
                        {{ $currentTenant ? 'Atelier Command Centre' : 'Platform Control' }}
                    </div>
                    <h1 class="font-display font-black text-4xl xl:text-5xl uppercase tracking-tight text-white leading-[1.05] mb-4">
                        Manage {{ $currentTenant ? 'The Boutique.' : $platformName . '.' }}
                    </h1>
                    <p class="text-sm text-white/50 font-light leading-relaxed max-w-xs">
                        Full access to inventory, staff management, payroll disbursement, sales reporting, and OCR paper ledger imports.
                    </p>

                    {{-- Access pills --}}
                    <div class="mt-8 flex flex-wrap gap-2">
                        @foreach(['Inventory Control', 'Staff & Payroll', 'Sales Reports', 'OCR Imports', 'Order Management'] as $item)
                            <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-semibold text-white/50 tracking-wide">{{ $item }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- Bottom --}}
                <div class="relative z-10 text-[10px] font-mono uppercase tracking-widest text-white/20">
                    <span>{{ $brandName }} &bull; {{ $currentTenant ? 'Store Administration' : 'Platform Operations' }}</span>
                </div>
            </div>

            {{-- Right: Login Form --}}
            <div class="w-full lg:w-1/2 min-h-screen flex flex-col justify-between p-6 sm:p-10 md:p-12 xl:p-16 bg-[#111015]">

                {{-- Mobile header --}}
                <div class="flex items-center justify-between pb-6 border-b border-white/5 lg:border-none">
                    <a href="{{ route('home') }}" class="group inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-white/30 hover:text-white/60 transition-colors">
                        <span class="w-7 h-7 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-brand-pink transition duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </span>
                        <span>Storefront</span>
                    </a>
                    <a href="{{ route('login') }}" class="text-[10px] font-bold uppercase tracking-wider text-white/30 hover:text-white/60 transition-colors">
                        Customer Login &rarr;
                    </a>
                </div>

                {{-- Form --}}
                <div class="w-full max-w-md mx-auto my-auto py-8">
                    <div class="mb-8">
                        <div class="mb-6">
                            <x-application-logo class="h-12" />
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-brand-pink/15 text-brand-pink border border-brand-pink/20 mb-3">
                            Admin Access
                        </div>
                        <h2 class="font-display font-extrabold text-2xl sm:text-3xl uppercase tracking-tight text-white">
                            Admin Sign In
                        </h2>
                        <p class="text-xs text-white/40 mt-1">
                            Restricted to authorised administrators only.
                        </p>
                    </div>

                    <div class="bg-white/[0.04] rounded-3xl p-7 sm:p-9 border border-white/[0.07] backdrop-blur-sm">
                        @if ($errors->any())
                            <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs flex items-center gap-2.5">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                {{ $errors->first() }}
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.login.store') }}" x-data="{ showPassword: false }" class="space-y-4">
                            @csrf

                            {{-- Email --}}
                            <div>
                                <label class="block font-semibold text-xs uppercase tracking-wider text-white/50 mb-1.5" for="admin_email">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/20">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                        </svg>
                                    </div>
                                    <input
                                        id="admin_email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="admin@jessiclassiq.com"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        class="w-full pl-10 py-3 pr-4 bg-white/5 border border-white/10 focus:border-brand-pink focus:ring-4 focus:ring-brand-pink/15 rounded-xl text-white placeholder:text-white/20 text-sm transition duration-150 outline-none"
                                    />
                                </div>
                            </div>

                            {{-- Password --}}
                            <div>
                                <label class="block font-semibold text-xs uppercase tracking-wider text-white/50 mb-1.5" for="admin_password">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/20">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <input
                                        id="admin_password"
                                        x-bind:type="showPassword ? 'text' : 'password'"
                                        name="password"
                                        required
                                        autocomplete="current-password"
                                        placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                                        class="w-full pl-10 pr-10 py-3 bg-white/5 border border-white/10 focus:border-brand-pink focus:ring-4 focus:ring-brand-pink/15 rounded-xl text-white placeholder:text-white/20 text-sm transition duration-150 outline-none"
                                    />
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-white/20 hover:text-white/50 transition-colors cursor-pointer" tabindex="-1">
                                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="pt-2">
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-brand-pink hover:bg-brand-pink-600 text-white font-display font-bold text-xs uppercase tracking-widest rounded-xl shadow-lg shadow-brand-pink/20 hover:shadow-brand-pink/30 focus:outline-none focus:ring-2 focus:ring-brand-pink focus:ring-offset-2 focus:ring-offset-[#111015] transition-all duration-200 cursor-pointer group">
                                    <span>Access Admin Portal</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Staff portal link --}}
                            <div class="pt-4 text-center border-t border-white/5">
                                <a href="{{ route('staff.login') }}" class="text-xs text-white/30 hover:text-white/60 transition-colors">
                                    Staff portal &rarr;
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="pt-6 border-t border-white/5 text-center text-xs text-white/20">
                    &copy; {{ date('Y') }} Jessi's ClassiQ Store &bull; Admin Access Only
                </div>
            </div>
        </div>
    </body>
</html>
