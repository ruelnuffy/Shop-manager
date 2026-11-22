<x-super-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Site Visitors</h1>
                <p class="text-xs text-slate-500 mt-0.5">Live traffic across the ShopManager platform and merchant storefronts.</p>
            </div>
            <a
                href="{{ route('super-admin.visitors.index') }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-xs transition self-start sm:self-auto"
            >
                Refresh Traffic
            </a>
        </div>

        <!-- KPI Row -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 sm:p-5 rounded-xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Page Visits</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalVisits) }}</div>
            </div>
            <div class="p-4 sm:p-5 rounded-xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Unique Visitors</div>
                <div class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($uniqueVisitors) }}</div>
            </div>
            <div class="p-4 sm:p-5 rounded-xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Visits Today</div>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($todayVisits) }}</div>
            </div>
            <div class="p-4 sm:p-5 rounded-xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Platform vs Stores</div>
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
                class="flex-1 px-3.5 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500"
            />
            <select name="scope" class="px-3.5 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500">
                <option value="all" {{ $scope === 'all' ? 'selected' : '' }}>All Traffic</option>
                <option value="platform" {{ $scope === 'platform' ? 'selected' : '' }}>Platform Site Only</option>
                @foreach ($tenants as $tenant)
                    <option value="{{ $tenant->id }}" {{ (string) $scope === (string) $tenant->id ? 'selected' : '' }}>
                        {{ $tenant->name }}
                    </option>
                @endforeach
            </select>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition cursor-pointer">
                    Filter
                </button>
                @if ($search !== '' || $scope !== 'all')
                    <a href="{{ route('super-admin.visitors.index') }}" class="px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>

        <!-- Visitors List -->
        <div class="rounded-xl bg-white border border-slate-200 shadow-xs overflow-hidden">
            <!-- Mobile Card View (< 768px) -->
            <div class="md:hidden divide-y divide-slate-100">
                @forelse ($visits as $visit)
                    @php
                        $deviceInfo = $visit->deviceSummary();
                    @endphp
                    <div class="p-4 space-y-2 text-xs">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $visit->tenant ? 'bg-emerald-50 text-emerald-700' : 'bg-indigo-50 text-indigo-700' }}">
                                {{ $visit->tenant ? $visit->tenant->name : 'Platform' }}
                            </span>
                            <span class="text-[11px] text-slate-400">{{ $visit->created_at?->format('M d, g:i A') }}</span>
                        </div>
                        <div class="font-mono text-[11px] text-slate-800 break-all">{{ $visit->path }}</div>
                        <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold">
                                {{ $deviceInfo['device'] }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md bg-slate-50 text-slate-500">
                                {{ $deviceInfo['os'] }} &bull; {{ $deviceInfo['browser'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-2 text-[11px] text-slate-500">
                            <span>IP: {{ $visit->ip_address ?: 'Unknown' }}</span>
                            <span class="truncate max-w-[180px]">{{ $visit->referer ?: 'Direct' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-xs text-slate-400">
                        No visitor records found.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View (>= 768px) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Destination</th>
                            <th class="px-5 py-3">Page Path</th>
                            <th class="px-5 py-3">Device &amp; Browser</th>
                            <th class="px-5 py-3">IP Address</th>
                            <th class="px-5 py-3">Referrer</th>
                            <th class="px-5 py-3 text-right">Visited</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($visits as $visit)
                            @php
                                $deviceInfo = $visit->deviceSummary();
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $visit->tenant ? 'bg-emerald-50 text-emerald-700' : 'bg-indigo-50 text-indigo-700' }}">
                                        {{ $visit->tenant ? $visit->tenant->name : 'Platform' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-mono text-[11px] text-slate-700 max-w-[200px] truncate" title="{{ $visit->path }}">
                                    {{ $visit->path }}
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap" title="{{ $visit->user_agent }}">
                                    <div class="font-semibold text-slate-800">{{ $deviceInfo['device'] }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $deviceInfo['os'] }} &bull; {{ $deviceInfo['browser'] }}</div>
                                </td>
                                <td class="px-5 py-3 text-slate-500 whitespace-nowrap font-mono text-[11px]">{{ $visit->ip_address ?: 'Unknown' }}</td>
                                <td class="px-5 py-3 text-slate-400 max-w-[180px] truncate" title="{{ $visit->referer ?: 'Direct' }}">
                                    {{ $visit->referer ?: 'Direct' }}
                                </td>
                                <td class="px-5 py-3 text-right text-slate-400 whitespace-nowrap">
                                    {{ $visit->created_at?->format('M d, g:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-slate-400">
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
