<?php

namespace App\Models;

use App\Services\CurrentTenant;
use App\Traits\BelongsToTenant;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use BelongsToTenant, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'city',
        'avatar',
        'wallet_balance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'wallet_balance' => 'decimal:2',
        ];
    }

    /**
     * Override BelongsToTenant boot logic for User model.
     * When a tenant is active, scope user queries to that tenant.
     * When in a platform / super-admin / neutral context without an active tenant,
     * allow user lookups without throwing TenantNotResolvedException so authentications
     * and super-admin accounts can resolve.
     */
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = app(CurrentTenant::class)->id();

            if ($tenantId) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', $tenantId);
            }
        });

        static::creating(function (Model $model) {
            if (! $model->tenant_id && ($tenantId = app(CurrentTenant::class)->id())) {
                $model->tenant_id = $tenantId;
            }
        });
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin'
            || $this->hasRole('super_admin')
            || $this->email === 'superadmin@shopmanager.test';
    }

    public function isAdmin(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->tenant_id && CurrentTenant::check() && $this->tenant_id !== CurrentTenant::id()) {
            return false;
        }

        return $this->role === 'admin' || $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->tenant_id && CurrentTenant::check() && $this->tenant_id !== CurrentTenant::id()) {
            return false;
        }

        return in_array($this->role, ['staff', 'admin', 'super_admin']) || $this->hasAnyRole(['staff', 'admin', 'super_admin']);
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function staffProfile(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function creditWallet(float $amount, string $description, ?string $reference = null, ?int $orderId = null): WalletTransaction
    {
        $this->increment('wallet_balance', $amount);
        $this->refresh();

        return $this->walletTransactions()->create([
            'type' => 'deposit',
            'amount' => $amount,
            'balance_after' => $this->wallet_balance,
            'description' => $description,
            'reference' => $reference,
            'status' => 'completed',
            'order_id' => $orderId,
        ]);
    }

    public function debitWallet(float $amount, string $description, ?string $reference = null, ?int $orderId = null): WalletTransaction
    {
        if ($this->wallet_balance < $amount) {
            throw new \Exception('Insufficient wallet balance.');
        }

        $this->decrement('wallet_balance', $amount);
        $this->refresh();

        return $this->walletTransactions()->create([
            'type' => 'payment',
            'amount' => $amount,
            'balance_after' => $this->wallet_balance,
            'description' => $description,
            'reference' => $reference,
            'status' => 'completed',
            'order_id' => $orderId,
        ]);
    }
}
