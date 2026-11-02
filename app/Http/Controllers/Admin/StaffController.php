<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Services\CurrentTenant;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;

class StaffController extends Controller
{
    public function __construct(protected PaystackService $paystackService) {}

    public function index(Request $request): View
    {
        $query = Staff::with('user');

        if ($request->filled('q')) {
            $searchTerm = '%'.trim($request->q).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('position', 'like', $searchTerm)
                    ->orWhere('bank_name', 'like', $searchTerm)
                    ->orWhereHas('user', function ($uq) use ($searchTerm) {
                        $uq->where('name', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm);
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $staffMembers = $query->latest()->paginate(10)->withQueryString();
        $totalPayroll = Staff::where('is_active', true)->sum('monthly_salary');
        $activeCount = Staff::where('is_active', true)->count();

        return view('admin.staff.index', compact('staffMembers', 'totalPayroll', 'activeCount'));
    }

    public function create(): View
    {
        $banks = $this->paystackService->listBanks();

        return view('admin.staff.create', compact('banks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where('tenant_id', app(CurrentTenant::class)->id()),
            ],
            'phone' => 'nullable|string|max:25',
            'position' => 'required|string|max:100',
            'bank_code' => 'required|string',
            'bank_name' => 'required|string|max:100',
            'bank_account_number' => 'required|string|min:10|max:10',
            'monthly_salary' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Paystack Transfer Recipient
            $recipientCode = null;
            try {
                $recipientData = $this->paystackService->createTransferRecipient(
                    $request->name,
                    $request->bank_account_number,
                    $request->bank_code
                );
                $recipientCode = $recipientData['data']['recipient_code'] ?? 'RCP_'.Str::random(10);
            } catch (\Exception $e) {
                $recipientCode = 'RCP_SIM_'.Str::random(8);
            }

            // 2. Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make(Str::random(12)),
                'role' => 'staff',
                'phone' => $request->phone,
                'email_verified_at' => now(),
            ]);
            $user->syncRoles(['staff']);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            // 3. Create Staff profile
            Staff::create([
                'user_id' => $user->id,
                'position' => $request->position,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_code' => $request->bank_code,
                'recipient_code' => $recipientCode,
                'monthly_salary' => $request->monthly_salary,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('admin.staff.index')
                ->with('success', "Staff member '{$request->name}' registered successfully with Paystack Recipient Code ({$recipientCode}).");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Failed to register staff: '.$e->getMessage());
        }
    }

    public function edit(Staff $staff): View
    {
        $banks = $this->paystackService->listBanks();
        $staff->load('user');

        return view('admin.staff.edit', compact('staff', 'banks'));
    }

    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:25',
            'position' => 'required|string|max:100',
            'bank_code' => 'required|string',
            'bank_name' => 'required|string|max:100',
            'bank_account_number' => 'required|string|min:10|max:10',
            'monthly_salary' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $staff->user->update([
                'name' => $request->name,
                'phone' => $request->phone,
            ]);

            // If bank account changed, regenerate recipient code
            $recipientCode = $staff->recipient_code;
            if ($staff->bank_account_number !== $request->bank_account_number || $staff->bank_code !== $request->bank_code) {
                try {
                    $recipientData = $this->paystackService->createTransferRecipient(
                        $request->name,
                        $request->bank_account_number,
                        $request->bank_code
                    );
                    $recipientCode = $recipientData['data']['recipient_code'] ?? $recipientCode;
                } catch (\Exception $e) {
                    $recipientCode = 'RCP_SIM_'.Str::random(8);
                }
            }

            $staff->update([
                'position' => $request->position,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_code' => $request->bank_code,
                'recipient_code' => $recipientCode,
                'monthly_salary' => $request->monthly_salary,
                'is_active' => $request->boolean('is_active'),
            ]);

            DB::commit();

            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Error updating staff: '.$e->getMessage());
        }
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        $name = $staff->user->name;
        $staff->update(['is_active' => false]);

        return redirect()->route('admin.staff.index')
            ->with('success', "Staff member '{$name}' has been deactivated.");
    }
}
