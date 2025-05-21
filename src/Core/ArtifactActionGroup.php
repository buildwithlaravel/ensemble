<?php

namespace BuildWithLaravel\Ensemble\Core;

use Livewire\Wireable;

class ArtifactActionGroup implements Wireable
{
    public ?string $id = null;
    public ?string $label = null;
    /** @var array<ArtifactAction|ArtifactActionGroup> */
    public array $actions = [];

    private function __construct(?string $id = null)
    {
        $this->id = $id;
    }

    public static function make(?string $id = null): static
    {
        return new static($id);
    }

    public function label(?string $label): static
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param array<ArtifactAction|ArtifactActionGroup> $actions
     */
    public function actions(array $actions): static
    {
        foreach ($actions as $action) {
            if (!$action instanceof ArtifactAction && !$action instanceof ArtifactActionGroup) {
                throw new \InvalidArgumentException('Each action must be an instance of ArtifactAction or ArtifactActionGroup');
            }
        }
        $this->actions = $actions;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'actions' => array_map(fn ($action) => $action->toArray(), $this->actions),
        ];
    }

    public function toLivewire()
    {
        return $this->toArray();
    }

    public static function fromLivewire($value): static
    {
        $group = self::make($value['id'] ?? null)->label($value['label'] ?? null);
        $actions = [];
        foreach ($value['actions'] ?? [] as $action) {
            if (isset($action['actions'])) {
                $actions[] = self::fromLivewire($action);
            } else {
                $actions[] = ArtifactAction::fromLivewire($action);
            }
        }
        $group->actions($actions);
        return $group;
    }
}