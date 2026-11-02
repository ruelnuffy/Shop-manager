<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Automated Disbursals
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium">Period: {{ $period }}</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-brand-charcoal text-slate-900 mt-1">
                    Paystack Direct Salary Transfers
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Automated bank-direct payroll disbursements to active team members via NUBAN rails.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.staff.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition inline-flex items-center gap-1.5 shadow-xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Staff Directory</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Period Selection & Batch Action Header -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.payroll.index') }}" class="flex items-center gap-3 w-full md:w-auto">
                <label class="text-xs font-semibold text-slate-600 shrink-0 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Payroll Period:
                </label>
                <input type="month" name="period" value="{{ $period }}" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-50/70 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
            </form>

            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                @if ($pendingStaffCount > 0)
                    <form method="POST" action="{{ route('admin.payroll.disburse') }}" onsubmit="return confirm('Disburse monthly salaries to all {{ $pendingStaffCount }} pending active staff members for period {{ $period }} via Paystack?');">
                        @csrf
                        <input type="hidden" name="period" value="{{ $period }}">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs flex items-center gap-2 transition shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Disburse Batch Payroll ({{ $pendingStaffCount }} Pending)</span>
                        </button>
                    </form>
                @else
                    <div class="px-4 py-2 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>All Active Staff Disbursed for {{ $period }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- KPI Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 block">Monthly Commitment</span>
                <div class="font-display font-extrabold text-2xl text-slate-900 mt-1 font-mono">₦{{ number_format($totalCommitment, 0) }}</div>
                <span class="text-[11px] text-slate-400 mt-1 block">{{ $staffMembers->count() }} active staff members</span>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-semibold text-emerald-700 block">Total Disbursed ({{ $period }})</span>
                <div class="font-display font-extrabold text-2xl text-emerald-600 mt-1 font-mono">₦{{ number_format($totalDisbursed, 0) }}</div>
                <span class="text-[11px] text-slate-400 mt-1 block">Successfully transferred</span>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-semibold text-amber-700 block">Pending Staff</span>
                <div class="font-display font-extrabold text-2xl text-amber-600 mt-1">{{ $pendingStaffCount }}</div>
                <span class="text-[11px] text-slate-400 mt-1 block">Awaiting salary run</span>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 block">Transfer Rail</span>
                <div class="font-display font-bold text-base text-slate-900 mt-1 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Paystack Transfers</span>
                </div>
                <span class="text-[11px] text-slate-400 mt-1 block">Balance &rarr; NUBAN accounts</span>
            </div>
        </div>

        <!-- Staff List for Selected Period -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                        Active Staff Payroll &bull; {{ date('F Y', strtotime($period . '-01')) }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Manage individual salary disbursements and check payment codes.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 pl-6">Staff Member</th>
                            <th class="py-3.5 px-4">Position</th>
                            <th class="py-3.5 px-4">Bank &amp; Account</th>
                            <th class="py-3.5 px-4">Monthly Salary</th>
                            <th class="py-3.5 px-4">Period Status</th>
                            <th class="py-3.5 px-4 pr-6 text-right">Disbursement Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($staffMembers as $staff)
                            @php
                                $payment = $staff->salaryPayments->first();
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 pl-6">
                                    <div class="font-bold text-slate-900 text-sm">{{ $staff->user->name }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $staff->user->email }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $staff->position }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800">{{ $staff->bank_name }}</div>
                                    <div class="font-mono text-[11px] text-slate-500">{{ $staff->bank_account_number }}</div>
                                    @if ($staff->recipient_code)
                                        <code class="text-[10px] text-slate-400 font-mono">{{ $staff->recipient_code }}</code>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-bold text-sm text-slate-900 font-mono">
                                    ₦{{ number_format($staff->monthly_salary, 0) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($payment && $payment->status === 'success')
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Paid
                                            </span>
                                            <span class="text-[11px] text-slate-400">{{ $payment->paid_at?->format('M d, H:i') }}</span>
                                        </div>
                                    @elseif ($payment && $payment->status === 'needs_review')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100" title="{{ $payment->error_message }}">
                                            Needs Review
                                        </span>
                                    @elseif ($payment && $payment->status === 'reversed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-100">
                                            Reversed
                                        </span>
                                    @elseif ($payment && $payment->status === 'failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100" title="{{ $payment->error_message }}">
                                            Failed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 pr-6 text-right">
                                    @if ($payment && $payment->status === 'needs_review')
                                        <form method="POST" action="{{ route('admin.payroll.verify', $payment->id) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-[11px] font-semibold transition shadow-xs cursor-pointer">
                                                Check Paystack status &rarr;
                                            </button>
                                        </form>
                                    @elseif ($payment && $payment->status === 'failed')
                                        <form method="POST" action="{{ route('admin.payroll.retry', $payment->id) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-semibold transition shadow-xs cursor-pointer">
                                                Retry &rarr;
                                            </button>
                                        </form>
                                    @elseif (!$payment || $payment->status !== 'success')
                                        <form method="POST" action="{{ route('admin.payroll.disburse') }}" class="inline-block" onsubmit="return confirm('Disburse ₦{{ number_format($staff->monthly_salary, 0) }} to {{ $staff->user->name }} via Paystack?');">
                                            @csrf
                                            <input type="hidden" name="period" value="{{ $period }}">
                                            <input type="hidden" name="staff_id" value="{{ $staff->id }}">
                                            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-semibold transition shadow-xs cursor-pointer">
                                                Pay Salary &rarr;
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] font-mono text-slate-400">
                                            {{ $payment->paystack_transfer_code ?? 'Disbursed' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    No active staff members found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Salary Payments Audit History -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                        Disbursement Audit History
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Recorded Paystack transfer logs and references.</p>
                </div>
                <span class="text-xs text-slate-400">Last {{ $recentPayments->count() }} disbursements</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 pl-6">Date &amp; Time</th>
                            <th class="py-3.5 px-4">Staff Recipient</th>
                            <th class="py-3.5 px-4">Period</th>
                            <th class="py-3.5 px-4">Amount</th>
                            <th class="py-3.5 px-4">Transfer Reference</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentPayments as $p)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 pl-6 text-slate-600">
                                    {{ $p->paid_at ? $p->paid_at->format('M d, Y H:i') : $p->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $p->staff?->user?->name ?? 'Deleted Staff' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $p->staff?->position }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-medium text-slate-700">
                                    {{ $p->period }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900 font-mono">
                                    ₦{{ number_format($p->amount, 0) }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px] text-slate-600">
                                    {{ $p->paystack_transfer_code ?? $p->paystack_reference ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($p->status === 'success')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Success
                                        </span>
                                    @elseif ($p->status === 'needs_review')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-purple-50 text-purple-700 border border-purple-100" title="{{ $p->error_message }}">
                                            Needs Review
                                        </span>
                                    @elseif ($p->status === 'reversed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-orange-50 text-orange-700 border border-orange-100">
                                            Reversed
                                        </span>
                                    @elseif ($p->status === 'failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-100">
                                            Failed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-100">
                                            {{ $p->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 pr-6 text-right">
                                    @if ($p->status === 'needs_review')
                                        <form method="POST" action="{{ route('admin.payroll.verify', $p->id) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-purple-600 hover:text-purple-800 cursor-pointer">
                                                Check Paystack
                                            </button>
                                        </form>
                                    @elseif ($p->status === 'failed')
                                        <form method="POST" action="{{ route('admin.payroll.retry', $p->id) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 cursor-pointer">
                                                Retry Transfer
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-300">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    No salary disbursements recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
