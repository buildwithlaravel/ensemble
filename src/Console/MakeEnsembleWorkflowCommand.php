<?php

namespace BuildWithLaravel\Ensemble\Console;

use Illuminate\Console\Command;

class MakeEnsembleWorkflowCommand extends Command
{
    protected $signature = 'make:ensemble-workflow';

    protected $description = 'Interactive wizard to create a workflow with agents, steps, and tools';

    public function handle()
    {
        $workflow = $this->ask('What is the workflow name?');

        // Agents
        $agents = [];
        if ($this->confirm('Would you like to add agents to this workflow?', true)) {
            do {
                $agent = $this->ask('Enter agent name (or leave blank to finish)');
                if ($agent) {
                    $agents[] = $agent;
                }
            } while ($agent);
        }

        // Steps
        $steps = [];
        if ($this->confirm('Would you like to add steps to this workflow?', true)) {
            do {
                $step = $this->ask('Enter step name (or leave blank to finish)');
                if ($step) {
                    $steps[] = $step;
                }
            } while ($step);
        }

        // Tools
        $tools = [];
        if ($this->confirm('Would you like to add tools to this workflow?', true)) {
            do {
                $tool = $this->ask('Enter tool name (or leave blank to finish)');
                if ($tool) {
                    $tools[] = $tool;
                }
            } while ($tool);
        }

        // Generate agents
        foreach ($agents as $agent) {
            $this->call('make:agent', [
                'workflow' => $workflow,
                'agent' => $agent,
            ]);
        }

        // Generate steps
        foreach ($steps as $step) {
            $this->call('make:step', [
                'workflow' => $workflow,
                'step' => $step,
            ]);
        }

        // Generate tools
        foreach ($tools as $tool) {
            $this->call('make:tool', [
                'workflow' => $workflow,
                'tool' => $tool,
            ]);
        }

        $this->info('Workflow scaffolding complete!');
    }
}
