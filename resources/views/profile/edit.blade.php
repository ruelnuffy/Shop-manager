<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-charcoal/50 mb-1">
                    <a href="{{ route('home') }}" class="hover:text-brand-pink transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-brand-pink" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Home</span>
                    </a>
                    <span>/</span>
                    <a href="{{ route('dashboard') }}" class="hover:text-brand-pink transition">
                        <span>Dashboard</span>
                    </a>
                    <span>/</span>
                    <span class="text-brand-charcoal">Account Settings</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-brand-charcoal">
                    {{ __('Client Profile') }}
                </h1>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full border border-black/10 hover:border-black text-xs font-bold uppercase tracking-wider text-brand-charcoal hover:bg-black hover:text-white transition shadow-xs">
                    <svg class="w-3.5 h-3.5 text-brand-pink shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Storefront</span>
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-black text-white hover:bg-brand-pink text-xs font-bold uppercase tracking-wider transition shadow-xs">
                    <span>&larr; Dashboard</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
