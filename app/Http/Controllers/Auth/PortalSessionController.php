<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PortalSessionController extends Controller
{
    /**
     * Display the admin login view.
     */
    public function adminLogin(): View
    {
        return view('auth.admin-login');
    }

    /**
     * Handle an incoming admin authentication request.
     * Only allows users with role of 'admin'.
     */
    public function adminStore(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        /** @var User $user */
        $user = Auth::user();

        if (! $user->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'These credentials are not authorised for Admin access.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Display the staff login view.
     */
    public function staffLogin(): View
    {
        return view('auth.staff-login');
    }

    /**
     * Handle an incoming staff authentication request.
     * Only allows users with role of 'staff' or 'admin'.
     */
    public function staffStore(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        /** @var User $user */
        $user = Auth::user();

        if (! $user->isStaff()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.login')
                ->withErrors(['email' => 'These credentials are not authorised for Staff access.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Direct test login bypass for development/testing environments.
     */
    public function quickLogin(Request $request, string $role): RedirectResponse
    {
        $role = strtolower(trim($role));

        $dashboards = [
            'admin' => [
                'email' => 'admin@jessiclassiq.com',
                'name' => 'Jessi Admin',
                'role' => 'admin',
                'target' => route('admin.dashboard', absolute: false),
                'label' => 'Admin Atelier Dashboard',
            ],
            'staff' => [
                'email' => 'staff@jessiclassiq.com',
                'name' => 'Grace Staff',
                'role' => 'staff',
                'target' => route('admin.dashboard', absolute: false),
                'label' => 'Staff Atelier Dashboard',
            ],
            'customer' => [
                'email' => 'customer@jessiclassiq.com',
                'name' => 'Amaka Customer',
                'role' => 'customer',
                'target' => route('dashboard', absolute: false),
                'label' => 'Customer Privé Dashboard',
            ],
        ];

        if (! array_key_exists($role, $dashboards)) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid test role specified.']);
        }

        $config = $dashboards[$role];

        // Find or create test user
        $user = User::firstOrCreate(
            ['email' => $config['email']],
            [
                'name' => $config['name'],
                'password' => Hash::make('password123'),
                'role' => $config['role'],
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasRole($config['role'])) {
            $user->syncRoles([$config['role']]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect($config['target'])->with('success', "Testing Mode: Logged in directly as {$user->name} ({$config['label']}).");
    }
}
