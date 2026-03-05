<?php

declare(strict_types=1);

namespace HomeControl\Features;

class TemperatureFeature extends BaseFeature
{
    public function getId(): string
    {
        return 'temperature';
    }

    public function getName(): string
    {
        return 'Temperature';
    }

    public function getDescription(): string
    {
        return 'Controls the target temperature setting.';
    }

    protected function getDefaultValue(): mixed
    {
        return 22.0;
    }

    protected function validateValue(mixed $value): void
    {
        if (!is_numeric($value) || $value < 10 || $value > 35) {
            throw new \InvalidArgumentException('Temperature must be a number between 10 and 35.');
        }
    }

    public function getSchema(): array
    {
        return [
            'type' => 'number',
            'minimum' => 10,
            'maximum' => 35,
            'description' => 'Target temperature in degrees Celsius.',
        ];
    }
}