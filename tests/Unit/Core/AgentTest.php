<?php

namespace BuildWithLaravel\Ensemble\Tests\Unit\Core;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Tests\TestCase;
use Mockery;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

// Create a concrete implementation of Agent for testing
class TestAgent extends Agent
{
}

test('it uses config defaults when no properties are set', function () {
    /** @var TestCase $this */

    // Mock Prism
    $prismMock = Mockery::mock(Prism::class);
    $prismMock->shouldReceive('using')
        ->once()
        ->with(Provider::from('openai'), 'gpt-4')
        ->andReturnSelf();
    $this->app->instance(Prism::class, $prismMock);

    $agent = new TestAgent();
    $prism = null;

    $agent->withPrism(function ($p) use (&$prism) {
        $prism = $p;
    });

    expect($prism)->toBeInstanceOf(Prism::class);
    $prismMock->shouldHaveReceived('using')->once();
});

test('it uses agent properties over config defaults', function () {
    /** @var TestCase $this */

    // Mock Prism
    $prismMock = Mockery::mock(Prism::class);
    $prismMock->shouldReceive('using')
        ->once()
        ->with(Provider::Anthropic, 'claude-2')
        ->andReturnSelf();
    $this->app->instance(Prism::class, $prismMock);

    $agent = new class extends Agent {
        protected ?Provider $provider = Provider::Anthropic;
        protected ?string $model = 'claude-2';
    };

    $prism = null;
    $agent->withPrism(function ($p) use (&$prism) {
        $prism = $p;
    });

    expect($prism)->toBeInstanceOf(Prism::class);
    $prismMock->shouldHaveReceived('using')->once();
});

test('withPrism executes the callback', function () {
    /** @var TestCase $this */

    // Mock Prism
    $prismMock = Mockery::mock(Prism::class);
    $prismMock->shouldReceive('using')->andReturnSelf();
    $this->app->instance(Prism::class, $prismMock);

    $agent = new TestAgent();
    $called = false;

    $agent->withPrism(function ($prism) use (&$called) {
        $called = true;
    });

    expect($called)->toBeTrue();
});

test('withPrism returns the agent instance', function () {
    /** @var TestCase $this */

    // Mock Prism
    $prismMock = Mockery::mock(Prism::class);
    $prismMock->shouldReceive('using')->andReturnSelf();
    $this->app->instance(Prism::class, $prismMock);

    $agent = new TestAgent();
    $result = $agent->withPrism(function ($prism) {});

    expect($result)->toBe($agent);
}); 