<?php

namespace BuildWithLaravel\Ensemble\Jobs;

use BuildWithLaravel\Ensemble\Core\Step;
use BuildWithLaravel\Ensemble\Enums\EventType;
use BuildWithLaravel\Ensemble\Enums\InterruptType;
use BuildWithLaravel\Ensemble\Models\Run;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class RunStepJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public $timeout = 300;

    public function __construct(protected Run $run, protected Step $step, protected bool $resumeAfter = false)
    {
    }


    public function handle(): void
    {
        $state = $this->step->handle($this->run->getAgentInstance(), $this->run->state);
        $this->run->event(EventType::StateSnapshot, ['status' => $this->run->status, 'state' => $state]);
        $this->run->state = $state->all();
        $this->run->save();
        if (!$state->isInterrupted() || $state->getInterrupt() == InterruptType::WaitForQueue) {
            $this->run->event(EventType::StepFinished, [
                'step' => $this->step,
                'status' => $this->run->status,
                'state' => $state
            ]);

            if ($this->resumeAfter) {
                $this->run->current_step_index += 1;
                $this->run->resume([]);
            }
        }
    }

    public function middleware()
    {
        return [
            new SkipIfBatchCancelled
        ];
    }
}