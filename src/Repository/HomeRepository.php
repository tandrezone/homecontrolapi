<?php

declare(strict_types=1);

namespace HomeControl\Repository;

use HomeControl\Models\Device;
use HomeControl\Models\Light;
use HomeControl\Models\Thermostat;
use HomeControl\Models\Room;

class HomeRepository
{
    private array $rooms;
    private array $devices;

    public function __construct()
    {
        $this->initializeMockData();
    }

    private function initializeMockData(): void
    {
        // Create mock devices
        $this->devices = [
            1 => new Light(1, 'Ceiling Light', true, 1, 80, '#FFFF00'),
            2 => new Light(2, 'Desk Lamp', false, 1, 60, '#FFFFFF'),
            3 => new Thermostat(3, 'Main Thermostat', true, 1, 21.5, 22.0, 'heat'),
            4 => new Light(4, 'Bedside Light', true, 2, 40, '#FFE4B5'),
            5 => new Thermostat(5, 'Bedroom Thermostat', false, 2, 19.0, 20.0, 'auto'),
            6 => new Light(6, 'Kitchen Light', true, 3, 100, '#FFFFFF'),
        ];

        // Create rooms
        $this->rooms = [
            1 => new Room(1, 'Living Room'),
            2 => new Room(2, 'Bedroom'),
            3 => new Room(3, 'Kitchen'),
        ];

        // Associate devices with rooms
        foreach ($this->devices as $device) {
            $roomId = $device->getRoomId();
            if (isset($this->rooms[$roomId])) {
                $this->rooms[$roomId]->addDevice($device);
            }
        }
    }

    public function getAllRooms(): array
    {
        return array_values($this->rooms);
    }

    public function getRoomById(int $id): ?Room
    {
        return $this->rooms[$id] ?? null;
    }

    public function getDeviceById(int $id): ?Device
    {
        return $this->devices[$id] ?? null;
    }

    public function getAllDevices(): array
    {
        return array_values($this->devices);
    }

    public function getDevicesByRoomId(int $roomId): array
    {
        $devices = [];
        foreach ($this->devices as $device) {
            if ($device->getRoomId() === $roomId) {
                $devices[] = $device;
            }
        }
        return $devices;
    }

    public function updateDevice(Device $device): bool
    {
        $id = $device->getId();
        if (isset($this->devices[$id])) {
            $this->devices[$id] = $device;
            return true;
        }
        return false;
    }
}
