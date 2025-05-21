<?php

namespace BuildWithLaravel\Ensemble\Tests\Unit\Core;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\GenericState;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Core\Workflow;
use BuildWithLaravel\Ensemble\Jobs\RunStepJob;
use BuildWithLaravel\Ensemble\Models\Run;
use BuildWithLaravel\Ensemble\Support\Parallel;
use Illuminate\Bus\Batch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Mockery;
use RuntimeException;

uses(RefreshDatabase::class);

// TODO: review workflow tests

// Concrete agent for use in parallel step tests
class TestWorkflowAgent extends Agent
{
    public function stateClass(): string
    {
        return GenericState::class;
    }
}

// Test step class that modifies state
class TestStep
{
    public function handle(Agent $agent, State $state): State
    {
        return $state->set('test', 'value');
    }
}

// Test step class that returns an interrupt
class InterruptStep
{
    public function handle(Agent $agent, State $state): State
    {
        return $state->halt('Test interrupt');
    }
}

// Test step class that returns invalid value
class InvalidStep
{
    public function handle(Agent $agent, State $state): string
    {
        return 'invalid';
    }
}

// Concrete agent with steps for integration tests
class IntegrationTestAgent extends Agent
{
    protected array $testSteps;

    public function __construct(Run $run, array $steps)
    {
        $this->run = $run;
        $this->testSteps = $steps;
    }

    public function stateClass(): string
    {
        return GenericState::class;
    }

    public function steps(): array
    {
        return $this->testSteps;
    }
}

describe('Workflow', function () {
    it('executes steps in sequence', function () {
        // TODO: Test that the workflow executes steps in the correct order
    });
    it('resolves step class names to instances', function () {
        // TODO: Test that the workflow resolves step class names to the correct instances
    });
    it('propagates state between steps', function () {
        // TODO: Test that the workflow propagates state correctly between steps
    });
    it('throws exception for invalid step return', function () {
        // TODO: Test that the workflow throws an exception for invalid step return values
    });
    it('executes parallel steps using job batch', function () {
        // TODO: Test that the workflow executes parallel steps using job batches
    });
    it('handles parallel step failures', function () {
        // TODO: Test that the workflow handles failures in parallel steps
    });
    it('sets status to completed after all steps', function () {
        // TODO: Test that the workflow sets the status to completed after all steps
    });
    it('updates last_ran_at and current_step_index after each step', function () {
        // TODO: Test that the workflow updates last_ran_at and current_step_index after each step
    });
    it('logs step completion, interrupts, and failures', function () {
        // TODO: Test that the workflow logs step completion, interrupts, and failures
    });
    it('resumes from specified step index', function () {
        // TODO: Test that the workflow resumes from the specified step index
    });
});
