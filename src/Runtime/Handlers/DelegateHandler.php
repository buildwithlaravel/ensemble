<?php

namespace BuildWithLaravel\Ensemble\Runtime\Handlers;

use BuildWithLaravel\Ensemble\Contracts\InterruptHandlerInterface;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Enums\InterruptType;
use BuildWithLaravel\Ensemble\Models\Run;

class DelegateHandler implements InterruptHandlerInterface
{
    public function canHandle(InterruptType $type): bool
    {
        return $type === InterruptType::Delegate;
    }

    public function handle(Run $run, State $state): void
    {
        $run->status = 'delegating';
        $run->save();
        $meta = $state->getMeta();
        $run->createLog('Delegating to agent.', [
            'agent' => $meta['agent'] ?? null,
            'initial_state' => $meta['initial_state'] ?? null,
        ]);
    }
}
