<?php

use Orchestra\Testbench\TestCase;
use BuildWithLaravel\Ensemble\EnsembleFacade;
use BuildWithLaravel\Ensemble\EnsembleServiceProvider;

uses(TestCase::class)
    ->beforeEach(function () {
        $this->app->register(EnsembleServiceProvider::class);
        $this->app->alias('Ensemble', EnsembleFacade::class);
    });

test('ensemble facade is resolvable', function () {
    expect(EnsembleFacade::getFacadeRoot())
        ->toBeInstanceOf(\BuildWithLaravel\Ensemble\Ensemble::class);
});

test('ensemble facade is resolvable via alias', function () {
    expect(\Ensemble::getFacadeRoot())
        ->toBeInstanceOf(\BuildWithLaravel\Ensemble\Ensemble::class);
}); 