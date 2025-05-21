<?php

namespace BuildWithLaravel\Ensemble\Support;

use BuildWithLaravel\Ensemble\Models\Memory;
use BuildWithLaravel\Ensemble\Support\Traits\ResolvesEnsembleModels;
use Illuminate\Database\Eloquent\Model;

class MemoryStore
{
    use ResolvesEnsembleModels;

    protected Model $memoryable;

    private function __construct(Model $memoryable)
    {
        if (! ($memoryable instanceof Model)) {
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
        $memoryClass = self::resolveModel('memory', Memory::class);
        $memory = $memoryClass::where('memoryable_id', $this->memoryable->getKey())
            ->where('memoryable_type', $this->memoryable->getMorphClass())
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
        $memoryClass = self::resolveModel('memory', Memory::class);
        $memoryClass::where('memoryable_id', $this->memoryable->getKey())
            ->where('memoryable_type', $this->memoryable->getMorphClass())
            ->where('key', $key)
            ->delete();

        return $this;
    }

    public function all(): array
    {
        $memoryClass = self::resolveModel('memory', Memory::class);

        return $memoryClass::where('memoryable_id', $this->memoryable->getKey())
            ->where('memoryable_type', $this->memoryable->getMorphClass())
            ->get()
            ->pluck('value', 'key')
            ->toArray();
    }

    public function flush(): static
    {
        $memoryClass = self::resolveModel('memory', Memory::class);
        $memoryClass::where('memoryable_id', $this->memoryable->getKey())
            ->where('memoryable_type', $this->memoryable->getMorphClass())
            ->delete();

        return $this;
    }
}
