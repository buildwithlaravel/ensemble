<?php

namespace BuildWithLaravel\Ensemble\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeToolCommand extends Command
{
    protected $signature = 'make:tool {workflow} {tool}';

    protected $description = 'Create a new Tool class for a workflow';

    public function handle()
    {
        $workflow = $this->argument('workflow');
        $tool = $this->argument('tool');
        $namespace = 'BuildWithLaravel\\Ensemble\\Workflows\\' . Str::studly($workflow);
        $class = Str::studly($tool);
        $dir = base_path('code/src/Workflows/' . Str::studly($workflow));
        $path = $dir . '/' . $class . '.php';

        (new Filesystem)->ensureDirectoryExists($dir);
        $stub = file_get_contents(base_path('code/stubs/ensemble/tool.stub'));
        $content = str_replace(['{{ namespace }}', '{{ class }}'], [$namespace, $class], $stub);
        file_put_contents($path, $content);

        $this->info("Tool class created: {$path}");
    }
}
