<?php

declare(strict_types=1);

namespace HomeControl\Features;

abstract class BaseFeature implements Feature
{
    protected mixed $value;
    protected bool $enabled = true;

    public function __construct(mixed $initialValue = null)
    {
        $this->value = $initialValue ?? $this->getDefaultValue();
    }

    abstract protected function getDefaultValue(): mixed;

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->validateValue($value);
        $this->value = $value;
    }

    abstract protected function validateValue(mixed $value): void;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'value' => $this->getValue(),
            'enabled' => $this->isEnabled(),
            'schema' => $this->getSchema(),
        ];
    }
}