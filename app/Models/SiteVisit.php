<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteVisit extends Model
{
    protected $fillable = [
        'tenant_id',
        'session_id',
        'ip_address',
        'path',
        'referer',
        'user_agent',
        'visited_on',
    ];

    protected function casts(): array
    {
        return [
            'visited_on' => 'date',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
