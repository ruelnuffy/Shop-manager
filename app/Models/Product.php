<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'name',
        'slug',
        'description',
        'base_price',
        'featured_image',
        'video_url',
        'images',
        'brand_tags',
        'status',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'images' => 'array',
            'brand_tags' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        $val = $this->attributes['featured_image'] ?? null;
        if (empty($val)) {
            return null;
        }

        if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
            return $val;
        }

        return asset(ltrim($val, '/'));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function totalStock(): int
    {
        return (int) $this->variants()->sum('stock_quantity');
    }

    public function inStock(): bool
    {
        return $this->totalStock() > 0;
    }
}
