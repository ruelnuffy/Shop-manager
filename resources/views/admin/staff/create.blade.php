<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Team Onboarding
                    </span>
                </div>
                <h1 class="font-display font-extrabold text-2xl uppercase tracking-tight text-slate-900 mt-1">
                    Onboard New Staff Member
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Create employee account, assign store role, set monthly compensation, and link NUBAN bank account.
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
        <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-6" x-data="{
            banks: {{ json_encode($banks) }},
            selectedBankCode: '{{ old('bank_code', '') }}',
            bankName: '{{ old('bank_name', '') }}',
            onBankChange(e) {
                const code = e.target.value;
                const b = this.banks.find(x => x.code === code);
                this.bankName = b ? b.name : '';
            }
        }">
            @csrf

            <!-- Personal / User Details -->
            <div class="pb-6 border-b border-slate-100 space-y-4">
                <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                    1. Team Member Details
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Full Legal Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition" placeholder="e.g. Chioma Adebayo">
                        @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition" placeholder="chioma@store.test">
                        @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition" placeholder="+234 801 234 5678">
                        @error('phone') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Position / Role *</label>
                        <input type="text" name="position" value="{{ old('position') }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition" placeholder="e.g. Store Sales Lead">
                        @error('position') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Compensation & Bank Details -->
            <div class="pb-6 border-b border-slate-100 space-y-4">
                <h2 class="font-bold text-sm text-slate-900 uppercase tracking-wider">
                    2. Bank Account &amp; Paystack Direct Transfer
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Monthly Salary (NGN ₦) *</label>
                        <input type="number" step="0.01" name="monthly_salary" value="{{ old('monthly_salary') }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition" placeholder="e.g. 180000">
                        @error('monthly_salary') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Select Bank *</label>
                        <select name="bank_code" x-model="selectedBankCode" @change="onBankChange($event)" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition">
                            <option value="">Choose Nigerian Bank</option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank['code'] }}" {{ old('bank_code') === $bank['code'] ? 'selected' : '' }}>
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
                    <input type="text" name="bank_account_number" maxlength="10" value="{{ old('bank_account_number') }}" required class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-mono font-bold tracking-wider text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition" placeholder="0123456789">
                    <p class="text-[11px] text-slate-400 mt-1">Paystack will verify this account number and generate a secure Transfer Recipient Code for one-click payroll.</p>
                    @error('bank_account_number') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-semibold text-xs tracking-wide transition shadow-xs cursor-pointer flex items-center justify-center gap-2">
                <span>Save &amp; Generate Paystack Recipient Code &rarr;</span>
            </button>
        </form>
    </div>
</x-admin-layout>
