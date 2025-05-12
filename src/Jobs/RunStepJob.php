<?php

namespace BuildWithLaravel\Ensemble\Jobs;

use BuildWithLaravel\Ensemble\Core\Step;
use BuildWithLaravel\Ensemble\Models\Run;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunStepJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $runId,
        public Step $step
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get the run and its current state
        $run = Run::findOrFail($this->runId);
        $state = $run->state();

        // Execute the step
        $resultState = $this->step->handle($run->agent, $state);

        // Update the run's state
        $run->update(['state' => $resultState]);
    }
} 