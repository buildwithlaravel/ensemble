<?php

use BuildWithLaravel\Ensemble\Enums\ParameterType;

test('ParameterType enum has correct cases and values', function () {
    expect(ParameterType::String->value)->toBe('string');
    expect(ParameterType::Integer->value)->toBe('integer');
    expect(ParameterType::Boolean->value)->toBe('boolean');
    expect(ParameterType::Array->value)->toBe('array');
    expect(ParameterType::Object->value)->toBe('object');
    expect(ParameterType::Null->value)->toBe('null');
});
