<?php

namespace BuildWithLaravel\Ensemble\Runtime\Handlers;

use BuildWithLaravel\Ensemble\Contracts\InterruptHandlerInterface;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Enums\InterruptType;
use BuildWithLaravel\Ensemble\Models\Run;

class CallToolHandler implements InterruptHandlerInterface
{
    public function canHandle(InterruptType $type): bool
    {
        return $type === InterruptType::CallTool;
    }

    public function handle(Run $run, State $state): void
    {
        $run->status = 'calling_tool';
        $run->save();
        $meta = $state->getMeta();
        $run->createLog('Calling tool.', [
            'tool' => $meta['tool'] ?? null,
            'arguments' => $meta['arguments'] ?? null,
        ]);
    }
}
