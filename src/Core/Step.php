<?php

namespace BuildWithLaravel\Ensemble\Core;

use JsonSerializable;

/**
 * Abstract base class for workflow steps.
 * Defines the contract for atomic units of work within a workflow.
 *
 * LLM Integration Pattern:
 *   - Step subclasses may `use \BuildWithLaravel\Ensemble\Support\Traits\InteractsWithLLM`.
 *   - Steps can define their own `$provider` and `$model` properties to override agent/global LLM config.
 *   - In the `handle()` method, call `$this->getPrismClient(...)` to access a configured Prism client.
 *   - The trait will resolve provider/model from the step instance first, then fallback to config.
 */
abstract class Step implements JsonSerializable
{
    protected static ?string $description = null;

    abstract public function handle(Agent $agent, State $state): State;

    public static function description(): ?string
    {
        return static::$description ?: static::class;
    }


    public function jsonSerialize(): mixed
    {
        return [
            'name' => $this->name(),
        ];
    }

    protected function name(): string
    {
        return static::class;
    }
}
