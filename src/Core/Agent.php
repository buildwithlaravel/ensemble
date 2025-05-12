<?php

namespace BuildWithLaravel\Ensemble\Core;

use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

/**
 * Abstract base class for agents with built-in Prism LLM support.
 */
abstract class Agent
{
    protected ?Provider $provider = null;

    protected ?string $model = null;

    protected function resolvePrismProvider(): ?Provider
    {
        if ($this->provider !== null) {
            return $this->provider;
        }

        $configValue = config('ensemble.defaults.llm.provider');
        if ($configValue === null) {
            return null;
        }

        // Convert config string value to Provider enum
        return Provider::from($configValue);
    }

    /**
     * Resolve the Prism model to use.
     * Returns the agent's model if set, otherwise the default from config.
     */
    protected function resolvePrismModel(): ?string
    {
        return $this->model ?? config('ensemble.defaults.llm.model');
    }

    /**
     * Get a configured Prism client instance.
     */
    protected function getPrismClient(): Prism
    {
        $provider = $this->resolvePrismProvider();
        $model = $this->resolvePrismModel();

        /** @var Prism $prism */
        $prism = app(Prism::class);

        if ($provider !== null && $model !== null) {
            $prism->using($provider, $model);
        }

        return $prism;
    }

    /**
     * Execute a callback with a configured Prism client instance.
     */
    public function withPrism(callable $callback): static
    {
        $callback($this->getPrismClient());

        return $this;
    }
}
