<?php

namespace BuildWithLaravel\Ensemble\Runtime;

use BuildWithLaravel\Ensemble\Contracts\InterruptHandlerInterface;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Models\Run;
use RuntimeException;

class InterruptHandler
{
    /** @var InterruptHandlerInterface[] */
    protected array $handlers = [];

    public function register(InterruptHandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    public function handle(Run $run, State $state): void
    {
        if (! $state->isInterrupted()) {
            throw new RuntimeException('InterruptHandler called for non-interrupted state.');
        }
        $type = $state->getInterrupt();
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($type)) {
                $handler->handle($run, $state);

                return;
            }
        }
        throw new RuntimeException("No handler registered for interrupt type [{$type->value}]");
    }
}
