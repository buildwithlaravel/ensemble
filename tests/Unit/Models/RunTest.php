<?php

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Models\Run;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Run::getAgentInstance returns correct agent instance', function () {
    $run = new Run([
        'agent' => TestRunAgent::class,
    ]);
    $agent = $run->getAgentInstance();
    expect($agent)->toBeInstanceOf(TestRunAgent::class)
        ->and($agent)->toBeInstanceOf(Agent::class);
    expect($agent->getRun())->toBe($run); // Use getter method
});

test('Run::getAgentInstance throws if agent is missing', function () {
    $run = new Run(['agent' => null]);
    expect(fn () => $run->getAgentInstance())->toThrow(RuntimeException::class);
});

test('Run::getAgentInstance throws if agent is not a subclass', function () {
    $run = new Run(['agent' => stdClass::class]);
    expect(fn () => $run->getAgentInstance())->toThrow(RuntimeException::class);
});

test('Run::state() instantiates correct State DTO and hydrates from JSON', function () {
    // TODO: Test that Run::state() instantiates the correct State DTO and hydrates from JSON
});

class TestRunAgent extends Agent
{
    public function __construct(Run $run)
    {
        $this->run = $run;
    }

    public function stateClass(): string
    {
        return \BuildWithLaravel\Ensemble\Core\GenericState::class;
    }
}

describe('Run::currentArtifact', function () {
    it('returns the default status artifact when agent does not override defineArtifact', function () {
        // TODO: Mock a Run with a dummy Agent that does not override defineArtifact
        // TODO: Mock logs and state
        // TODO: Call currentArtifact and assert the returned artifact is of type 'ensemble-status' and has expected data
    });

    it('returns a custom artifact when agent overrides defineArtifact', function () {
        // TODO: Mock a Run with a dummy Agent that overrides defineArtifact to return a custom artifact
        // TODO: Mock logs and state
        // TODO: Call currentArtifact and assert the returned artifact is the custom type
    });

    it('returns the default artifact when agent override calls parent', function () {
        // TODO: Mock a Run with a dummy Agent that calls parent::defineArtifact under certain conditions
        // TODO: Mock logs and state with the condition met
        // TODO: Call currentArtifact and assert the returned artifact is of type 'ensemble-status'
    });

    it('returns null when agent override returns null', function () {
        // TODO: Mock a Run with a dummy Agent that returns null from defineArtifact under certain conditions
        // TODO: Mock logs and state with the condition met
        // TODO: Call currentArtifact and assert the result is null
    });
});
