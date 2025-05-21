<?php

namespace BuildWithLaravel\Ensemble\Core;

use Livewire\Wireable;

class Artifact implements Wireable
{
    public string $type;
    public array $data;
    /**
     * @var array<ArtifactAction|ArtifactActionGroup>
     */
    public array $actions;

    private function __construct(string $type, array $data = [], array $actions = [])
    {
        $this->type = $type;
        $this->data = $data;
        $this->actions = $actions;
    }

    public static function make(string $type, array $data = [], array $actions = []): static
    {
        // Type check actions
        foreach ($actions as $action) {
            if (!$action instanceof ArtifactAction && !$action instanceof ArtifactActionGroup) {
                throw new \InvalidArgumentException('Each action must be an instance of ArtifactAction or ArtifactActionGroup');
            }
        }
        return new static($type, $data, $actions);
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'data' => $this->data,
            'actions' => array_map(fn ($action) => $action->toArray(), $this->actions),
        ];
    }

    public function toLivewire()
    {
        return $this->toArray();
    }

    public static function fromLivewire($value): static
    {
        $actions = [];
        foreach ($value['actions'] ?? [] as $action) {
            if (isset($action['actions'])) {
                $actions[] = ArtifactActionGroup::fromLivewire($action);
            } else {
                $actions[] = ArtifactAction::fromLivewire($action);
            }
        }
        return self::make($value['type'], $value['data'] ?? [], $actions);
    }
}