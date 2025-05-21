<?php

namespace BuildWithLaravel\Ensemble\Runtime;

use BuildWithLaravel\Ensemble\Core\Workflow;
use BuildWithLaravel\Ensemble\Enums\EventType;
use BuildWithLaravel\Ensemble\Enums\RunStatus;
use BuildWithLaravel\Ensemble\Models\Run;

class RunResumer
{
    public function resume(Run $run, array $inputData): Run
    {
        $state = $run->state;
        $state->fill($inputData);
        $state->resetInterrupt();
        $run->state = $state;
        $run->status = RunStatus::Running;
        $run->save();

        $run->event(EventType::StatusUpdate, [
            'message' => 'run_resumed',
            'index' => $run->current_step_index,
            'input' => $inputData
        ]);

        $workflow = new Workflow($run->getAgentInstance());
        $workflow->setStartStep($run->current_step_index);
        $finalState = $workflow->process($state);

        $run->refresh();
        $run->state = $finalState->all();
        $run->save();

        return $run;
    }
}
