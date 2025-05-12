<?php

namespace BuildWithLaravel\Ensemble\Models;

use BuildWithLaravel\Ensemble\Core\State;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        $agent = $this->getAgentInstance();
        $stateClass = null;
        if ($agent && method_exists($agent, 'getStateClass')) {
            $stateClass = $agent->getStateClass();
        }
        if (!$stateClass || !class_exists($stateClass)) {
            $stateClass = \BuildWithLaravel\Ensemble\Core\GenericState::class;
        }
        return $stateClass::from($this->state ?? []);
    }

    /**
     * Hydrate the agent instance for this run.
     */
    public function getAgentInstance(): ?\BuildWithLaravel\Ensemble\Core\Agent
    {
        $class = $this->agent_class;
        if ($class && class_exists($class)) {
            return app($class);
        }
        return null;
    }
}
