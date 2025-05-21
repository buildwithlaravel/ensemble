<?php

namespace BuildWithLaravel\Ensemble\Support\Traits;

use BuildWithLaravel\Ensemble\Enums\EventType;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait HasEvents
{
    public function event(EventType $eventType, array $payload = [])
    {
        if ($eventType == EventType::RunError) {
            $payload['exception'] = $this->convertExceptionToArray($payload['exception']);
        }
        Log::info('Ensemble Event: ' . $eventType->value, $payload);

//        event(new EnsembleEvent(run: $this, type: $eventType, payload: $payload));
    }

    // TODO: Move out to event listener
    protected function convertExceptionToArray(\Throwable $e)
    {
        return config('app.debug') ? [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => (new Collection($e->getTrace()))->map(fn ($trace) => Arr::except($trace, ['args']))->all(),
        ] : [
            'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
        ];
    }
}