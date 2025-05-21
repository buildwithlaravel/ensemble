<?php

namespace BuildWithLaravel\Ensemble\Tests;

use BuildWithLaravel\Ensemble\EnsembleServiceProvider;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

#[WithMigration]
abstract class TestCase extends Orchestra
{
    use WithWorkbench;

    protected function getPackageProviders($app)
    {
        return [
            EnsembleServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Define environment setup
        $app['config']->set('ensemble.defaults.llm.provider', 'openai');
        $app['config']->set('ensemble.defaults.llm.model', 'gpt-4');
    }
}
