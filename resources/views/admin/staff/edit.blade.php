<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Team Member
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-mono">{{ $staff->position }}</span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-slate-900 mt-1">
                    Edit Staff &bull; {{ $staff->user->name }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Update profile, role, active status, compensation, and NUBAN bank details.
                </p>
            </div>
            <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Directory</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <form method="POST" action="{{ route('admin.staff.update', $staff->id) }}" class="space-y-6" x-data="{
            banks: {{ json_encode($banks) }},
            selectedBankCode: '{{ old('bank_code', $staff->bank_code) }}',
            bankName: '{{ old('bank_name', $staff->bank_name) }}',
            onBankChange(e) {
                const code = e.target.value;
                const b = this.banks.find(x => x.code === code);
                this.bankName = b ? b.name : '';
            }
        }">
            @csrf
            @method('PUT')

            <!-- Personal Details -->
            <div class="pb-6 border-b border-slate-100 space-y-4">
                <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                    1. Profile &amp; Contact
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Full Legal Name *</label>
                        <input type="text" name="name" value="{{ old('name', $staff->user->name) }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                        @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Address</label>
                        <input type="email" disabled value="{{ $staff->user->email }}" class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-500 cursor-not-allowed font-mono">
                        <span class="text-[10px] text-slate-400 mt-1 block">Email is fixed to user authentication account.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone', $staff->user->phone) }}" class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Position / Role *</label>
                        <input type="text" name="position" value="{{ old('position', $staff->position) }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $staff->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs font-semibold text-slate-700">Active Staff Member (Eligible for Monthly Payroll Runs)</span>
                    </label>
                </div>
            </div>

            <!-- Compensation & Bank Details -->
            <div class="pb-6 border-b border-slate-100 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                        2. Compensation &amp; Bank Account
                    </h2>
                    @if ($staff->recipient_code)
                        <span class="text-[10px] font-mono bg-emerald-50 text-emerald-700 border border-emerald-100 px-2.5 py-1 rounded-full font-bold">
                            {{ $staff->recipient_code }}
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Monthly Salary (NGN ₦) *</label>
                        <input type="number" step="0.01" name="monthly_salary" value="{{ old('monthly_salary', $staff->monthly_salary) }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 font-mono focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                        @error('monthly_salary') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Select Bank *</label>
                        <select name="bank_code" x-model="selectedBankCode" @change="onBankChange($event)" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                            @foreach ($banks as $bank)
                                <option value="{{ $bank['code'] }}" {{ old('bank_code', $staff->bank_code) === $bank['code'] ? 'selected' : '' }}>
                                    {{ $bank['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="bank_name" :value="bankName">
                        @error('bank_code') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">10-Digit NUBAN Account Number *</label>
                    <input type="text" name="bank_account_number" maxlength="10" value="{{ old('bank_account_number', $staff->bank_account_number) }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-mono font-bold tracking-wider text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                    <p class="text-[11px] text-slate-400 mt-1">If bank or account number changes, a new Paystack recipient code will automatically be generated.</p>
                    @error('bank_account_number') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-semibold text-xs tracking-wide transition shadow-xs cursor-pointer flex items-center justify-center gap-2">
                <span>Save Changes &rarr;</span>
            </button>
        </form>
    </div>
</x-admin-layout>
