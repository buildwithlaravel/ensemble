<?php

use BuildWithLaravel\Ensemble\Agents\Steps\ProcessCurationStep;
use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Models\Run;

describe('ProcessCurationStep', function () {
    beforeEach(function () {
        $this->step = new ProcessCurationStep();
        $this->agent = mock(Agent::class);
        $this->run = mock(Run::class);
        $this->state = mock(State::class);

        // Setup common mocks
        $this->agent->shouldReceive('run')->andReturn($this->run);
        $this->run->shouldReceive('createLog')->andReturn(null);
    });

    it('handles save_all action by continuing to next step', function () {
        $this->state->data = [
            '_action_id' => 'save_all',
            'summary' => 'Test summary',
            'topics' => ['Topic 1', 'Topic 2'],
        ];

        $result = $this->step->handle($this->agent, $this->state);

        // Should return the state unchanged for the next step
        expect($result)->toBe($this->state);
        
        // Verify log was created
        $this->run->shouldHaveReceived('createLog')
            ->with('Processing curation. Action taken: save_all', ['user_input' => [
                'summary' => 'Test summary',
                'topics' => ['Topic 1', 'Topic 2'],
            ]]);
    });

    it('handles discard_all action by halting the workflow', function () {
        $this->state->data = ['_action_id' => 'discard_all'];
        $haltedState = mock(State::class);

        $this->state->shouldReceive('halt')
            ->with('User discarded analysis results.')
            ->andReturn($haltedState);

        $result = $this->step->handle($this->agent, $this->state);

        expect($result)->toBe($haltedState);
        
        // Verify logs were created
        $this->run->shouldHaveReceived('createLog')
            ->with('Processing curation. Action taken: discard_all', ['user_input' => []]);
        $this->run->shouldHaveReceived('createLog')
            ->with('User chose to discard all insights and halt.');
    });

    it('handles edit_and_save action by halting with not implemented message', function () {
        $this->state->data = ['_action_id' => 'edit_and_save'];
        $haltedState = mock(State::class);

        $this->state->shouldReceive('halt')
            ->with('Edit functionality not implemented.')
            ->andReturn($haltedState);

        $result = $this->step->handle($this->agent, $this->state);

        expect($result)->toBe($haltedState);
        
        // Verify logs were created
        $this->run->shouldHaveReceived('createLog')
            ->with('Processing curation. Action taken: edit_and_save', ['user_input' => []]);
        $this->run->shouldHaveReceived('createLog')
            ->with('User chose to edit (not implemented), halting for now.');
    });

    it('handles unknown action by halting with error', function () {
        $this->state->data = ['_action_id' => 'unknown_action'];
        $haltedState = mock(State::class);

        $this->state->shouldReceive('halt')
            ->with('Curation processing error: Unknown action.')
            ->andReturn($haltedState);

        $result = $this->step->handle($this->agent, $this->state);

        expect($result)->toBe($haltedState);
        
        // Verify log was created
        $this->run->shouldHaveReceived('createLog')
            ->with('Processing curation. Action taken: unknown_action', ['user_input' => []]);
    });
}); 