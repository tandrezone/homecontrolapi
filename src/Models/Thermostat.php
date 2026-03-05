<?php

declare(strict_types=1);

namespace HomeControl\Models;

use HomeControl\Features\OnOffFeature;
use HomeControl\Features\TemperatureFeature;
use HomeControl\Features\ModeFeature;

class Thermostat extends Device
{
    private float $currentTemperature;

    public function __construct(
        int $id,
        string $name,
        bool $state,
        int $roomId,
        float $currentTemperature = 20.0,
        float $targetTemperature = 22.0,
        string $mode = 'auto'
    ) {
        parent::__construct($id, $name, 'thermostat', $roomId);

        $this->currentTemperature = $currentTemperature;

        // Add features
        $this->addFeature(new OnOffFeature($state));
        $this->addFeature(new TemperatureFeature($targetTemperature));
        $this->addFeature(new ModeFeature($mode));
    }

    // Backward compatibility methods
    public function getTemperature(): float
    {
        return $this->currentTemperature;
    }

    public function setTemperature(float $temperature): void
    {
        $this->currentTemperature = $temperature;
    }

    public function getTargetTemperature(): float
    {
        $feature = $this->getFeature('temperature');
        return $feature ? $feature->getValue() : 22.0;
    }

    public function setTargetTemperature(float $targetTemperature): void
    {
        $feature = $this->getFeature('temperature');
        if ($feature) {
            $feature->setValue($targetTemperature);
        }
    }

    public function getMode(): string
    {
        $feature = $this->getFeature('mode');
        return $feature ? $feature->getValue() : 'auto';
    }

    public function setMode(string $mode): void
    {
        $feature = $this->getFeature('mode');
        if ($feature) {
            $feature->setValue($mode);
        }
    }

    public function toArray(): array
    {
        $features = [];
        foreach ($this->features as $feature) {
            $features[$feature->getId()] = $feature->toArray();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'roomId' => $this->roomId,
            'state' => $this->getState(), // Backward compatibility
            'temperature' => $this->currentTemperature, // Backward compatibility
            'targetTemperature' => $this->getTargetTemperature(), // Backward compatibility
            'mode' => $this->getMode(), // Backward compatibility
            'features' => $features,
        ];
    }
}
