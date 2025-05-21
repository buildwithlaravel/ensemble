<?php

namespace BuildWithLaravel\Ensemble\Core;

use BuildWithLaravel\Ensemble\Models\Run;
use BuildWithLaravel\Ensemble\Support\MemoryStore;
use BuildWithLaravel\Ensemble\Support\Traits\InteractsWithLLM;
use RuntimeException;

/**
 * Abstract base class for agents with built-in Prism LLM support.
 */
abstract class Agent
{
    use InteractsWithLLM;

    protected Run $run;

    protected MemoryStore $memoryStore;

    public function __construct(Run $run)
    {
        $this->run = $run;
        if (!$run->runnable) {
            throw new RuntimeException('Agent run must have a runnable to use memory.');
        }
        $this->memoryStore = MemoryStore::for($run->runnable);
    }

    public function memory(): MemoryStore
    {
        return $this->memoryStore;
    }

    public function steps(): array
    {
        return [];
    }

    public function stateClass(): string
    {
        return GenericState::class;
    }

    public function getRun(): Run
    {
        return $this->run;
    }

    public function defineArtifact(Run $run, State $state): ?Artifact
    {
        // Data for the default status artifact
        $artifactData = [
            'run_id' => $run->id,
            'agent_class' => $run->agent_class,
            'status' => $run->status,
            'current_step_index' => $run->current_step_index,
            'last_ran_at' => $run->last_ran_at?->toIso8601String(),
            'state_data' => $state->data, // Expose the raw state data for debugging/display
            'recent_logs' => $run->logs()->latest()->take(10)->get()->map(fn($log) => [
                'message' => $log->message,
                'level' => $log->level,
                'context' => $log->context,
                'created_at' => $log->created_at?->toIso8601String(),
            ])->toArray(), // Fetch last 10 logs and format them
        ];

        if ($state->isInterrupted()) {
            $artifactData['interrupt'] = [
                'type' => $state->getInterrupt()?->value,
                'meta' => $state->getMeta(),
            ];
        }

        return Artifact::make('ensemble-status', $artifactData);
    }

    public function currentStep(?int $index = null): mixed
    {
        $steps = $this->steps();
        $idx = $index ?? $this->run->current_step_index ?? 0;
        if (!isset($steps[$idx])) {
            return null;
        }
        $step = $steps[$idx];
        if (is_callable($step)) {
            // Pass the current state from the run
            $state = $this->run->state;
            return $step($state);
        }
        return $step;
    }
}
