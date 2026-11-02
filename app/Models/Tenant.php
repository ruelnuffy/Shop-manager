<?php

namespace App\Models;

use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'domain',
        'settings',
        'paystack_public_key',
        'paystack_secret_key',
        'plan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'paystack_public_key' => 'encrypted',
            'paystack_secret_key' => 'encrypted',
        ];
    }

    /**
     * Retrieve a nested setting value using dot notation.
     */
    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings ?? [], $key, $default);
    }

    /**
     * Check if a specific feature flag is enabled for this tenant.
     */
    public function hasFeature(string $feature): bool
    {
        return (bool) $this->setting("features.{$feature}", false);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function salesRecords(): HasMany
    {
        return $this->hasMany(SalesRecord::class);
    }

    public function ocrImportBatches(): HasMany
    {
        return $this->hasMany(OcrImportBatch::class);
    }

    public function shopLocations(): HasMany
    {
        return $this->hasMany(ShopLocation::class);
    }
}
