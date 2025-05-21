<?php

use BuildWithLaravel\Ensemble\Agents\Steps\ReviewAndCurateStep;
use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;

describe('ReviewAndCurateStep', function () {
    it('creates waitForHuman interrupt with existing state data', function () {
        $step = new ReviewAndCurateStep();
        $agent = mock(Agent::class);
        $state = mock(State::class);
        $interruptState = mock(State::class);

        // Setup state with existing data
        $state->data = [
            'summary' => 'Existing summary',
            'topics' => ['Existing Topic 1', 'Existing Topic 2'],
        ];

        // Mock waitForHuman call
        $state->shouldReceive('waitForHuman')
            ->with(
                'curate_content',
                'Review the summary and topics, then choose an action.',
                []
            )
            ->andReturn($interruptState);

        $result = $step->handle($agent, $state);

        expect($result)->toBe($interruptState);
    });

    it('creates waitForHuman interrupt with default data when none exists', function () {
        $step = new ReviewAndCurateStep();
        $agent = mock(Agent::class);
        $state = mock(State::class);
        $interruptState = mock(State::class);

        // Setup state with no existing data
        $state->data = [];

        // Mock waitForHuman call
        $state->shouldReceive('waitForHuman')
            ->with(
                'curate_content',
                'Review the summary and topics, then choose an action.',
                []
            )
            ->andReturn($interruptState);

        $result = $step->handle($agent, $state);

        expect($result)->toBe($interruptState);
        expect($state->data)->toMatchArray([
            'summary' => 'Example content summary for review',
            'topics' => ['Topic 1', 'Topic 2', 'Topic 3'],
        ]);
    });
}); 