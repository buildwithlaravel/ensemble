<?php

namespace BuildWithLaravel\Ensemble\Core;

use BuildWithLaravel\Ensemble\Enums\EventType;
use BuildWithLaravel\Ensemble\Enums\InterruptType;
use BuildWithLaravel\Ensemble\Enums\RunStatus;
use BuildWithLaravel\Ensemble\Jobs\RunStepJob;
use BuildWithLaravel\Ensemble\Models\Run;
use BuildWithLaravel\Ensemble\Support\Parallel;
use Closure;
use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class Workflow
{
    protected Agent $agent;
    protected Run $run;
    protected int $currentIndex = 0;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function setStartStep(int $index): void
    {
        $this->currentIndex = $index;
    }

    public function process(State $state): State
    {
        try {
            $this->run = $this->agent->getRun();

            if (!$this->run->current_step_index) {
                $this->run->event(EventType::RunStarted, ['status' => $this->run->status, 'state' => $this->run->state]);
            }

            collect($this->agent->steps())
                ->skip($this->currentIndex)
                ->reduce(function ($previousState, $step) {
                    if ($previousState->isInterrupted()) {
                        return $previousState;
                    }

                    $state = $this->processStep($step, $previousState);

                    if (!$state->isInterrupted()) {
                        $this->run->current_step_index += 1;
                    }
                    $this->run->state = $state->all();
                    $this->run->save();

                    return $state;
                }, $state);

            $this->run->refresh();
            $currentState = $this->run->state;

            if ($currentState->isInterrupted()) {
                if ($currentState->getInterrupt() == InterruptType::Error) {
                    throw new \Error($currentState->interruptData['reason']);
                }
                $this->run->status = RunStatus::Interrupted;
                $this->run->save();
                return $currentState;
            }

            $this->run->event(EventType::RunFinished, ['status' => $this->run->status, 'state' => $this->run->state]);
            $this->run->status = RunStatus::Completed;
            $this->run->save();
            return $currentState;

        } catch (Throwable $exception) {
            $this->run->refresh();
            Log::error($exception);
            $this->run->event(EventType::RunError, [
                'status' => $this->run->status, 'state' => $this->run->state,
                'exception' => $exception
            ]);
            $this->run->status = RunStatus::Error;
            $this->run->save();
            return $this->run->state;
        }
    }

    protected function processStep(Step|Parallel|Closure|string $stepDefinition, State $previousState): State
    {
        $step = $this->resolveStep($stepDefinition, $previousState);
        $this->run->event(EventType::StepStarted, ['step' => $step, 'status' => $this->run->status, 'state' => $this->run->state]);

        if ($step instanceof Step) {
            $state = $this->executeStep($step, $previousState);
        } elseif ($step instanceof Parallel) {
            $state = $this->executeParallelStep($step, $previousState);
        } else {
            throw new Exception("Invalid step type: " . get_class($step));
        }

        return $state;
    }

    protected function resolveStep(Parallel|Step|string|Closure $step, State $state): Step|Parallel
    {
        if ($step instanceof Step || $step instanceof Parallel) {
            return $step;
        }

        if (is_string($step)) {
            return app($step);
        }

        if ($step instanceof Closure) {
            return $step($state);
        }

        throw new Exception('Unsupported step type');
    }

    protected function executeStep(Step $step, State $previousState): State
    {
        if ($step instanceof ShouldQueue) {
            $job = new RunStepJob($this->run, $step, resumeAfter: true);
            dispatch($job);

            return $this->run->state->waitForQueued('Step:' . get_class($step));
        }

        $state = $step->handle($this->agent, $previousState);

        if (!$state->isInterrupted()) {
            $this->run->event(EventType::StepFinished, [
                'step' => $step,
                'status' => $this->run->status,
                'state' => $this->run->state]);
        }

        $this->run->event(EventType::StateSnapshot, ['status' => $this->run->status, 'state' => $state]);
        return $state;
    }

    protected function executeParallelStep(Parallel $parallel, State $previousState): State
    {
        $batch = Bus::batch(collect($parallel->steps)
            ->map(fn ($stepDefinition) => new RunStepJob($this->run, $this->resolveStep($stepDefinition, $previousState)))
        )
            ->name('Parallel Steps: ' . $this->run->id)
            ->allowFailures()
            ->finally(function (Batch $batch) use ($parallel) {
                $this->run->refresh();
                $state = $this->run->state;
                ray([
                    'status' => $this->run->status,
                    'index' => $this->run->current_step_index,
                    'interrupt' => $state->getInterrupt(),
                    'parallel-result' => $state->toArray(),
                    'batch' => $batch->toArray()
                ]);

                if (!$state->isInterrupted() || $state->getInterrupt() == InterruptType::WaitForQueue) {
                    // Progress to next step
                    $this->run->current_step_index += 1;
                    $this->run->save();
                    $this->run->resume([]);
                }
            })
            ->dispatch();

        return $previousState->waitForQueued($batch->name);
    }
}