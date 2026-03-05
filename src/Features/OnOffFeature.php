<?php

declare(strict_types=1);

namespace HomeControl\Features;

class OnOffFeature extends BaseFeature
{
    public function getId(): string
    {
        return 'on_off';
    }

    public function getName(): string
    {
        return 'On/Off';
    }

    public function getDescription(): string
    {
        return 'Controls whether the device is turned on or off.';
    }

    protected function getDefaultValue(): mixed
    {
        return false;
    }

    protected function validateValue(mixed $value): void
    {
        if (!is_bool($value)) {
            throw new \InvalidArgumentException('On/Off feature value must be a boolean.');
        }
    }

    public function getSchema(): array
    {
        return [
            'type' => 'boolean',
            'description' => 'True for on, false for off.',
        ];
    }
}