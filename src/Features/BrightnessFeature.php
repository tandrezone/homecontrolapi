<?php

declare(strict_types=1);

namespace HomeControl\Features;

class BrightnessFeature extends BaseFeature
{
    public function getId(): string
    {
        return 'brightness';
    }

    public function getName(): string
    {
        return 'Brightness';
    }

    public function getDescription(): string
    {
        return 'Controls the brightness level of the device (0-100).';
    }

    protected function getDefaultValue(): mixed
    {
        return 100;
    }

    protected function validateValue(mixed $value): void
    {
        if (!is_int($value) || $value < 0 || $value > 100) {
            throw new \InvalidArgumentException('Brightness must be an integer between 0 and 100.');
        }
    }

    public function getSchema(): array
    {
        return [
            'type' => 'integer',
            'minimum' => 0,
            'maximum' => 100,
            'description' => 'Brightness level from 0 (off) to 100 (full brightness).',
        ];
    }
}