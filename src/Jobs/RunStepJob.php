<?php

namespace BuildWithLaravel\Ensemble\Jobs;

use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Models\Run;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class RunStepJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $runId;

    protected string $stepClass;

    /**
     * Create a new job instance.
     */
    public function __construct(string $runId, string $stepClass)
    {
        $this->runId = $runId;
        $this->stepClass = $stepClass;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var Run $run */
        $run = Run::find($this->runId);
        if (! $run) {
            Log::error('RunStepJob: Run not found', ['runId' => $this->runId]);

            return;
        }

        // Hydrate agent instance (placeholder method, must exist on Run)
        if (! method_exists($run, 'getAgentInstance')) {
            Log::error('RunStepJob: getAgentInstance method missing on Run', ['runId' => $this->runId]);

            return;
        }
        $agent = $run->getAgentInstance();
        $state = $run->state();

        $step = app($this->stepClass);

        try {
            $newState = $step->handle($agent, $state);

            if (! $newState instanceof State) {
                $run->createLog('Step did not return a valid State instance', ['step' => $this->stepClass]);
                throw new RuntimeException('Step must return an instance of State');
            }

            // Update run state and status
            $run->state = method_exists($newState, 'getData') ? $newState->getData() : (array) $newState;
            if ($newState->isInterrupted()) {
                $interrupt = $newState->getInterrupt();
                $run->status = $interrupt ? $interrupt->value : 'interrupted';
                // Optionally store meta or handle specific interrupts
            }
            // If not interrupted, keep status as running (workflow decides completion)
            $run->save();
        } catch (\Throwable $e) {
            $run->status = 'failed';
            $run->createLog('Exception in RunStepJob', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $run->save();
            throw $e; // Let Laravel queue handle retries/failure
        }
    }
}
