<?php

namespace BuildWithLaravel\Ensemble;

use BuildWithLaravel\Ensemble\Core\Workflow;
use BuildWithLaravel\Ensemble\Models\Run;
use BuildWithLaravel\Ensemble\Runtime\RunResumer;
use BuildWithLaravel\Ensemble\Support\Traits\ResolvesEnsembleModels;
use Illuminate\Database\Eloquent\Model;

class Ensemble
{
    use ResolvesEnsembleModels;

    protected RunResumer $runResumer;

    public function __construct(RunResumer $runResumer)
    {
        $this->runResumer = $runResumer;
    }

    /**
     * Start a new agent run.
     */
    public function run(string $agentClass, ?Model $runnable = null, array $initialState = []): Run
    {
        /** @var Run $run */
        $run = self::newModel('run', Run::class);
        $run->agent = $agentClass;
        if ($runnable) {
            $run->runnable()->associate($runnable);
        }
        $run->state = $initialState;
        $run->status = 'running';
        $run->current_step_index = 0;
        $run->save();
        $agent = $run->getAgentInstance();
        $workflow = new Workflow($agent);
        $state = $run->state;
        $workflow->process($state);
        $run->refresh();

        return $run;
    }

    /**
     * Resume an interrupted run.
     */
    public function resume(Run $run, array $inputData): Run
    {
        return $this->runResumer->resume($run, $inputData);
    }
}
