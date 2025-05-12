<?php

namespace BuildWithLaravel\Ensemble\Core;

use BuildWithLaravel\Ensemble\Support\Parallel;
use Illuminate\Support\Facades\Bus;
use RuntimeException;

class Workflow
{
    /**
     * The agent instance.
     */
    protected Agent $agent;

    /**
     * The array of resolved step instances.
     *
     * @var array<int, mixed>
     */
    protected array $steps;

    /**
     * The current step index.
     */
    protected int $currentIndex = 0;

    /**
     * Create a new Workflow instance.
     */
    public function __construct(Agent $agent, array $steps)
    {
        $this->agent = $agent;
        $this->steps = $this->resolveSteps($steps);
    }

    /**
     * Resolve the array of steps, instantiating any class names.
     */
    protected function resolveSteps(array $steps): array
    {
        return array_map(function ($step) {
            if (is_string($step)) {
                return new $step();
            }

            return $step;
        }, $steps);
    }

    /**
     * Set the starting step index.
     */
    public function setStartStep(int $index): void
    {
        $this->currentIndex = $index;
    }

    /**
     * Get the current step index.
     */
    public function currentStepIndex(): int
    {
        return $this->currentIndex;
    }

    /**
     * Run the workflow.
     */
    public function run(State $state): State
    {
        $totalSteps = count($this->steps);

        for ($i = $this->currentIndex; $i < $totalSteps; $i++) {
            // Store current index before executing step
            $this->currentIndex = $i;
            
            $stepInstance = $this->steps[$this->currentIndex];

            // Handle parallel execution
            if ($stepInstance instanceof Parallel) {
                $state = $this->executeParallelSteps($stepInstance, $state);
                
                // Check if state was interrupted during parallel execution
                if ($state->isInterrupted()) {
                    $this->persistRunState($state);
                    return $state;
                }
                
                continue;
            }

            // Execute single step
            $resultState = $stepInstance->handle($this->agent, $state);

            if (!$resultState instanceof State) {
                throw new RuntimeException(
                    sprintf('Step %s must return an instance of %s', get_class($stepInstance), State::class)
                );
            }

            // Check for interrupts
            if ($resultState->isInterrupted()) {
                $this->persistRunState($resultState);
                return $resultState;
            }

            // Update state for next iteration
            $state = $resultState;
            $this->persistRunState($state);
        }

        return $state;
    }

    /**
     * Execute a set of steps in parallel.
     */
    protected function executeParallelSteps(Parallel $parallel, State $state): State
    {
        // Create jobs for each parallel step
        $jobs = array_map(function ($step) {
            return new \BuildWithLaravel\Ensemble\Jobs\RunStepJob(
                $this->agent->run()->id,
                is_string($step) ? new $step() : $step
            );
        }, $parallel->steps);

        // Dispatch jobs as a batch and wait for completion
        $batch = Bus::batch($jobs)
            ->allowFailures()
            ->dispatch();

        $batch->waitUntilFinished();

        // Re-hydrate state from persistent storage
        $state = $this->agent->run()->state();

        // Handle any batch failures
        if ($batch->hasFailures()) {
            return $state->halt('One or more parallel steps failed');
        }

        return $state;
    }

    /**
     * Persist the current state and step index to the Run model.
     */
    protected function persistRunState(State $state): void
    {
        $this->agent->run()->update([
            'current_step_index' => $this->currentIndex,
            'state' => $state,
        ]);
    }
} 