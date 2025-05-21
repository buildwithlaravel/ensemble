<?php

namespace BuildWithLaravel\Ensemble\Console;

use BuildWithLaravel\Ensemble\Facades\Ensemble;
use BuildWithLaravel\Ensemble\Models\Run;
use Illuminate\Console\Command;

class RunAgent extends Command
{
    protected $signature = 'ensemble:run {agentClass : The fully qualified class name of the agent to run} {--runnable= : The ID of the runnable model (e.g., User ID)} {--runnable-type= : The class name of the runnable model} {--state= : Initial state data as JSON}';

    protected $description = 'Starts a new agent run';

    public function handle(): int
    {
        $agentClass = $this->argument('agentClass');
        $runnableId = $this->option('runnable');
        $runnableType = $this->option('runnable-type');
        $initialStateJson = $this->option('state');

        if (! class_exists($agentClass)) {
            $this->error("Agent class [{$agentClass}] does not exist.");

            return Command::FAILURE;
        }
        if (! is_subclass_of($agentClass, \BuildWithLaravel\Ensemble\Core\Agent::class)) {
            $this->error("Agent class [{$agentClass}] must extend Ensemble\\Core\\Agent.");

            return Command::FAILURE;
        }

        $runnable = null;
        if ($runnableId) {
            if (! $runnableType) {
                $this->error('You must provide --runnable-type when using --runnable.');

                return Command::FAILURE;
            }
            if (! class_exists($runnableType)) {
                $this->error("Runnable type [{$runnableType}] does not exist.");

                return Command::FAILURE;
            }
            $runnable = $runnableType::find($runnableId);
            if (! $runnable) {
                $this->error("Runnable model [{$runnableType}:{$runnableId}] not found.");

                return Command::FAILURE;
            }
        }

        $initialStateData = [];
        if ($initialStateJson) {
            $initialStateData = json_decode($initialStateJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON provided for initial state: '.json_last_error_msg());

                return Command::FAILURE;
            }
        }

        $run = new Run;
        $run->agent = $agentClass;
        if ($runnable) {
            $run->runnable_id = $runnable->getKey();
            $run->runnable_type = $runnable->getMorphClass();
        }
        $run->state = $initialStateData;
        $run->status = 'running';
        $run->current_step_index = 0;
        $run->save();

        $this->info("Started run [{$run->id}] for agent: {$agentClass}.");
        $finalState = Ensemble::run($agentClass, $runnable, $initialStateData);
        $run->refresh();
        $this->info("Run [{$run->id}] finished with status: ".($run->status ?? 'completed'));
        $this->line('Final State: '.json_encode($run->state, JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
