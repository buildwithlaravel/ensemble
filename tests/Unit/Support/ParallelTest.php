<?php

namespace BuildWithLaravel\Ensemble\Tests\Unit\Support;

use BuildWithLaravel\Ensemble\Support\Parallel;
use BuildWithLaravel\Ensemble\Tests\TestCase;

test('it can be instantiated with an array of steps', function () {
    $steps = [
        'StepOne',
        'StepTwo',
    ];

    $parallel = new Parallel($steps);

    expect($parallel->steps)->toBe($steps);
});

test('it can be created using the make method', function () {
    $steps = [
        'StepOne',
        'StepTwo',
    ];

    $parallel = Parallel::make($steps);

    expect($parallel)
        ->toBeInstanceOf(Parallel::class)
        ->and($parallel->steps)
        ->toBe($steps);
}); 