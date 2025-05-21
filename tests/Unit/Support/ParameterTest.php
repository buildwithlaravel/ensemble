<?php

use BuildWithLaravel\Ensemble\Enums\ParameterType;
use BuildWithLaravel\Ensemble\Support\Parameter;

test('Parameter make and fluent setters work', function () {
    $param = Parameter::make('foo', ParameterType::String)
        ->description('A foo string')
        ->required();
    expect($param->name)->toBe('foo');
    expect($param->type)->toBe(ParameterType::String);
    expect($param->description)->toBe('A foo string');
    expect($param->required)->toBeTrue();

    $param->optional();
    expect($param->required)->toBeFalse();
});

test('Parameter required can be set to false', function () {
    // TODO: Test that Parameter required can be set to false
});
