<?php

namespace BuildWithLaravel\Ensemble\Tests\Unit\Jobs;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Core\Step;
use BuildWithLaravel\Ensemble\Enums\InterruptType;
use BuildWithLaravel\Ensemble\Jobs\RunStepJob;
use BuildWithLaravel\Ensemble\Models\Run;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


// Minimal test agent for use in these tests
class TestAgent extends Agent
{
}

test('job executes step and updates run state and status', function () {
    // Create a sample Run
    $run = Run::create([
        'id' => 'abc-123',
        'agent_class' => TestAgent::class,
        'state' => [],
        'status' => 'running',
    ]);


    // Bind the step class in the container
    $stepClass = 'App\Agents\TestStep';
    app()->bind($stepClass, fn () => new class extends Step {
        public function handle(Agent $agent, State $state): State
        {
            $state->set('foo', 'bar');
            return $state;
        }
    });

    $job = new RunStepJob($run->id, $stepClass);
    $job->handle();

    $run->refresh();
    expect($run->state)->toBe(['foo' => 'bar']);
    expect($run->status)->toBe('running');
});

test('job handles step interrupt and updates run status', function () {
    $run = Run::create([
        'id' => 'abc-456',
        'agent_class' => TestAgent::class,
        'state' => [],
        'status' => 'running',
    ]);

    $stepClass = 'App\Agents\TestStep';
    app()->bind($stepClass, fn () => new class extends Step {
        public function handle(Agent $agent, State $state): State
        {
            $state = new class([]) extends State {
                public function getData(): array
                {
                    return ['foo' => 'bar'];
                }
            };
            $state->waitForHuman('test_human_interrupt', 'This is the message to the human');
            return $state;
        }
    });

    $job = new RunStepJob($run->id, $stepClass);
    $job->handle();

    $run->refresh();
    expect($run->state)->toBe(['foo' => 'bar']);
    expect($run->status)->toBe(InterruptType::WaitHuman->value);
});

test('job logs and marks run as failed on exception', function () {
    $run = Run::create([
        'id' => 'abc-789',
        'agent_class' => TestAgent::class,
        'state' => [],
        'status' => 'running',
    ]);

    $stepClass = 'App\Agents\TestStep';
    app()->bind($stepClass, fn () => new class extends Step {
        public function handle(Agent $agent, State $state): State
        {
            throw new Exception('Step failed');
        }
    });

    expect(fn () => (new RunStepJob($run->id, $stepClass))->handle())->toThrow(Exception::class);
    $run->refresh();
    expect($run->status)->toBe('failed');
});

