<?php

namespace BuildWithLaravel\Ensemble\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interrupt extends Model
{
    use HasUuids;

    protected $table = 'ensemble_interrupts';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'run_id',
        'type',
        'payload',
        'interrupted_at',
        'resolved_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'interrupted_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class, 'run_id');
    }
} 