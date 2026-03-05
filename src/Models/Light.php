<?php

declare(strict_types=1);

namespace HomeControl\Models;

use HomeControl\Features\OnOffFeature;
use HomeControl\Features\BrightnessFeature;
use HomeControl\Features\ColorFeature;

class Light extends Device
{
    public function __construct(
        int $id,
        string $name,
        bool $state,
        int $roomId,
        int $brightness = 100,
        string $color = '#FFFFFF'
    ) {
        parent::__construct($id, $name, 'light', $roomId);

        // Add features
        $this->addFeature(new OnOffFeature($state));
        $this->addFeature(new BrightnessFeature($brightness));
        $this->addFeature(new ColorFeature($color));
    }

    // Backward compatibility methods
    public function getBrightness(): int
    {
        $feature = $this->getFeature('brightness');
        return $feature ? $feature->getValue() : 100;
    }

    public function setBrightness(int $brightness): void
    {
        $feature = $this->getFeature('brightness');
        if ($feature) {
            $feature->setValue($brightness);
        }
    }

    public function getColor(): string
    {
        $feature = $this->getFeature('color');
        return $feature ? $feature->getValue() : '#FFFFFF';
    }

    public function setColor(string $color): void
    {
        $feature = $this->getFeature('color');
        if ($feature) {
            $feature->setValue($color);
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
            'brightness' => $this->getBrightness(), // Backward compatibility
            'color' => $this->getColor(), // Backward compatibility
            'features' => $features,
        ];
    }
}
