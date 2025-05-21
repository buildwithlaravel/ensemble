<?php

namespace BuildWithLaravel\Ensemble\Runtime\Handlers;

use BuildWithLaravel\Ensemble\Contracts\InterruptHandlerInterface;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Enums\InterruptType;
use BuildWithLaravel\Ensemble\Models\Run;

class DoneHandler implements InterruptHandlerInterface
{
    public function canHandle(InterruptType $type): bool
    {
        return $type === InterruptType::Done;
    }

    public function handle(Run $run, State $state): void
    {
        $run->status = 'completed';
        $run->save();
        $meta = $state->getMeta();
        $run->createLog('Run completed.', ['result' => $meta['result'] ?? null]);
    }
}
