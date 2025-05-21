<?php

namespace BuildWithLaravel\Ensemble\Support;

use BuildWithLaravel\Ensemble\Enums\ParameterType;

class Parameter
{
    public string $name;

    public ParameterType $type;

    public ?string $description = null;

    public bool $required = false;

    private function __construct(string $name, ParameterType $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public static function make(string $name, ParameterType $type): static
    {
        return new static($name, $type);
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function optional(): static
    {
        return $this->required(false);
    }
}
