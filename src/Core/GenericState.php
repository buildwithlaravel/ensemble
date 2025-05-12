<?php

namespace BuildWithLaravel\Ensemble\Core;

/**
 * Generic state DTO for runs without a specific state class.
 */
class GenericState extends State
{
    public function getData(): array
    {
        return $this->attributes ?? [];
    }
} 