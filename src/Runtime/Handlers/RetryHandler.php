<?php

namespace BuildWithLaravel\Ensemble\Runtime\Handlers;

use BuildWithLaravel\Ensemble\Contracts\InterruptHandlerInterface;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Enums\InterruptType;
use BuildWithLaravel\Ensemble\Models\Run;

class RetryHandler implements InterruptHandlerInterface
{
    public function canHandle(InterruptType $type): bool
    {
        return $type === InterruptType::Retry;
    }

    public function handle(Run $run, State $state): void
    {
        $run->status = 'retrying';
        $run->save();
        $meta = $state->getMeta();
        $run->createLog('Run retrying.', ['reason' => $meta['reason'] ?? null]);
    }
}
