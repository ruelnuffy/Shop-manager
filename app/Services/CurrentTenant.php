<?php

namespace App\Services;

use App\Models\Tenant;

class CurrentTenant
{
    public ?Tenant $tenant = null;

    public bool $isHttpRequest = false;

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): void
    {
        $this->tenant = $tenant;

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($tenant?->id);
        }
    }

    public function getTenantId(): ?int
    {
        return $this->tenant?->id;
    }

    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    public function forgetTenant(): void
    {
        $this->setTenant(null);
    }

    public function isCurrentTenant(?Tenant $tenant): bool
    {
        if (! $this->tenant || ! $tenant) {
            return false;
        }

        return $this->tenant->id === $tenant->id;
    }

    public function isHttpRequestContext(): bool
    {
        if ($this->isHttpRequest) {
            return true;
        }

        if (! app()->runningInConsole()) {
            return true;
        }

        return false;
    }

    public function setHttpRequestContext(bool $isHttp): void
    {
        $this->isHttpRequest = $isHttp;
    }

    public function buildCacheKey(string $key): string
    {
        $tenantId = $this->getTenantId() ?? 'global';

        return "tenant:{$tenantId}:{$key}";
    }

    public function executeRunAs(?Tenant $tenant, callable $callback): mixed
    {
        $previousTenant = $this->tenant;
        $this->setTenant($tenant);

        try {
            return $callback();
        } finally {
            $this->setTenant($previousTenant);
        }
    }

    // Static facade methods
    public static function get(): ?Tenant
    {
        return app(self::class)->getTenant();
    }

    public static function set(?Tenant $tenant): void
    {
        app(self::class)->setTenant($tenant);
    }

    public static function id(): ?int
    {
        return app(self::class)->getTenantId();
    }

    public static function check(): bool
    {
        return app(self::class)->hasTenant();
    }

    public static function forget(): void
    {
        app(self::class)->forgetTenant();
    }

    public static function is(?Tenant $tenant): bool
    {
        return app(self::class)->isCurrentTenant($tenant);
    }

    public static function runAs(?Tenant $tenant, callable $callback): mixed
    {
        return app(self::class)->executeRunAs($tenant, $callback);
    }

    public static function cacheKey(string $key): string
    {
        return app(self::class)->buildCacheKey($key);
    }

    public static function isHttpRequest(): bool
    {
        return app(self::class)->isHttpRequestContext();
    }

    public static function setIsHttpRequest(bool $isHttp): void
    {
        app(self::class)->setHttpRequestContext($isHttp);
    }

    public static function instance(): self
    {
        return app(self::class);
    }
}
