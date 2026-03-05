<?php

declare(strict_types=1);

namespace HomeControl\Models;

use HomeControl\Features\Feature;

abstract class Device
{
    protected int $id;
    protected string $name;
    protected string $type;
    protected int $roomId;
    protected array $features;

    public function __construct(int $id, string $name, string $type, int $roomId, array $features = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->roomId = $roomId;
        $this->features = $features;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    // Backward compatibility - get state from on_off feature if present
    public function getState(): bool
    {
        $onOffFeature = $this->getFeature('on_off');
        return $onOffFeature ? $onOffFeature->getValue() : false;
    }

    // Backward compatibility - set state via on_off feature if present
    public function setState(bool $state): void
    {
        $onOffFeature = $this->getFeature('on_off');
        if ($onOffFeature) {
            $onOffFeature->setValue($state);
        }
    }

    public function toggle(): void
    {
        $state = $this->getState();
        $this->setState(!$state);
    }

    public function getRoomId(): int
    {
        return $this->roomId;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function getFeature(string $id): ?Feature
    {
        return $this->features[$id] ?? null;
    }

    public function addFeature(Feature $feature): void
    {
        $this->features[$feature->getId()] = $feature;
    }

    public function removeFeature(string $id): void
    {
        unset($this->features[$id]);
    }

    public function hasFeature(string $id): bool
    {
        return isset($this->features[$id]);
    }

    abstract public function toArray(): array;
}
        $this->state = !$this->state;
    }

    public function getRoomId(): int
    {
        return $this->roomId;
    }

    abstract public function toArray(): array;
}
