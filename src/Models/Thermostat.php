<?php

declare(strict_types=1);

namespace HomeControl\Models;

class Thermostat extends Device
{
    private float $temperature;
    private float $targetTemperature;
    private string $mode;

    public function __construct(
        int $id,
        string $name,
        bool $state,
        int $roomId,
        float $temperature = 20.0,
        float $targetTemperature = 22.0,
        string $mode = 'auto'
    ) {
        parent::__construct($id, $name, 'thermostat', $state, $roomId);
        $this->temperature = $temperature;
        $this->targetTemperature = $targetTemperature;
        $this->mode = $mode;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): void
    {
        $this->temperature = $temperature;
    }

    public function getTargetTemperature(): float
    {
        return $this->targetTemperature;
    }

    public function setTargetTemperature(float $targetTemperature): void
    {
        if ($targetTemperature < 10 || $targetTemperature > 35) {
            throw new \InvalidArgumentException('Target temperature must be between 10 and 35');
        }
        $this->targetTemperature = $targetTemperature;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): void
    {
        $validModes = ['auto', 'heat', 'cool', 'off'];
        if (!in_array($mode, $validModes)) {
            throw new \InvalidArgumentException('Invalid mode. Must be: ' . implode(', ', $validModes));
        }
        $this->mode = $mode;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'state' => $this->state,
            'roomId' => $this->roomId,
            'temperature' => $this->temperature,
            'targetTemperature' => $this->targetTemperature,
            'mode' => $this->mode,
        ];
    }
}
