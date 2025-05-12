<?php

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Core\Step;

test('a step subclass must implement handle method with correct signature and return type', function () {
    $agent = Mockery::mock(Agent::class);
    $state = Mockery::mock(State::class);
    $expectedState = Mockery::mock(State::class);

    // Concrete step for testing
    $step = new class extends Step
    {
        public function handle(Agent $agent, State $state): State
        {
            return new class([]) extends State
            {
                public function getData(): array
                {
                    return [];
                }
            };
        }
    };

    $result = $step->handle($agent, $state);
    expect($result)->toBeInstanceOf(State::class);
});
