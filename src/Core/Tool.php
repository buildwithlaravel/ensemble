<?php

namespace BuildWithLaravel\Ensemble\Core;

use BuildWithLaravel\Ensemble\Support\Parameter;

abstract class Tool
{
    protected string $description = '';

    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return Parameter[]
     */
    public function parameters(): array
    {
        return [];
    }

    /**
     * Executes the tool logic.
     */
    abstract public function handle(array $arguments, Agent $agent): ?string;
}
