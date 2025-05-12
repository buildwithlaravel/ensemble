<?php

namespace BuildWithLaravel\Ensemble\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ensemble\Core\State;

class Run extends Model
{
    use HasUuids;

    protected $table = 'ensemble_runs';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'runnable_id',
        'runnable_type',
        'agent_class',
        'state',
        'status',
        'current_step_index',
        'last_ran_at',
    ];

    protected $casts = [
        'state' => 'array',
        'last_ran_at' => 'datetime',
    ];

    public function runnable(): MorphTo
    {
        return $this->morphTo();
    }

    public function interrupts(): HasMany
    {
        return $this->hasMany(Interrupt::class, 'run_id');
    }

    public function runLogs(): HasMany
    {
        return $this->hasMany(RunLog::class, 'run_id');
    }

    public function createLog(string $message, array $context = []): RunLog
    {
        return $this->runLogs()->create([
            'level' => 'info',
            'message' => $message,
            'context' => $context,
        ]);
    }

    public function state(): State
    {
        return State::fromArray($this->state ?? []);
    }
} 