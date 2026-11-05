<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(
        protected CurrentTenant $currentTenant
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->currentTenant->setHttpRequestContext(true);

        try {
            // Webhook routes resolve their own tenant from the payment reference
            if ($request->is('paystack/webhook') || $request->is('*/paystack/webhook')) {
                return $next($request);
            }

            // Explicit platform switch via header or query parameter
            $explicitPlatform = $request->header('X-Platform') === 'true'
                || $request->header('X-Tenant') === 'none'
                || $request->query('platform') === '1'
                || $request->query('tenant') === 'none'
                || $request->is('super-admin')
                || $request->is('super-admin/*');

            if ($explicitPlatform) {
                if ($request->hasSession()) {
                    $request->session()->forget('active_tenant_id');
                }
                $this->currentTenant->forget();

                if (function_exists('setPermissionsTeamId')) {
                    setPermissionsTeamId(null);
                }

                return $next($request);
            }

            $tenant = $this->resolveTenant($request);

            if (! $tenant && app()->environment('testing')) {
                if ($this->currentTenant->check()) {
                    $tenant = $this->currentTenant->get();
                } else {
                    $defaultSlug = config('tenancy.local_default_tenant_slug', config('tenancy.default_tenant_slug', 'jessis-classiq-store'));
                    $tenant = Tenant::where('slug', $defaultSlug)->first() ?? Tenant::first();
                }
            }

            if (! $tenant) {
                // Allow platform-level requests (apex domain, localhost, reserved subdomain, or explicit platform request)
                if ($this->isPlatformRequest($request)) {
                    $this->currentTenant->forget();

                    if (function_exists('setPermissionsTeamId')) {
                        setPermissionsTeamId(null);
                    }

                    return $next($request);
                }

                abort(404, 'Store not found.');
            }

            if ($tenant->status !== 'active') {
                abort(503, 'This store is currently inactive.');
            }

            $this->currentTenant->set($tenant);

            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($tenant->id);
            }

            return $next($request);
        } finally {
            if (app()->runningInConsole()) {
                $this->currentTenant->setHttpRequestContext(false);
            }
        }
    }

    protected function isPlatformRequest(Request $request): bool
    {
        $host = strtolower($request->getHost());
        $baseDomain = strtolower((string) config('tenancy.base_domain', 'localhost'));
        $reserved = config('tenancy.reserved_subdomains', ['www', 'app', 'admin', 'api', 'mail', 'billing', 'support', 'status']);

        // Check if host matches base domain, localhost, or standard loopback IP
        if ($host === $baseDomain || $host === 'localhost' || $host === '127.0.0.1') {
            return true;
        }

        // Check if host is on a reserved subdomain of the base domain
        if (! empty($baseDomain) && str_ends_with($host, '.'.$baseDomain)) {
            $extracted = substr($host, 0, -(strlen($baseDomain) + 1));
            $parts = explode('.', $extracted);
            $subdomain = end($parts);
            if (in_array($subdomain, $reserved, true)) {
                return true;
            }
        }

        return false;
    }

    protected function resolveTenant(Request $request): ?Tenant
    {
        // 0. Support explicit test / API tenant identifier header ONLY in local/testing
        if (app()->environment(['local', 'testing'])) {
            if ($header = $request->header('X-Tenant')) {
                $tenant = Tenant::where('slug', $header)
                    ->orWhere('subdomain', $header)
                    ->orWhere('id', is_numeric($header) ? (int) $header : 0)
                    ->first();

                if ($tenant) {
                    return $tenant;
                }
            }
        }

        // 0b. Support ?tenant=slug query switcher on platform hosts
        if ($queryTenant = $request->query('tenant')) {
            if (is_string($queryTenant) && $queryTenant !== 'none') {
                $tenant = Tenant::where('slug', $queryTenant)
                    ->orWhere('subdomain', $queryTenant)
                    ->first();

                if ($tenant) {
                    if ($request->hasSession()) {
                        $request->session()->put('active_tenant_id', $tenant->id);
                    }

                    return $tenant;
                }
            }
        }

        $host = strtolower($request->getHost());
        $isIp = filter_var($host, FILTER_VALIDATE_IP) !== false;
        $baseDomain = strtolower((string) config('tenancy.base_domain', 'localhost'));
        $reserved = config('tenancy.reserved_subdomains', ['www', 'app', 'admin', 'api', 'mail', 'billing', 'support', 'status']);

        // 1. Resolve by subdomain against config('tenancy.base_domain') (skip if host is an IP address)
        $subdomain = null;
        if (! $isIp) {
            if (! empty($baseDomain) && str_ends_with($host, '.'.$baseDomain)) {
                $extracted = substr($host, 0, -(strlen($baseDomain) + 1));
                $parts = explode('.', $extracted);
                $subdomain = end($parts);
            } elseif (app()->environment(['local', 'testing'])) {
                // Local/testing support for hostnames like "tenant.test"
                $parts = explode('.', $host);
                if (count($parts) >= 2) {
                    $subdomain = $parts[0];
                }
            }
        }

        if ($subdomain && ! in_array($subdomain, $reserved, true)) {
            $tenant = Tenant::where('subdomain', $subdomain)->first();
            if ($tenant) {
                return $tenant;
            }
        }

        // 2. Resolve by custom domain
        $tenant = Tenant::where('domain', $host)->first();
        if ($tenant) {
            return $tenant;
        }

        if (! $isIp) {
            $strippedHost = preg_replace('/^www\./', '', $host);
            if ($strippedHost !== $host) {
                $tenant = Tenant::where('domain', $strippedHost)->first();
                if ($tenant) {
                    return $tenant;
                }
            }
        }

        // 3. Resolve from active session tenant when operating on a platform host
        if ($this->isPlatformRequest($request) && $request->hasSession()) {
            $sessionTenantId = $request->session()->get('active_tenant_id');
            if ($sessionTenantId) {
                $tenant = Tenant::find($sessionTenantId);
                if ($tenant) {
                    return $tenant;
                }
            }
        }

        return null;
    }
}
