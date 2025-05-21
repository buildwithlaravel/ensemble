<?php

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\GenericState;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Facades\Ensemble as EnsembleFacade;
use BuildWithLaravel\Ensemble\Models\Run;
use BuildWithLaravel\Ensemble\Runtime\RunResumer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;

uses(RefreshDatabase::class);

class TestEnsembleAgent extends Agent
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

describe('Ensemble', function () {
    it('run creates a new run and executes workflow', function () {
        // TODO: Test that Ensemble::run creates a new run and executes the workflow
    });
    it('run sets runnable polymorphic fields if provided', function () {
        // TODO: Test that Ensemble::run sets the runnable polymorphic fields if provided
    });
    it('resume delegates to RunResumer and returns state', function () {
        // TODO: Test that Ensemble::resume delegates to RunResumer and returns the state
    });
    it('RunResumer::resume resumes a paused run and updates state', function () {
        // TODO: Test that RunResumer::resume resumes a paused run and updates the state
    });
    it('Run::resume calls RunResumer and returns state', function () {
        // TODO: Test that Run::resume calls RunResumer and returns the state
    });
});
