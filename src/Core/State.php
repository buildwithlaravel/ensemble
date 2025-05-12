<?php

namespace BuildWithLaravel\Ensemble\Core;

use Illuminate\Support\Fluent;
use BuildWithLaravel\Ensemble\Enums\InterruptType;

/**
 * Abstract base class for agent state DTOs.
 */
abstract class State extends Fluent
{
    /**
     * @var null|InterruptType
     */
    protected ?InterruptType $interrupt = null;

    /**
     * @var null|array
     */
    protected ?array $meta = null;

    /**
     * Hydrate public properties from $data.
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        parent::__construct($data);
    }

    // --- Fluent interrupt methods ---
    public function halt(string $reason): static
    {
        $this->interrupt = InterruptType::Halt;
        $this->meta = ['reason' => $reason];
        return $this;
    }

    public function retry(string $reason): static
    {
        $this->interrupt = InterruptType::Retry;
        $this->meta = ['reason' => $reason];
        return $this;
    }

    public function waitForHuman(string $tag, string $message): static
    {
        $this->interrupt = InterruptType::WaitHuman;
        $this->meta = ['tag' => $tag, 'message' => $message];
        return $this;
    }

    public function waitForEvent(string $event, array $payload = []): static
    {
        $this->interrupt = InterruptType::WaitEvent;
        $this->meta = ['event' => $event, 'payload' => $payload];
        return $this;
    }

    public function callTool(string $tool, array $arguments = []): static
    {
        $this->interrupt = InterruptType::CallTool;
        $this->meta = ['tool' => $tool, 'arguments' => $arguments];
        return $this;
    }

    public function delegate(string $agentClass, array $initialStateData = []): static
    {
        $this->interrupt = InterruptType::Delegate;
        $this->meta = ['agent_class' => $agentClass, 'initial_state' => $initialStateData];
        return $this;
    }

    public function done(mixed $result = null): static
    {
        $this->interrupt = InterruptType::Done;
        $this->meta = ['result' => $result];
        return $this;
    }

    // --- Helper methods ---
    public function isInterrupted(): bool
    {
        return $this->interrupt !== null;
    }

    public function getInterrupt(): ?InterruptType
    {
        return $this->interrupt;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    /**
     * Merge new data into public properties.
     */
    public function merge(array $data): static
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    /**
     * Hydrate a new instance from array.
     */
    public static function from(array $data): static
    {
        return new static($data);
    }
} 