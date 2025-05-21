<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'summary',
        'topics',
        'original_run_id',
    ];

    protected $casts = [
        'topics' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
