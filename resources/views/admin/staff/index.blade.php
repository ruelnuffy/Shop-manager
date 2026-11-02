<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Team &amp; Personnel
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium">{{ $activeCount }} Active Members</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-brand-charcoal text-slate-900 mt-1">
                    Staff &amp; Team Directory
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Manage team roles, store assignments, salary commitments, and Paystack recipient codes.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.payroll.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition inline-flex items-center gap-1.5 shadow-xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>View Payroll</span>
                </a>
                <a href="{{ route('admin.staff.create') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-indigo-600 text-xs font-semibold transition shadow-xs inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Onboard New Staff</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 block">Active Team Members</span>
                <div class="font-display font-extrabold text-2xl text-slate-900 mt-1">{{ $activeCount }}</div>
                <span class="text-[11px] text-slate-400 mt-1 block">Retail floor &amp; operations</span>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 block">Monthly Payroll Commitment</span>
                <div class="font-display font-extrabold text-2xl text-slate-900 mt-1 font-mono">₦{{ number_format($totalPayroll, 0) }}</div>
                <span class="text-[11px] text-slate-400 mt-1 block">Gross monthly salary budget</span>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 block">Payout Rail</span>
                <div class="font-display font-bold text-base text-slate-900 mt-1 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Paystack Transfers API</span>
                </div>
                <span class="text-[11px] text-slate-400 mt-1 block">Automated direct-to-bank NUBAN</span>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.staff.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-6 relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by staff name, email, role, bank..."
                           class="w-full pl-9.5 pr-4 py-2 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                </div>

                <div class="sm:col-span-3">
                    <select name="status" class="w-full px-3.5 py-2 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Staff</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Deactivated Staff</option>
                    </select>
                </div>

                <div class="sm:col-span-3 flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-semibold tracking-wide transition shadow-xs flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['q', 'status']))
                        <a href="{{ route('admin.staff.index') }}" title="Clear filters" class="p-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Staff Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 pl-6">Staff Member</th>
                            <th class="py-3.5 px-4">Position</th>
                            <th class="py-3.5 px-4">Bank Account</th>
                            <th class="py-3.5 px-4">Monthly Salary</th>
                            <th class="py-3.5 px-4">Paystack Recipient</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($staffMembers as $staff)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 pl-6">
                                    <div class="font-bold text-slate-900 text-sm">{{ $staff->user->name }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $staff->user->email }} &bull; {{ $staff->user->phone ?? 'No phone' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $staff->position }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800">{{ $staff->bank_name }}</div>
                                    <div class="font-mono text-[11px] text-slate-500">{{ $staff->bank_account_number }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-sm text-slate-900 font-mono">
                                    ₦{{ number_format($staff->monthly_salary, 0) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($staff->recipient_code)
                                        <code class="font-mono text-[11px] bg-slate-100 border border-slate-200/60 px-2 py-0.5 rounded text-slate-700">
                                            {{ $staff->recipient_code }}
                                        </code>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($staff->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 pr-6 text-right space-x-3">
                                    <a href="{{ route('admin.staff.edit', $staff->id) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                        Edit
                                    </a>
                                    @if ($staff->is_active)
                                        <form method="POST" action="{{ route('admin.staff.destroy', $staff->id) }}" class="inline-block" onsubmit="return confirm('Deactivate this staff member?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 cursor-pointer transition">
                                                Deactivate
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800">No staff members found</p>
                                    <p class="text-xs text-slate-400 mt-1">Try adjusting your search criteria or add new staff.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($staffMembers->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $staffMembers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
