<?php

use BuildWithLaravel\Ensemble\EnsembleServiceProvider;
use BuildWithLaravel\Ensemble\Runtime\Handlers\HaltHandler;
use BuildWithLaravel\Ensemble\Runtime\InterruptHandler;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->app->register(EnsembleServiceProvider::class);
});

test('config is published to config directory', function () {
    $publishedPath = config_path('ensemble.php');
    @unlink($publishedPath);
    Artisan::call('vendor:publish', ['--tag' => 'ensemble-config', '--force' => true]);
    expect(file_exists($publishedPath))->toBeTrue();
    $contents = file_get_contents($publishedPath);
    expect($contents)->toContain('interrupt_handlers');
});

test('migrations are published to database/migrations', function () {
    $publishedDir = database_path('migrations');
    Artisan::call('vendor:publish', ['--tag' => 'ensemble-migrations', '--force' => true]);
    expect(is_dir($publishedDir))->toBeTrue();
});

test('service bindings resolve from container', function () {
    expect(app('ensemble'))->toBeInstanceOf(\BuildWithLaravel\Ensemble\Ensemble::class);
    expect(app(\BuildWithLaravel\Ensemble\Ensemble::class))->toBeInstanceOf(\BuildWithLaravel\Ensemble\Ensemble::class);
    expect(app(\BuildWithLaravel\Ensemble\Runtime\InterruptHandler::class))->toBeInstanceOf(\BuildWithLaravel\Ensemble\Runtime\InterruptHandler::class);
    expect(app(\BuildWithLaravel\Ensemble\Runtime\RunResumer::class))->toBeInstanceOf(\BuildWithLaravel\Ensemble\Runtime\RunResumer::class);
});

test('interrupt handlers are registered from config', function () {
    // TODO: Test that interrupt handlers are registered from the config and contain HaltHandler::class
});

test('config merging works for ensemble.defaults.llm.provider', function () {
    $provider = config('ensemble.defaults.llm.provider');
    expect($provider)->not()->toBeNull();
});
