<?php

namespace BuildWithLaravel\Ensemble\Support;

use BuildWithLaravel\Ensemble\Enums\EventType;
use BuildWithLaravel\Ensemble\Models\Run;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnsembleEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Run $run, public EventType $type, public array $payload)
    {

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ensemble-events.' . $this->run->id)
        ];
    }

    public function broadcastAs(): string
    {
        return 'ensemble-event';
    }

    public function broadcastWith(): array
    {
        return  [
            'event_type' => $this->type->value,
            'status' => $this->run->status,
            'current_step' => $this->run->currentStep()::description(),
            'payload' => $this->payload
        ];
    }

}