<?php

namespace BuildWithLaravel\Ensemble\Tests\Unit\Core;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\GenericState;
use BuildWithLaravel\Ensemble\Models\Run;
use Error;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

// Create a concrete implementation of Agent for testing
class TestAgent extends Agent
{
    public function stateClass(): string
    {
        return GenericState::class;
    }
}

uses(RefreshDatabase::class);

test('it uses config defaults when no properties are set', function () {
    // TODO: Test that the agent uses config defaults when no properties are set
});

test('it uses agent properties over config defaults', function () {
    // TODO: Test that the agent uses its own properties over config defaults
});

test('withPrism executes the callback', function () {
    // TODO: Test that withPrism executes the callback
});

test('withPrism returns the agent instance', function () {
    // TODO: Test that withPrism returns the agent instance
});

test('extending Agent without implementing stateClass throws error', function () {
    // TODO: Test that extending Agent without implementing stateClass throws an error
});

describe('Agent defineArtifact', function () {
    it('returns the default status artifact if not overridden', function () {
        // TODO: Create a dummy Agent subclass that does not override defineArtifact
        // TODO: Create a mock Run and State
        // TODO: Call defineArtifact and assert the returned artifact is of type 'ensemble-status' and has expected data
    });

    it('returns a custom artifact if overridden in the Agent subclass', function () {
        // TODO: Create a dummy Agent subclass that overrides defineArtifact to return a custom artifact
        // TODO: Create a mock Run and State
        // TODO: Call defineArtifact and assert the returned artifact is the custom type
    });

    it('calls parent::defineArtifact for fallback in the Agent subclass', function () {
        // TODO: Create a dummy Agent subclass that calls parent::defineArtifact under certain conditions
        // TODO: Create a mock Run and State with the condition met
        // TODO: Call defineArtifact and assert the returned artifact is the default 'ensemble-status'
    });

    it('returns null if the Agent subclass returns null', function () {
        // TODO: Create a dummy Agent subclass that returns null from defineArtifact under certain conditions
        // TODO: Create a mock Run and State with the condition met
        // TODO: Call defineArtifact and assert the result is null
    });
});
