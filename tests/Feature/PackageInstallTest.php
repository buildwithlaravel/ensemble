<?php

use BuildWithLaravel\Ensemble\EnsembleFacade;
use BuildWithLaravel\Ensemble\EnsembleServiceProvider;
use Orchestra\Testbench\TestCase;

uses(TestCase::class)
    ->beforeEach(function () {
        $this->app->register(EnsembleServiceProvider::class);
        $this->app->alias('Ensemble', EnsembleFacade::class);
        if (! class_exists('Ensemble')) {
            class_alias(\BuildWithLaravel\Ensemble\EnsembleFacade::class, 'Ensemble');
        }
    });

test('ensemble facade is resolvable', function () {
    expect(EnsembleFacade::getFacadeRoot())
        ->toBeInstanceOf(\BuildWithLaravel\Ensemble\Ensemble::class);
});

test('ensemble facade is resolvable via alias', function () {
    expect(\Ensemble::getFacadeRoot())
        ->toBeInstanceOf(\BuildWithLaravel\Ensemble\Ensemble::class);
});
