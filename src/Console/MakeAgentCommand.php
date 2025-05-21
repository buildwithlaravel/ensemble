<?php

namespace BuildWithLaravel\Ensemble\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeAgentCommand extends Command
{
    protected $signature = 'make:agent {workflow} {agent}';

    protected $description = 'Create a new Agent class for a workflow';

    public function handle()
    {
        $workflow = $this->argument('workflow');
        $agent = $this->argument('agent');
        $namespace = 'BuildWithLaravel\\Ensemble\\Workflows\\'.Str::studly($workflow);
        $class = Str::studly($agent);
        $dir = base_path('code/src/Workflows/'.Str::studly($workflow));
        $path = $dir.'/'.$class.'.php';

        (new Filesystem)->ensureDirectoryExists($dir);
        $stub = file_get_contents(base_path('code/stubs/ensemble/agent.stub'));
        $content = str_replace(['{{ namespace }}', '{{ class }}'], [$namespace, $class], $stub);
        file_put_contents($path, $content);

        $this->info("Agent class created: {$path}");
    }
}
