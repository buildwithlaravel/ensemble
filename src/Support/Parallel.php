<?php

namespace BuildWithLaravel\Ensemble\Support;

use JsonSerializable;

class Parallel implements JsonSerializable
{

    /**
     * @var callable
     */
    protected $onFailCallback;

    public function __construct(public array $steps)
    {
    }

    public static function make(array $steps): static
    {
        return new static($steps);
    }

    public function whenHasFailedSteps(callable $callback): static
    {
        $this->onFailCallback = $callback;

        return $this;
    }

    public function onStepsFailed()
    {

    }

    public function jsonSerialize(): mixed
    {
        return [
            'name' => static::class,
            'steps' => $this->steps,
        ];
    }
}
