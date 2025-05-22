<?php

namespace BuildWithLaravel\Ensemble\Support;

use BuildWithLaravel\Ensemble\Models\Memory;
use BuildWithLaravel\Ensemble\Support\Traits\ResolvesEnsembleModels;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MemoryStore
{
    use ResolvesEnsembleModels;

    protected Model $memoryable;

    private function __construct(Model $memoryable)
    {
        if (!($memoryable instanceof Model)) {
            throw new \InvalidArgumentException('MemoryStore requires a valid Eloquent model instance.');
        }
        $this->memoryable = $memoryable;
    }

    public static function for(Model $memoryable): self
    {
        return new self($memoryable);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $memory = $this->query()
            ->where('key', $key)
            ->first();

        return $memory ? $memory->value : $default;
    }

    public function put(string $key, mixed $value): static
    {
        $memoryClass = self::resolveModel('memory', Memory::class);
        $memory = $memoryClass::firstOrNew([
            'memoryable_id' => $this->memoryable->getKey(),
            'memoryable_type' => $this->memoryable->getMorphClass(),
            'key' => $key,
        ]);
        $memory->value = $value;
        $memory->save();

        return $this;
    }

    public function forget(string $key): static
    {
        $this->query()
            ->where('key', $key)
            ->delete();

        return $this;
    }

    public function all(): array
    {
        return $this->query()
            ->get()
            ->pluck('value', 'key')
            ->toArray();
    }

    public function flush(): static
    {
        $this->query()
            ->delete();

        return $this;
    }

    protected function query(): Builder
    {
        /** @var Memory $memoryClass */
        $memoryClass = self::resolveModel('memory', Memory::class);
        return $memoryClass::for($this->memoryable);
    }
}
