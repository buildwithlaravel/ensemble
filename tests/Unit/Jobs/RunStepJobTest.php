<?php

namespace BuildWithLaravel\Ensemble\Tests\Unit\Jobs;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Core\Step;
use BuildWithLaravel\Ensemble\Jobs\RunStepJob;
use BuildWithLaravel\Ensemble\Models\Run;
use BuildWithLaravel\Ensemble\Tests\TestCase;
use Mockery;

test('job executes step and updates run state', function () {
    // Create mock step
    $step = Mockery::mock(Step::class);
    
    // Create mock run and its dependencies
    $run = Mockery::mock(Run::class);
    $agent = Mockery::mock(Agent::class);
    $state = Mockery::mock(State::class);
    $resultState = Mockery::mock(State::class);
    
    // Setup run mock
    Run::shouldReceive('findOrFail')
        ->once()
        ->with(1)
        ->andReturn($run);
    
    $run->agent = $agent;
    
    $run->shouldReceive('state')
        ->once()
        ->andReturn($state);
    
    $run->shouldReceive('update')
        ->once()
        ->with(['state' => $resultState]);
    
    // Setup step mock
    $step->shouldReceive('handle')
        ->once()
        ->with($agent, $state)
        ->andReturn($resultState);
    
    // Create and execute job
    $job = new RunStepJob(1, $step);
    $job->handle();
}); 