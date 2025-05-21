<?php

namespace BuildWithLaravel\Ensemble\Core;

use BuildWithLaravel\Ensemble\Enums\InterruptType;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use BuildWithLaravel\Ensemble\Core\ArtifactAction;
use BuildWithLaravel\Ensemble\Core\ArtifactActionGroup;

/**
 * @property ?InterruptType interrupt
 * @property array $interruptData
 */
abstract class State extends Fluent
{
    use HasAttributes;

    public function getCasts()
    {
        return [
            ...$this->casts,
            'interrupt' => InterruptType::class,
            'interruptData' => 'array'
        ];
    }


    // --- Fluent interrupt methods ---
    public function error(string $reason): static
    {
        $this->interrupt = InterruptType::Error;
        $this->interruptData = ['reason' => $reason];

        return $this;
    }

    public function halt(string $reason): static
    {
        $this->interrupt = InterruptType::Halt;
        $this->interruptData = ['reason' => $reason];

        return $this;
    }

    public function retry(string $reason): static
    {
        $this->interrupt = InterruptType::Retry;
        $this->interruptData = ['reason' => $reason];

        return $this;
    }

    public function waitForQueued(string $tag): static
    {
        $this->interrupt = InterruptType::WaitForQueue;
        $this->interruptData = ['tag' => $tag];

        return $this;
    }

    /**
     * @param string $tag
     * @param string $message
     * @param array<ArtifactAction|ArtifactActionGroup> $actions
     * @return static
     */
    public function waitForHuman(string $tag, string $message, array $actions = []): static
    {
        $this->interrupt = InterruptType::WaitHuman;
        $this->interruptData = [
            'tag' => $tag,
            'message' => $message,
            'actions' => $actions,
        ];
        return $this;
    }

    public function waitForEvent(string $event, array $payload = []): static
    {
        $this->interrupt = InterruptType::WaitEvent;
        $this->interruptData = ['event' => $event, 'payload' => $payload];

        return $this;
    }

    public function callTool(string $tool, array $arguments = []): static
    {
        $this->interrupt = InterruptType::CallTool;
        $this->interruptData = ['tool' => $tool, 'arguments' => $arguments];

        return $this;
    }

    public function delegate(string $agentClass, array $initialStateData = []): static
    {
        $this->interrupt = InterruptType::Delegate;
        $this->interruptData = ['agent' => $agentClass, 'initial_state' => $initialStateData];

        return $this;
    }

    public function done(mixed $result = null): static
    {
        $this->interrupt = InterruptType::Done;
        $this->interruptData = ['result' => $result];

        return $this;
    }

    // --- Helper methods ---
    public function isInterrupted(): bool
    {
        return $this->interrupt !== null;
    }

    public function getInterrupt(): ?InterruptType
    {
        return InterruptType::tryFrom($this->interrupt);
    }

    public function getMeta(): ?array
    {
        return $this->interruptData;
    }

    /**
     * Hydrate a new instance from array.
     */
    public static function from(array $data): static
    {
        return new static($data);
    }

    public function resetInterrupt(): static
    {
        Arr::forget($this->attributes, ['interrupt', 'interruptData']);

        return $this;
    }
}
