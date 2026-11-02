@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'mt-1.5 text-xs text-rose-600 bg-rose-50/80 border border-rose-200/60 rounded-xl px-3 py-2 flex items-start gap-2 shadow-2xs']) }}>
        <svg class="w-4 h-4 shrink-0 text-rose-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <ul class="space-y-0.5">
            @foreach ((array) $messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
