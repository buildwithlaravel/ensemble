<?php

use BuildWithLaravel\Ensemble\Agents\ContentAnalysisAgent;
use BuildWithLaravel\Ensemble\Core\Artifact;
use BuildWithLaravel\Ensemble\Core\ArtifactAction;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Models\Run;

describe('ContentAnalysisAgent', function () {
    it('defines content curation form artifact when paused with curate_content tag', function () {
        $agent = new ContentAnalysisAgent();
        $run = mock(Run::class);
        $state = mock(State::class);
        $interrupt = mock(State\Interrupt::class);

        // Mock the necessary method calls
        $run->status = 'paused';
        $state->shouldReceive('getInterrupt')->andReturn($interrupt);
        $interrupt->shouldReceive('getMeta')->andReturn([
            'tag' => 'curate_content',
            'message' => 'Review the summary and topics, then choose an action.',
        ]);
        $state->data = [
            'summary' => 'Test summary',
            'topics' => ['Topic 1', 'Topic 2'],
        ];

        // Get the artifact
        $artifact = $agent->defineArtifact($run, $state);

        // Verify the artifact structure
        expect($artifact)->toBeInstanceOf(Artifact::class);
        expect($artifact->type)->toBe('content-curation-form');
        expect($artifact->data)->toMatchArray([
            'summary' => 'Test summary',
            'topics' => ['Topic 1', 'Topic 2'],
            'instructions' => 'Review the summary and topics, then choose an action.',
        ]);

        // Verify actions
        expect($artifact->actions)->toHaveCount(3);
        
        // Verify save_all action
        $saveAction = $artifact->actions[0];
        expect($saveAction)->toBeInstanceOf(ArtifactAction::class);
        expect($saveAction->id)->toBe('save_all');
        expect($saveAction->type)->toBe('button');
        expect($saveAction->variant)->toBe('primary');
        expect($saveAction->requires_confirmation)->toBeTrue();

        // Verify discard_all action
        $discardAction = $artifact->actions[1];
        expect($discardAction)->toBeInstanceOf(ArtifactAction::class);
        expect($discardAction->id)->toBe('discard_all');
        expect($discardAction->variant)->toBe('danger');
        expect($discardAction->requires_confirmation)->toBeTrue();

        // Verify edit_and_save action
        $editAction = $artifact->actions[2];
        expect($editAction)->toBeInstanceOf(ArtifactAction::class);
        expect($editAction->id)->toBe('edit_and_save');
        expect($editAction->disabled)->toBeTrue();
    });

    it('falls back to parent defineArtifact when not in curate_content state', function () {
        $agent = new ContentAnalysisAgent();
        $run = mock(Run::class);
        $state = mock(State::class);

        $run->status = 'running';
        $state->shouldReceive('getInterrupt')->andReturn(null);

        $artifact = $agent->defineArtifact($run, $state);
        
        // The actual return value depends on the parent implementation
        // We just verify it called through to parent by not throwing any errors
        expect($artifact)->toBeNull();
    });
}); 