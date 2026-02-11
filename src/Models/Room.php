<?php

declare(strict_types=1);

namespace HomeControl\Models;

class Room
{
    private int $id;
    private string $name;
    private array $devices;

    public function __construct(int $id, string $name, array $devices = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->devices = $devices;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDevices(): array
    {
        return $this->devices;
    }

    public function addDevice(Device $device): void
    {
        $this->devices[] = $device;
    }

    public function toArray(bool $includeDevices = false): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'deviceCount' => count($this->devices),
        ];

        if ($includeDevices) {
            $data['devices'] = array_map(function (Device $device) {
                return $device->toArray();
            }, $this->devices);
        }

        return $data;
    }
}
