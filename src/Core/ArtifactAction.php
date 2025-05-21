<?php

namespace BuildWithLaravel\Ensemble\Core;

use Livewire\Wireable;

class ArtifactAction implements Wireable
{
    public string $id;
    public ?string $label = null;
    public string $type = 'button';
    public ?string $variant = null;
    public bool $requires_confirmation = false;
    public ?string $confirmation_message = null;
    public bool $disabled = false;
    public bool $visible = true;
    public array $data = [];

    private static array $allowedTypes = ['button', 'link', 'icon'];

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function make(string $id): static
    {
        return new static($id);
    }

    public function type(string $type): static
    {
        if (!in_array($type, self::$allowedTypes, true)) {
            throw new \InvalidArgumentException("Invalid action type: $type");
        }
        $this->type = $type;
        return $this;
    }

    public function variant(?string $variant): static
    {
        $this->variant = $variant;
        return $this;
    }

    public function label(string $label)
    {
        $this->label = $label;

        return $this;
    }

    public function requiresConfirmation(bool $requires = true, ?string $message = null): static
    {
        $this->requires_confirmation = $requires;
        if ($message !== null) {
            $this->confirmation_message = $message;
        }
        return $this;
    }

    public function confirmationMessage(?string $message): static
    {
        $this->confirmation_message = $message;
        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function visible(bool $visible = true): static
    {
        $this->visible = $visible;
        return $this;
    }

    public function withData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'type' => $this->type,
            'variant' => $this->variant,
            'requires_confirmation' => $this->requires_confirmation,
            'confirmation_message' => $this->confirmation_message,
            'disabled' => $this->disabled,
            'visible' => $this->visible,
            'data' => $this->data,
        ];
    }

    public function toLivewire()
    {
        return $this->toArray();
    }

    public static function fromLivewire($value): static
    {
        $action = self::make($value['id'], $value['label']);
        $action->type = $value['type'] ?? 'button';
        $action->variant = $value['variant'] ?? null;
        $action->requires_confirmation = $value['requires_confirmation'] ?? false;
        $action->confirmation_message = $value['confirmation_message'] ?? null;
        $action->disabled = $value['disabled'] ?? false;
        $action->visible = $value['visible'] ?? true;
        $action->data = $value['data'] ?? [];
        return $action;
    }
}