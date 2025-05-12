<?php

namespace BuildWithLaravel\Ensemble\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            \BuildWithLaravel\Ensemble\EnsembleServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Define environment setup
        $app['config']->set('ensemble.defaults.llm.provider', 'openai');
        $app['config']->set('ensemble.defaults.llm.model', 'gpt-4');
    }
}
