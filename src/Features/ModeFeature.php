<?php

declare(strict_types=1);

namespace HomeControl\Features;

class ModeFeature extends BaseFeature
{
    public function getId(): string
    {
        return 'mode';
    }

    public function getName(): string
    {
        return 'Mode';
    }

    public function getDescription(): string
    {
        return 'Controls the operating mode of the device.';
    }

    protected function getDefaultValue(): mixed
    {
        return 'auto';
    }

    protected function validateValue(mixed $value): void
    {
        $validModes = ['auto', 'heat', 'cool', 'off'];
        if (!is_string($value) || !in_array($value, $validModes)) {
            throw new \InvalidArgumentException('Mode must be one of: ' . implode(', ', $validModes));
        }
    }

    public function getSchema(): array
    {
        return [
            'type' => 'string',
            'enum' => ['auto', 'heat', 'cool', 'off'],
            'description' => 'Operating mode of the device.',
        ];
    }
}