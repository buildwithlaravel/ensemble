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

//TODO: review workflow tests

// Concrete agent for use in parallel step tests
class TestWorkflowAgent extends Agent
{
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

test('workflow executes steps in sequence', function () {
    // Mock dependencies
    $agent = Mockery::mock(Agent::class);
    $run = Mockery::mock(Run::class);
    $agent->shouldReceive('run')->andReturn($run);
    $run->shouldReceive('update');

    $steps = [
        new TestStep,
        new TestStep,
    ];

    $workflow = new Workflow($agent, $steps);
    $state = Mockery::mock(State::class);

    $state->shouldReceive('set')
        ->twice()
        ->with('test', 'value')
        ->andReturnSelf();

    $state->shouldReceive('isInterrupted')
        ->twice()
        ->andReturn(false);

    $result = $workflow->run($state);

    expect($result)->toBe($state);
});

test('workflow resolves step class names to instances', function () {
    $agent = Mockery::mock(Agent::class);
    $run = Mockery::mock(Run::class);
    $agent->shouldReceive('run')->andReturn($run);
    $run->shouldReceive('update');

    $steps = [
        TestStep::class,
    ];

    $workflow = new Workflow($agent, $steps);
    $state = Mockery::mock(State::class);

    $state->shouldReceive('set')
        ->once()
        ->with('test', 'value')
        ->andReturnSelf();

    $state->shouldReceive('isInterrupted')
        ->once()
        ->andReturn(false);

    $result = $workflow->run($state);

    expect($result)->toBe($state);
});

test('workflow propagates state between steps', function () {
    $agent = Mockery::mock(Agent::class);
    $run = Mockery::mock(Run::class);
    $agent->shouldReceive('run')->andReturn($run);
    $run->shouldReceive('update');

    $steps = [
        new TestStep,
        new class {
            public function handle(Agent $agent, State $state): State
            {
                expect($state->get('test'))->toBe('value');

                return $state;
            }
        },
    ];

    $workflow = new Workflow($agent, $steps);
    $state = Mockery::mock(State::class);

    $state->shouldReceive('set')
        ->once()
        ->with('test', 'value')
        ->andReturnSelf();

    $state->shouldReceive('get')
        ->once()
        ->with('test')
        ->andReturn('value');

    $state->shouldReceive('isInterrupted')
        ->twice()
        ->andReturn(false);

    $workflow->run($state);
});

test('workflow stops on interrupt and returns interrupted state', function () {
    $agent = Mockery::mock(Agent::class);
    $run = Mockery::mock(Run::class);
    $agent->shouldReceive('run')->andReturn($run);
    $run->shouldReceive('update');

    $steps = [
        new InterruptStep,
        new TestStep, // Should not be executed
    ];

    $workflow = new Workflow($agent, $steps);
    $state = Mockery::mock(State::class);

    $state->shouldReceive('halt')
        ->once()
        ->with('Test interrupt')
        ->andReturnSelf();

    $state->shouldReceive('isInterrupted')
        ->once()
        ->andReturn(true);

    $result = $workflow->run($state);

    expect($result)->toBe($state);
});

test('workflow starts from specified step index', function () {
    $agent = Mockery::mock(Agent::class);
    $run = Mockery::mock(Run::class);
    $agent->shouldReceive('run')->andReturn($run);
    $run->shouldReceive('update');

    $steps = [
        new TestStep, // Should be skipped
        new TestStep,
    ];

    $workflow = new Workflow($agent, $steps);
    $workflow->setStartStep(1);

    $state = Mockery::mock(State::class);

    $state->shouldReceive('set')
        ->once() // Only the second step should be executed
        ->with('test', 'value')
        ->andReturnSelf();

    $state->shouldReceive('isInterrupted')
        ->once()
        ->andReturn(false);

    $result = $workflow->run($state);

    expect($result)->toBe($state);
});

test('workflow throws exception for invalid step return', function () {
    $agent = Mockery::mock(Agent::class);
    $run = Mockery::mock(Run::class);
    $agent->shouldReceive('run')->andReturn($run);
    $run->shouldReceive('update');

    $steps = [
        new InvalidStep,
    ];

    $workflow = new Workflow($agent, $steps);
    $state = Mockery::mock(State::class);

    expect(fn () => $workflow->run($state))
        ->toThrow(RuntimeException::class);
});

test('workflow executes parallel steps using job batch', function () {
    // Use a real Run model
    $run = \BuildWithLaravel\Ensemble\Models\Run::create([
        'id' => 1,
        'agent_class' => TestWorkflowAgent::class,
        'state' => [],
        'status' => 'running',
    ]);
    $agent = Mockery::mock(Agent::class);
    $agent->shouldReceive('run')->andReturn($run);

    $parallelSteps = [
        new TestStep,
        new TestStep,
    ];

    $steps = [
        new Parallel($parallelSteps),
    ];

    $workflow = new Workflow($agent, $steps);
    $state = new GenericState([]);

    // Mock Bus facade and batch
    $batch = Mockery::mock(Batch::class);
    $batch->shouldReceive('allowFailures')->andReturnSelf();
    $batch->shouldReceive('dispatch')->andReturnSelf();
    $batch->shouldReceive('waitUntilFinished');
    $batch->shouldReceive('hasFailures')->andReturn(false);

    Bus::shouldReceive('batch')
        ->once()
        ->withArgs(function ($jobs) {
            return count($jobs) === 2 &&
                $jobs[0] instanceof RunStepJob &&
                $jobs[1] instanceof RunStepJob;
        })
        ->andReturn($batch);

    $result = $workflow->run($state);

    expect($result)->toBeInstanceOf(State::class);
});

test('workflow handles parallel step failures', function () {
    // Use a real Run model
    $run = \BuildWithLaravel\Ensemble\Models\Run::create([
        'id' => 2,
        'agent_class' => TestWorkflowAgent::class,
        'state' => [],
        'status' => 'running',
    ]);
    $agent = Mockery::mock(Agent::class);
    $agent->shouldReceive('run')->andReturn($run);

    $state = new GenericState([]);
    $run->state = $state;
    $run->save();

    $parallelSteps = [
        new TestStep,
        new TestStep,
    ];

    $steps = [
        new Parallel($parallelSteps),
    ];

    $workflow = new Workflow($agent, $steps);

    // Mock Bus facade and batch with failures
    $batch = Mockery::mock(Batch::class);
    $batch->shouldReceive('allowFailures')->andReturnSelf();
    $batch->shouldReceive('dispatch')->andReturnSelf();
    $batch->shouldReceive('waitUntilFinished');
    $batch->shouldReceive('hasFailures')->andReturn(true);

    Bus::shouldReceive('batch')->andReturn($batch);

    $result = $workflow->run($state);

    expect($result)->toBeInstanceOf(State::class)
        ->and($result->isInterrupted())->toBeTrue();
});
