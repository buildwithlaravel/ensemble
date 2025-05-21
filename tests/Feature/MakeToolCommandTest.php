<?php

use BuildWithLaravel\Ensemble\EnsembleServiceProvider;
use Orchestra\Testbench\TestCase;

uses(TestCase::class)
    ->beforeEach(function () {
        // PREPARE: Register the EnsembleServiceProvider in the app container
    });

test('make:tool command creates a tool class file', function () {
    // PREPARE: Set workflow and tool names, ensure target directory and file do not exist
    // ACT: Run the 'make:tool' command with workflow and tool arguments
    // ASSERT: Output contains 'Tool class created', file exists, contents contain correct namespace and class, file and directory are cleaned up
});
