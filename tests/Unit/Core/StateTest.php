<?php

use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Enums\InterruptType;

// Create a concrete implementation of State for testing
class TestState extends State
{
    public string $name = '';

    public int $count = 0;
}

test('it instantiates with public properties hydrated', function () {
    // TODO: Test that the state instantiates with public properties hydrated
});

test('it merges data into public properties', function () {
    // TODO: Test that merging data updates public properties and returns the instance
});

test('it creates instances with static from method', function () {
    // TODO: Test that static from creates an instance with the correct data
});

test('it sets halt interrupt', function () {
    // TODO: Test that halt sets the correct interrupt and data on the state
});

test('it sets retry interrupt', function () {
    // TODO: Test that retry sets the correct interrupt and data on the state
});

describe('State', function () {
    it('sets waitForHuman interrupt', function () {
        // TODO: Test that waitForHuman sets the correct interrupt and data on the state
    });
    it('sets waitForEvent interrupt', function () {
        // TODO: Test that waitForEvent sets the correct interrupt and data on the state
    });
    it('sets callTool interrupt', function () {
        // TODO: Test that callTool sets the correct interrupt and data on the state
    });
    it('sets delegate interrupt', function () {
        // TODO: Test that delegate sets the correct interrupt and data on the state
    });
    it('sets done interrupt', function () {
        // TODO: Test that done sets the correct interrupt and data on the state
    });
});

test('isInterrupted returns false when not interrupted', function () {
    $state = new TestState;
    expect($state->isInterrupted())->toBeFalse();
    expect($state->getInterrupt())->toBeNull();
    expect($state->getMeta())->toBeNull();
});

test('it supports being initialized with no data', function () {
    $state = new TestState;
    expect($state->name)->toBe('');
    expect($state->count)->toBe(0);
});
