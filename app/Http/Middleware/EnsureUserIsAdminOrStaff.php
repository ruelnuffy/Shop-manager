<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdminOrStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isStaff()) {
            abort(403, 'Unauthorized access. This area is reserved for boutique staff and administration.');
        }

        $tenant = app(CurrentTenant::class)->get();
        if (! $tenant && $request->user()->tenant_id) {
            $tenant = Tenant::find($request->user()->tenant_id);
            if ($tenant) {
                app(CurrentTenant::class)->set($tenant);
                if ($request->hasSession()) {
                    $request->session()->put('active_tenant_id', $tenant->id);
                }
            }
        }

        if ($tenant && ! $tenant->isActive() && ! $request->routeIs('admin.dashboard')) {
            if ($request->expectsJson()) {
                abort(403, 'Your plan is pending activation.');
            }

            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Your plan is pending activation. Please message us on WhatsApp to activate your plan.');
        }

        return $next($request);
    }
}
