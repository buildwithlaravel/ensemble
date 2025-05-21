<?php

namespace BuildWithLaravel\Ensemble\Models;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\Artifact;
use BuildWithLaravel\Ensemble\Core\GenericState;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Database\Factories\RunFactory;
use BuildWithLaravel\Ensemble\Enums\RunStatus;
use BuildWithLaravel\Ensemble\Runtime\RunResumer;
use BuildWithLaravel\Ensemble\Support\Traits\HasEvents;
use BuildWithLaravel\Ensemble\Support\Traits\ResolvesEnsembleModels;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * @property ?Carbon $last_ran_at
 * @property State $state
 * @property mixed $status
 * @property int|mixed $current_step_index
 * @property string $agent
 * @property mixed $runnable_id
 * @property string $runnable_type
 */
class Run extends Model
{
    use HasFactory;
    use HasUuids;
    use ResolvesEnsembleModels;
    use HasEvents;

    protected $table = 'ensemble_runs';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'runnable_id',
        'runnable_type',
        'agent',
        'state',
        'status',
        'current_step_index',
        'last_ran_at',
    ];

    protected $casts = [
        'status' => RunStatus::class,
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

    public function logs(): HasMany
    {
        return $this->hasMany(RunLog::class, 'run_id');
    }

    public function createLog(string $message, array $context = []): RunLog
    {
        return $this->logs()->create([
            'level' => 'info',
            'message' => $message,
            'context' => $context,
        ]);
    }

    public function getStateAttribute(): State
    {
        $stateClass = $this->getAgentInstance()->stateClass();
        if (!$stateClass || !class_exists($stateClass) || !is_subclass_of($stateClass, State::class)) {
            $stateClass = GenericState::class;
        }

        return $stateClass::from($this->fromJson($this->getAttributeFromArray('state')) ?? []);
    }

    /**
     * Hydrate the agent instance for this run.
     */
    public function getAgentInstance(): Agent
    {
        $class = $this->agent;
        if (!$class || !class_exists($class)) {
            throw new RuntimeException('No valid agent class set on Run.');
        }
        if (!is_subclass_of($class, Agent::class)) {
            throw new RuntimeException('Agent class must extend BuildWithLaravel\\Ensemble\\Core\\Agent.');
        }

        return app($class, ['run' => $this]);
    }

    public function resume(array $userInput): Run
    {
        return app(RunResumer::class)->resume($this, $userInput);
    }

    /**
     * Get the current artifact representation for frontend display based on the run's state and status.
     */
    public function currentArtifact(): ?Artifact
    {
        return $this->getAgentInstance()->defineArtifact($this, $this->state);
    }

    protected static function newFactory()
    {
        return RunFactory::new();
    }

    public function currentStep()
    {
        return $this->getAgentInstance()->currentStep();
    }
}
