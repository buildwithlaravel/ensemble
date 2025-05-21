<?php

namespace BuildWithLaravel\Ensemble\Runtime\Handlers;

use BuildWithLaravel\Ensemble\Contracts\InterruptHandlerInterface;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Enums\InterruptType;
use BuildWithLaravel\Ensemble\Models\Run;

class WaitForEventHandler implements InterruptHandlerInterface
{
    public function canHandle(InterruptType $type): bool
    {
        return $type === InterruptType::WaitEvent;
    }

    public function handle(Run $run, State $state): void
    {
        $run->status = 'waiting_event';
        $run->save();
        $meta = $state->getMeta();
        $run->createLog('Waiting for event.', [
            'event' => $meta['event'] ?? null,
            'payload' => $meta['payload'] ?? null,
        ]);
    }
}
