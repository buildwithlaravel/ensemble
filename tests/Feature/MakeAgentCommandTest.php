<?php

use BuildWithLaravel\Ensemble\EnsembleServiceProvider;
use Orchestra\Testbench\TestCase;

uses(TestCase::class)
    ->beforeEach(function () {
        // PREPARE: Register the EnsembleServiceProvider in the app container
    });

test('make:agent command creates an agent class file', function () {
    // PREPARE: Set workflow and agent names, ensure target directory and file do not exist
    // ACT: Run the 'make:agent' command with workflow and agent arguments
    // ASSERT: Output contains 'Agent class created', file exists, contents contain correct namespace and class, file and directory are cleaned up
});
