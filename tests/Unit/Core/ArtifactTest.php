<?php

describe('Artifact', function () {
    it('creates an instance with correct type, data, and actions', function () {
        // TODO: Create an Artifact using Artifact::make
        // TODO: Assert the type, data, and actions properties are set correctly
    });

    it('is serializable to JSON with public properties', function () {
        // TODO: Create an Artifact and encode to JSON
        // TODO: Assert the JSON structure matches the expected type, data, actions
    });
});

describe('ArtifactAction', function () {
    it('can be created with id and label and supports fluent API', function () {
        // PREPARE: Create an ArtifactAction with id and label
        // ACT: Chain fluent setters (type, variant, requiresConfirmation, etc.)
        // ASSERT: All properties are set as expected
    });

    it('has correct default property values', function () {
        // PREPARE: Create an ArtifactAction with only id and label
        // ACT: None
        // ASSERT: Defaults for type, variant, requires_confirmation, disabled, visible, data
    });
});

describe('ArtifactActionGroup', function () {
    it('can be created with id and supports fluent API', function () {
        // PREPARE: Create an ArtifactActionGroup with id
        // ACT: Set label and actions (with valid ArtifactAction/ArtifactActionGroup)
        // ASSERT: Properties are set as expected
    });

    it('actions property can hold ArtifactAction and nested ArtifactActionGroup', function () {
        // PREPARE: Create ArtifactAction and ArtifactActionGroup
        // ACT: Add both to a group
        // ASSERT: actions property contains both types
    });
});



describe('State::waitForHuman', function () {
    it('stores actions in interrupt meta', function () {
        // PREPARE: Create ArtifactAction and ArtifactActionGroup
        // ACT: Call waitForHuman with actions array
        // ASSERT: State meta contains the actions array
    });
});

describe('Agent defineArtifact usage', function () {
    it('can return an Artifact with actions and groups without type errors', function () {
        // PREPARE: Create mock agent, ArtifactAction, ArtifactActionGroup
        // ACT: Return Artifact with actions/groups from defineArtifact
        // ASSERT: No type errors, artifact is constructed as expected
    });
});