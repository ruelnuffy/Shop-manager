<?php

namespace App\Jobs\Middleware;

use App\Models\Tenant;
use App\Services\CurrentTenant;
use Closure;

class SetTenantContext
{
    public function __construct(
        public ?int $tenantId = null
    ) {}

    public function handle(object $job, Closure $next): mixed
    {
        $tenantId = $this->tenantId;
        if (! $tenantId && isset($job->tenantId)) {
            $tenantId = $job->tenantId;
        }

        $tenant = $tenantId ? Tenant::find($tenantId) : null;

        return CurrentTenant::runAs($tenant, function () use ($next, $job) {
            return $next($job);
        });
    }
}
