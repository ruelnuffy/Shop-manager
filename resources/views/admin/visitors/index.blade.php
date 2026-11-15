<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-display font-extrabold text-2xl text-slate-900">Storefront Visitors</h1>
                <p class="text-xs text-slate-500 mt-1">Track customers and shoppers visiting your online storefront.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white shadow-xs text-xs font-bold text-slate-700 hover:bg-slate-50 transition self-start">
                &larr; Back to Dashboard
            </a>
        </div>

        <!-- KPI Cloud Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="rounded-[2rem] p-6 bg-gradient-to-br from-white via-cyan-50/40 to-cyan-100/50 border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)]">
                <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-800">Today</span>
                <div class="font-display font-extrabold text-3xl text-slate-900 mt-1">{{ number_format($visitsToday) }}</div>
                <div class="text-xs text-slate-500 mt-1">Storefront visits today</div>
            </div>
            <div class="rounded-[2rem] p-6 bg-gradient-to-br from-white via-indigo-50/40 to-indigo-100/50 border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)]">
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-800">Unique</span>
                <div class="font-display font-extrabold text-3xl text-slate-900 mt-1">{{ number_format($uniqueVisitors) }}</div>
                <div class="text-xs text-slate-500 mt-1">Unique visitor sessions</div>
            </div>
            <div class="rounded-[2rem] p-6 bg-gradient-to-br from-white via-emerald-50/40 to-emerald-100/50 border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)]">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">All Time</span>
                <div class="font-display font-extrabold text-3xl text-slate-900 mt-1">{{ number_format($totalVisits) }}</div>
                <div class="text-xs text-slate-500 mt-1">Total storefront views</div>
            </div>
        </div>

        <!-- Search -->
        <form method="GET" action="{{ route('admin.visitors.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search by page path, IP address, or referrer..."
                class="flex-1 rounded-full border-slate-200 bg-white px-4 py-2.5 text-xs focus:border-cyan-500 focus:ring-cyan-500 shadow-xs"
            />
            <button type="submit" class="px-5 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                Search
            </button>
        </form>

        <!-- Visitors Log -->
        <div class="rounded-[2rem] bg-white border border-white/90 shadow-[0_14px_35px_-10px_rgba(15,23,42,0.08)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">Page Path</th>
                            <th class="px-6 py-3.5">IP Address</th>
                            <th class="px-6 py-3.5">Referrer</th>
                            <th class="px-6 py-3.5 text-right">Visited</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($visits as $visit)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-6 py-3.5 font-mono text-[11px] font-semibold text-slate-800">{{ $visit->path }}</td>
                                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">{{ $visit->ip_address ?: 'Unknown' }}</td>
                                <td class="px-6 py-3.5 text-slate-400 max-w-xs truncate">{{ $visit->referer ?: 'Direct' }}</td>
                                <td class="px-6 py-3.5 text-right text-slate-400 whitespace-nowrap">
                                    {{ $visit->created_at?->format('M d, g:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-400">
                                    No storefront visitors recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($visits->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $visits->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
