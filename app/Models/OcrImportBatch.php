<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OcrImportBatch extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'image_path',
        'original_filename',
        'status',
        'raw_text',
        'parsed_data',
        'error_message',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'parsed_data' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function salesRecords(): HasMany
    {
        return $this->hasMany(SalesRecord::class, 'batch_id');
    }
}
