<?php

declare(strict_types=1);

namespace HomeControl\Models;

class Light extends Device
{
    private int $brightness;
    private string $color;

    public function __construct(
        int $id,
        string $name,
        bool $state,
        int $roomId,
        int $brightness = 100,
        string $color = '#FFFFFF'
    ) {
        parent::__construct($id, $name, 'light', $state, $roomId);
        $this->brightness = $brightness;
        $this->color = $color;
    }

    public function getBrightness(): int
    {
        return $this->brightness;
    }

    public function setBrightness(int $brightness): void
    {
        if ($brightness < 0 || $brightness > 100) {
            throw new \InvalidArgumentException('Brightness must be between 0 and 100');
        }
        $this->brightness = $brightness;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            throw new \InvalidArgumentException('Color must be a valid hex color code');
        }
        $this->color = $color;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'state' => $this->state,
            'roomId' => $this->roomId,
            'brightness' => $this->brightness,
            'color' => $this->color,
        ];
    }
}
