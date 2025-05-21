<?php

use BuildWithLaravel\Ensemble\Core\Tool;
use BuildWithLaravel\Ensemble\Enums\ParameterType;
use BuildWithLaravel\Ensemble\Support\Parameter;

test('Tool contract: must implement required methods', function () {
    $tool = new class extends Tool
    {
        public function description(): string
        {
            return 'desc';
        }

        public function parameters(): array
        {
            return [Parameter::make('foo', ParameterType::String)->description('desc')->required()];
        }

        public function handle(array $arguments, $agent): ?string
        {
            return 'ok';
        }
    };
    expect($tool->description())->toBe('desc');
    $params = $tool->parameters();
    expect($params)->toBeArray();
    expect($params[0])->toBeInstanceOf(Parameter::class);
    expect($params[0]->name)->toBe('foo');
    expect($params[0]->type)->toBe(ParameterType::String);
    expect($params[0]->description)->toBe('desc');
    expect($params[0]->required)->toBeTrue();
    expect($tool->handle(['foo' => 'bar'], null))->toBe('ok');
});
