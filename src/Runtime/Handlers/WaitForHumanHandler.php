<?php

namespace BuildWithLaravel\Ensemble\Runtime\Handlers;

use BuildWithLaravel\Ensemble\Contracts\InterruptHandlerInterface;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Enums\InterruptType;
use BuildWithLaravel\Ensemble\Models\Run;

class WaitForHumanHandler implements InterruptHandlerInterface
{
    public function canHandle(InterruptType $type): bool
    {
        return $type === InterruptType::WaitHuman;
    }

    public function handle(Run $run, State $state): void
    {
        $run->status = 'paused';
        $run->save();
        $meta = $state->getMeta();
        $run->createLog('Waiting for human input.', [
            'tag' => $meta['tag'] ?? null,
            'message' => $meta['message'] ?? null,
        ]);
    }
}
