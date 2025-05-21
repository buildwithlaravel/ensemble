<?php

namespace BuildWithLaravel\Ensemble\Console;

use BuildWithLaravel\Ensemble\Facades\Ensemble;
use BuildWithLaravel\Ensemble\Models\Run;
use Illuminate\Console\Command;

class ResumeAgent extends Command
{
    protected $signature = 'ensemble:resume {runId : The UUID of the run to resume} {--input= : Input data as JSON to merge into the state}';

    protected $description = 'Resumes an interrupted agent run';

    public function handle(): int
    {
        $runId = $this->argument('runId');
        $inputJson = $this->option('input');

        if (! preg_match('/^[a-f0-9-]{36}$/i', $runId)) {
            $this->error('Invalid run ID format. Must be a UUID.');

            return Command::FAILURE;
        }
        $run = Run::find($runId);
        if (! $run) {
            $this->error("Run [{$runId}] not found.");

            return Command::FAILURE;
        }
        $inputData = [];
        if ($inputJson) {
            $inputData = json_decode($inputJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON provided for input data: '.json_last_error_msg());

                return Command::FAILURE;
            }
        }
        $this->info("Attempting to resume run [{$runId}].");
        $finalState = Ensemble::resume($runId, $inputData);
        $run->refresh();
        $this->info("Run [{$runId}] finished resumption with status: ".($run->status ?? 'completed'));
        $this->line('Final State: '.json_encode($run->state, JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
