<?php

namespace BuildWithLaravel\Ensemble\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeStepCommand extends Command
{
    protected $signature = 'make:step {workflow} {step}';

    protected $description = 'Create a new Step class for a workflow';

    public function handle()
    {
        $workflow = $this->argument('workflow');
        $step = $this->argument('step');
        $namespace = 'BuildWithLaravel\\Ensemble\\Workflows\\'.Str::studly($workflow);
        $class = Str::studly($step);
        $dir = base_path('code/src/Workflows/'.Str::studly($workflow));
        $path = $dir.'/'.$class.'.php';

        (new Filesystem)->ensureDirectoryExists($dir);
        $stub = file_get_contents(base_path('code/stubs/ensemble/step.stub'));
        $content = str_replace(['{{ namespace }}', '{{ class }}'], [$namespace, $class], $stub);
        file_put_contents($path, $content);

        $this->info("Step class created: {$path}");
    }
}
