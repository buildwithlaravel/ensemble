<?php

use BuildWithLaravel\Ensemble\EnsembleServiceProvider;
use BuildWithLaravel\Ensemble\Runtime\InterruptHandler;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    // PREPARE: Register the EnsembleServiceProvider in the app container
});

test('ensemble config is merged and accessible', function () {
    // PREPARE: None needed
    // ACT: Access config values
    // ASSERT: Config values for ensemble.defaults.llm.provider and ensemble.models.run are correct
});

test('ensemble config can be published', function () {
    // PREPARE: None needed
    // ACT: Call the vendor:publish command for ensemble-config
    // ASSERT: File::copy is called; config file is published
});

test('ensemble migrations can be published', function () {
    // PREPARE: None needed
    // ACT: Call the vendor:publish command for ensemble-migrations
    // ASSERT: File::copyDirectory is called; migrations are published
});

test('core services are bound in the container', function () {
    // PREPARE: None needed
    // ACT: Resolve services from the container
    // ASSERT: Services are instances of the expected classes
});

test('interrupt handlers from config are registered', function () {
    // PREPARE: Set config for interrupt handlers and mock InterruptHandler
    // ACT: Boot the EnsembleServiceProvider
    // ASSERT: InterruptHandler::register is called with the correct handler class
});
