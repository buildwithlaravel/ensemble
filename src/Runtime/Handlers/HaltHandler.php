<?php

namespace BuildWithLaravel\Ensemble\Runtime\Handlers;

use BuildWithLaravel\Ensemble\Contracts\InterruptHandlerInterface;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Enums\InterruptType;
use BuildWithLaravel\Ensemble\Models\Run;

class HaltHandler implements InterruptHandlerInterface
{
    public function canHandle(InterruptType $type): bool
    {
        return $type === InterruptType::Halt;
    }

    public function handle(Run $run, State $state): void
    {
        $run->status = 'halted';
        $run->save();
        $meta = $state->getMeta();
        $run->createLog('Run halted.', ['reason' => $meta['reason'] ?? null]);
    }
}
