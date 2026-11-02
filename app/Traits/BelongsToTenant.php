<?php

namespace App\Traits;

use App\Exceptions\TenantNotResolvedException;
use App\Models\Tenant;
use App\Services\CurrentTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = app(CurrentTenant::class)->id();

            if ($tenantId) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', $tenantId);

                return;
            }

            if (CurrentTenant::isHttpRequest()) {
                throw new TenantNotResolvedException(
                    'Attempted to query tenant-scoped model ['.get_class($builder->getModel()).'] without an active tenant in an HTTP request.'
                );
            }
        });

        static::creating(function (Model $model) {
            if (! $model->tenant_id && ($tenantId = app(CurrentTenant::class)->id())) {
                $model->tenant_id = $tenantId;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeWithoutTenancy(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }
}
