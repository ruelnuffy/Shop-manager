<nav x-data="{ open: false }" class="bg-white/90 backdrop-blur-md border-b border-black/[0.06] sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Left: Logo & Direct Links -->
            <div class="flex items-center gap-6 sm:gap-8">
                <a href="{{ route('home') }}" class="group" title="Jessi's ClassiQ Store — Return to Homepage">
                    <x-application-logo />
                </a>

                <div class="hidden md:flex items-center space-x-6 text-xs font-semibold tracking-wider uppercase text-brand-charcoal/70">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 py-1 text-brand-charcoal hover:text-brand-pink transition duration-150 font-bold">
                        <svg class="w-3.5 h-3.5 text-brand-pink shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Home</span>
                    </a>

                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <a href="{{ route('customer.orders') }}" class="hover:text-brand-pink transition duration-150 py-1">
                            {{ __('My Orders') }}
                        </a>
                    @endauth

                    <a href="{{ route('products.index') }}" class="hover:text-brand-pink transition duration-150 py-1">
                        {{ __('Catalog') }}
                    </a>

                    <a href="{{ route('store.location') }}" class="hover:text-brand-pink transition duration-150 py-1">
                        {{ __('Flagship Store') }}
                    </a>
                </div>
            </div>

            <!-- Center: Sleek Minimal Search Bar -->
            <div class="hidden lg:flex flex-1 max-w-xs xl:max-w-sm mx-6">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-charcoal/40">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" placeholder="Search pieces, bags, footwear..." class="w-full pl-9 pr-4 py-1.5 bg-[#f4f4f4] border-transparent focus:border-brand-charcoal/20 focus:bg-white focus:ring-0 rounded-full text-xs font-medium placeholder-brand-charcoal/40 text-brand-charcoal transition duration-150">
                </div>
            </div>

            <!-- Right: Role Badges & Account Actions -->
            <div class="hidden sm:flex sm:items-center space-x-3">
                {{-- Storefront Home Button --}}
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-black/10 hover:border-black text-xs font-bold uppercase tracking-wider text-brand-charcoal transition duration-150"
                    title="Return to Storefront Homepage"
                >
                    <svg class="w-3.5 h-3.5 text-brand-pink shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Storefront</span>
                </a>

                @auth
                    <!-- User Role Badge -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wider uppercase
                        {{ Auth::user()->role === 'admin' ? 'bg-black text-white' :
                           (Auth::user()->role === 'staff' ? 'bg-brand-charcoal/10 text-brand-charcoal' : 'bg-brand-pink-100 text-brand-pink-800') }}">
                        {{ Auth::user()->role }}
                    </span>

                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3.5 py-1.5 border border-black/10 text-xs font-semibold rounded-full text-brand-charcoal bg-white hover:bg-[#f4f4f4] focus:outline-none transition ease-in-out duration-150 cursor-pointer">
                                <div>{{ Auth::user()->name }}</div>
                                <svg class="ms-1.5 h-3.5 w-3.5 text-brand-charcoal/50" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('home')">
                                <span class="flex items-center gap-2 font-bold text-brand-charcoal">
                                    <svg class="w-4 h-4 text-brand-pink shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    <span>Storefront Homepage</span>
                                </span>
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('dashboard')">
                                {{ __('Member Dashboard') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('customer.orders')">
                                {{ __('My Orders') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('My Profile') }}
                            </x-dropdown-link>

                            @if(Auth::user()->isStaff())
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    <span class="text-brand-pink font-bold">Atelier Management &rarr;</span>
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-black/[0.06]"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('cart.index') }}" class="p-2 text-brand-charcoal hover:text-brand-pink transition" title="Shopping Bag">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </a>
                    <a href="{{ route('login') }}" class="text-xs font-bold uppercase tracking-wider text-brand-charcoal hover:text-black px-2 py-1 transition">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-black text-white hover:bg-brand-pink text-xs font-bold uppercase tracking-wider transition">
                        Sign Up
                    </a>
                @endauth
            </div>

            <!-- Mobile Controls: Direct Home Shortcut + Hamburger -->
            <div class="-me-2 flex items-center gap-1 sm:hidden">
                <a
                    href="{{ route('home') }}"
                    class="p-2 rounded-lg text-brand-charcoal hover:bg-black/5 transition"
                    title="Return to Storefront Homepage"
                >
                    <svg class="h-5 w-5 text-brand-pink" stroke="currentColor" stroke-width="2" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-brand-charcoal hover:bg-black/5 focus:outline-none transition duration-150">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-b border-black/10">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                <span class="flex items-center gap-2 font-bold text-brand-charcoal">
                    <svg class="w-4 h-4 text-brand-pink shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Storefront Homepage</span>
                </span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Member Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('customer.orders')" :active="request()->routeIs('customer.orders')">
                {{ __('My Orders') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                {{ __('Catalog') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('store.location')" :active="request()->routeIs('store.location')">
                {{ __('Flagship Boutique (Jos)') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-black/10 px-4">
            @auth
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-bold text-sm text-brand-charcoal">{{ Auth::user()->name }}</div>
                        <div class="font-normal text-xs text-brand-charcoal/50">{{ Auth::user()->email }}</div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-brand-charcoal text-white">
                        {{ Auth::user()->role }}
                    </span>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('My Profile') }}
                    </x-responsive-nav-link>

                    @if(Auth::user()->isStaff())
                        <x-responsive-nav-link :href="route('admin.dashboard')">
                            <span class="text-brand-pink font-bold">Atelier Management &rarr;</span>
                        </x-responsive-nav-link>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="w-full text-center py-2 px-4 rounded-xl border border-black/15 text-xs font-bold uppercase tracking-wider">
                        Log In
                    </a>
                    <a href="{{ route('register') }}" class="w-full text-center py-2 px-4 rounded-xl bg-black text-white text-xs font-bold uppercase tracking-wider">
                        Sign Up
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>
