<?php

namespace BuildWithLaravel\Ensemble\Contracts;

use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Enums\InterruptType;
use BuildWithLaravel\Ensemble\Models\Run;

interface InterruptHandlerInterface
{
    public function canHandle(InterruptType $type): bool;

    public function handle(Run $run, State $state): void;
}
