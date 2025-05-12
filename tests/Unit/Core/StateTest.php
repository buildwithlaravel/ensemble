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
    $state = new TestState([
        'name' => 'Test Name',
        'count' => 42,
    ]);

    expect($state->name)->toBe('Test Name');
    expect($state->count)->toBe(42);
});

test('it merges data into public properties', function () {
    $state = new TestState([
        'name' => 'Original Name',
        'count' => 10,
    ]);

    // Merge should update public properties and return the instance
    $result = $state->merge([
        'name' => 'Updated Name',
        'count' => 20,
    ]);

    expect($result)->toBe($state);
    expect($state->name)->toBe('Updated Name');
    expect($state->count)->toBe(20);
});

test('it creates instances with static from method', function () {
    $state = TestState::from([
        'name' => 'Static Name',
        'count' => 99,
    ]);

    expect($state)->toBeInstanceOf(TestState::class);
    expect($state->name)->toBe('Static Name');
    expect($state->count)->toBe(99);
});

test('it sets halt interrupt', function () {
    $state = new TestState;
    $result = $state->halt('Testing halt');

    expect($result)->toBe($state);
    expect($state->isInterrupted())->toBeTrue();
    expect($state->getInterrupt())->toBe(InterruptType::Halt);
    expect($state->getMeta())->toBe(['reason' => 'Testing halt']);
});

test('it sets retry interrupt', function () {
    $state = new TestState;
    $result = $state->retry('Testing retry');

    expect($result)->toBe($state);
    expect($state->isInterrupted())->toBeTrue();
    expect($state->getInterrupt())->toBe(InterruptType::Retry);
    expect($state->getMeta())->toBe(['reason' => 'Testing retry']);
});

test('it sets waitForHuman interrupt', function () {
    $state = new TestState;
    $result = $state->waitForHuman('confirm', 'Please confirm this action');

    expect($result)->toBe($state);
    expect($state->isInterrupted())->toBeTrue();
    expect($state->getInterrupt())->toBe(InterruptType::WaitHuman);
    expect($state->getMeta())->toBe([
        'tag' => 'confirm',
        'message' => 'Please confirm this action',
    ]);
});

test('it sets waitForEvent interrupt', function () {
    $state = new TestState;
    $payload = ['action' => 'test', 'id' => 123];
    $result = $state->waitForEvent('user.input', $payload);

    expect($result)->toBe($state);
    expect($state->isInterrupted())->toBeTrue();
    expect($state->getInterrupt())->toBe(InterruptType::WaitEvent);
    expect($state->getMeta())->toBe([
        'event' => 'user.input',
        'payload' => $payload,
    ]);
});

test('it sets callTool interrupt', function () {
    $state = new TestState;
    $arguments = ['prompt' => 'Generate an image', 'style' => 'cartoon'];
    $result = $state->callTool('image_generator', $arguments);

    expect($result)->toBe($state);
    expect($state->isInterrupted())->toBeTrue();
    expect($state->getInterrupt())->toBe(InterruptType::CallTool);
    expect($state->getMeta())->toBe([
        'tool' => 'image_generator',
        'arguments' => $arguments,
    ]);
});

test('it sets delegate interrupt', function () {
    $state = new TestState;
    $initialStateData = ['name' => 'Delegated Task', 'priority' => 'high'];
    $result = $state->delegate('App\\Agents\\SearchAgent', $initialStateData);

    expect($result)->toBe($state);
    expect($state->isInterrupted())->toBeTrue();
    expect($state->getInterrupt())->toBe(InterruptType::Delegate);
    expect($state->getMeta())->toBe([
        'agent_class' => 'App\\Agents\\SearchAgent',
        'initial_state' => $initialStateData,
    ]);
});

test('it sets done interrupt', function () {
    $state = new TestState;
    $resultData = ['status' => 'success', 'data' => ['id' => 123]];
    $result = $state->done($resultData);

    expect($result)->toBe($state);
    expect($state->isInterrupted())->toBeTrue();
    expect($state->getInterrupt())->toBe(InterruptType::Done);
    expect($state->getMeta())->toBe(['result' => $resultData]);
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
