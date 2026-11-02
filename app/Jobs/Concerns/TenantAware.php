<?php

namespace App\Jobs\Concerns;

use App\Jobs\Middleware\SetTenantContext;
use App\Models\Tenant;
use App\Services\CurrentTenant;

trait TenantAware
{
    public ?int $tenantId = null;

    /**
     * Explicitly assign a tenant to the job.
     */
    public function forTenant(Tenant|int|null $tenant = null): static
    {
        if ($tenant instanceof Tenant) {
            $this->tenantId = $tenant->id;
        } elseif (is_numeric($tenant)) {
            $this->tenantId = (int) $tenant;
        } else {
            $this->tenantId = app(CurrentTenant::class)->id();
        }

        return $this;
    }

    /**
     * Capture the current tenant context.
     */
    public function captureTenantContext(?int $tenantId = null): static
    {
        $this->tenantId = $tenantId ?? app(CurrentTenant::class)->id();

        return $this;
    }

    /**
     * Job middleware stack.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            new SetTenantContext($this->tenantId),
        ];
    }
}
