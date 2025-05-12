<?php

namespace BuildWithLaravel\Ensemble\Support;

class Parallel
{
    /**
     * The steps to be executed in parallel.
     */
    public array $steps;

    /**
     * Create a new Parallel instance.
     */
    public function __construct(array $steps)
    {
        $this->steps = $steps;
    }

    /**
     * Create a new Parallel instance.
     */
    public static function make(array $steps): self
    {
        return new self($steps);
    }
}
