<?php

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\GenericState;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Models\Run;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

class DummyAgent extends Agent
{
    public function stateClass(): string
    {
        return GenericState::class;
    }

    public function steps(): array
    {
        return [new class
        {
            public function handle(Agent $agent, State $state): State
            {
                $state->set('foo', 'bar');

                return $state;
            }
        }];
    }
}

beforeEach(function () {
    // PREPARE: Ensure App\Agents\DummyAgent and App\Agents\NotAgent exist for tests
    if (! class_exists('App\\Agents\\DummyAgent')) {
        eval('namespace App\\Agents; class DummyAgent extends \\BuildWithLaravel\\Ensemble\\Core\\Agent { public function stateClass(): string { return \\BuildWithLaravel\\Ensemble\\Core\\GenericState::class; } public function steps(): array { return [new class { public function handle($agent, $state) { $state->set(\'foo\', \'bar\'); return $state; } }]; } }');
    }
});

test('ensemble:run basic creates run and outputs status/state', function () {
    // PREPARE: None needed
    // ACT: Run the 'ensemble:run' command with DummyAgent
    // ASSERT: Output contains 'Started run', 'finished with status: completed', 'foo'; run exists, status is completed, state contains 'foo' => 'bar'
});

test('ensemble:run with state JSON', function () {
    // PREPARE: None needed
    // ACT: Run the 'ensemble:run' command with DummyAgent and state JSON
    // ASSERT: Output contains 'foo'; run state contains 'bar' => 123
});

test('ensemble:run fails for invalid agent class', function () {
    // PREPARE: None needed
    // ACT: Run the 'ensemble:run' command with a non-existent agent class
    // ASSERT: Output contains 'does not exist'; exit code is 1
});

test('ensemble:run fails for agent not extending Agent', function () {
    // PREPARE: Ensure App\Agents\NotAgent exists and does not extend Agent
    // ACT: Run the 'ensemble:run' command with NotAgent
    // ASSERT: Output contains 'must extend Ensemble\\Core\\Agent'; exit code is 1
});

test('ensemble:run fails for invalid state JSON', function () {
    // PREPARE: None needed
    // ACT: Run the 'ensemble:run' command with invalid state JSON
    // ASSERT: Output contains 'Invalid JSON'; exit code is 1
});

test('ensemble:resume resumes paused run and outputs status/state', function () {
    // PREPARE: Create a paused run with DummyAgent and state 'foo' => 'baz'
    // ACT: Run the 'ensemble:resume' command with the run ID
    // ASSERT: Output contains 'Attempting to resume run', 'finished resumption with status: completed', 'foo'; run status is completed, state contains 'foo' => 'bar'
});

test('ensemble:resume fails for invalid run ID', function () {
    // PREPARE: None needed
    // ACT: Run the 'ensemble:resume' command with an invalid run ID
    // ASSERT: Output contains 'Invalid run ID format'; exit code is 1
});

test('ensemble:resume fails for non-existent run', function () {
    // PREPARE: Generate a random UUID
    // ACT: Run the 'ensemble:resume' command with the non-existent run ID
    // ASSERT: Output contains 'not found'; exit code is 1
});

test('ensemble:resume fails for invalid input JSON', function () {
    // PREPARE: Create a paused run with DummyAgent
    // ACT: Run the 'ensemble:resume' command with invalid input JSON
    // ASSERT: Output contains 'Invalid JSON'; exit code is 1
});
