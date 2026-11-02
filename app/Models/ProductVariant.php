<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'size',
        'color',
        'sku',
        'stock_quantity',
        'price_override',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stock_quantity' => 'integer',
            'price_override' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->price_override ?? $this->product->base_price ?? 0);
    }

    public function inStock(): bool
    {
        return $this->stock_quantity > 0;
    }
}
