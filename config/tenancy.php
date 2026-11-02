<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Base Domain
    |--------------------------------------------------------------------------
    |
    | The root domain for multi-tenant subdomains in production/staging.
    | e.g. "jessiclassiq.com", "classiq.test", or "localhost"
    |
    */
    'base_domain' => env('TENANCY_BASE_DOMAIN', 'localhost'),

    /*
    |--------------------------------------------------------------------------
    | Local / Testing Default Tenant Slug
    |--------------------------------------------------------------------------
    |
    | A local and testing convenience fallback tenant slug. In production or
    | staging environments, this value NEVER applies and resolution is strictly
    | host/domain/subdomain-based.
    |
    */
    'local_default_tenant_slug' => env('LOCAL_DEFAULT_TENANT_SLUG', env('DEFAULT_TENANT_SLUG', 'jessis-classiq-store')),
    'default_tenant_slug' => env('LOCAL_DEFAULT_TENANT_SLUG', env('DEFAULT_TENANT_SLUG', 'jessis-classiq-store')),

    /*
    |--------------------------------------------------------------------------
    | Reserved Subdomains
    |--------------------------------------------------------------------------
    |
    | Subdomains that cannot be assigned to or resolved as individual tenants.
    |
    */
    'reserved_subdomains' => [
        'www',
        'app',
        'admin',
        'api',
        'mail',
        'billing',
        'support',
        'status',
    ],
];
