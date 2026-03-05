<?php

declare(strict_types=1);

namespace HomeControl\Features;

class ColorFeature extends BaseFeature
{
    public function getId(): string
    {
        return 'color';
    }

    public function getName(): string
    {
        return 'Color';
    }

    public function getDescription(): string
    {
        return 'Controls the color of the device using hex color codes.';
    }

    protected function getDefaultValue(): mixed
    {
        return '#FFFFFF';
    }

    protected function validateValue(mixed $value): void
    {
        if (!is_string($value) || !preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
            throw new \InvalidArgumentException('Color must be a valid hex color code (e.g., #FFFFFF).');
        }
    }

    public function getSchema(): array
    {
        return [
            'type' => 'string',
            'pattern' => '^#[0-9A-Fa-f]{6}$',
            'description' => 'Hex color code (e.g., #FFFFFF for white).',
        ];
    }
}