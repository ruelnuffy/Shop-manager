<x-super-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Site Visitors</h1>
                <p class="text-xs text-slate-500 mt-0.5">Live traffic across the ShopManager platform and merchant storefronts.</p>
            </div>
        </div>

        <!-- KPI Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500">Total Page Visits</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalVisits) }}</div>
            </div>
            <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500">Unique Visitors</div>
                <div class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($uniqueVisitors) }}</div>
            </div>
            <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500">Visits Today</div>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($todayVisits) }}</div>
            </div>
            <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500">Platform vs Storefronts</div>
                <div class="text-sm font-bold text-slate-900 mt-2">
                    {{ number_format($platformVisits) }} Platform &bull; {{ number_format(max(0, $totalVisits - $platformVisits)) }} Stores
                </div>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('super-admin.visitors.index') }}" class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs flex flex-col sm:flex-row gap-3">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search by path, IP address, or referrer..."
                class="flex-1 rounded-lg border-slate-200 text-xs focus:border-indigo-500 focus:ring-indigo-500"
            />
            <select name="scope" class="rounded-lg border-slate-200 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                <option value="all" {{ $scope === 'all' ? 'selected' : '' }}>All Traffic</option>
                <option value="platform" {{ $scope === 'platform' ? 'selected' : '' }}>Platform Site Only</option>
                @foreach ($tenants as $tenant)
                    <option value="{{ $tenant->id }}" {{ (string) $scope === (string) $tenant->id ? 'selected' : '' }}>
                        {{ $tenant->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition">
                Filter
            </button>
        </form>

        <!-- Visitors Table -->
        <div class="rounded-xl bg-white border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Destination</th>
                            <th class="px-5 py-3">Page Path</th>
                            <th class="px-5 py-3">IP Address</th>
                            <th class="px-5 py-3">Referrer</th>
                            <th class="px-5 py-3 text-right">Visited</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($visits as $visit)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-3 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $visit->tenant ? $visit->tenant->name : 'Platform' }}
                                </td>
                                <td class="px-5 py-3 font-mono text-[11px] text-slate-700">{{ $visit->path }}</td>
                                <td class="px-5 py-3 text-slate-500 whitespace-nowrap">{{ $visit->ip_address ?: 'Unknown' }}</td>
                                <td class="px-5 py-3 text-slate-400 max-w-xs truncate">{{ $visit->referer ?: 'Direct' }}</td>
                                <td class="px-5 py-3 text-right text-slate-400 whitespace-nowrap">
                                    {{ $visit->created_at?->format('M d, g:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                                    No visitor records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($visits->hasPages())
                <div class="px-5 py-3 border-t border-slate-100">
                    {{ $visits->links() }}
                </div>
            @endif
        </div>
    </div>
</x-super-admin-layout>
