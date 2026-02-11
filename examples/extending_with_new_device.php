<?php

/**
 * Example: Adding a New Device Type - Smart Lock
 * 
 * This example demonstrates how to extend the Smart Home API
 * with a new device type while maintaining the existing architecture.
 */

declare(strict_types=1);

namespace HomeControl\Models;

/**
 * Smart Lock Device
 * 
 * Represents a smart lock with lock/unlock functionality
 * and access code management.
 */
class SmartLock extends Device
{
    private bool $locked;
    private ?string $accessCode;
    private array $accessLog;

    public function __construct(
        int $id,
        string $name,
        bool $state,
        int $roomId,
        bool $locked = true,
        ?string $accessCode = null
    ) {
        parent::__construct($id, $name, 'smart_lock', $state, $roomId);
        $this->locked = $locked;
        $this->accessCode = $accessCode;
        $this->accessLog = [];
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function lock(): void
    {
        $this->locked = true;
        $this->logAccess('locked');
    }

    public function unlock(): void
    {
        $this->locked = false;
        $this->logAccess('unlocked');
    }

    public function setAccessCode(string $code): void
    {
        if (strlen($code) < 4 || strlen($code) > 8) {
            throw new \InvalidArgumentException('Access code must be between 4 and 8 characters');
        }
        $this->accessCode = $code;
    }

    public function verifyAccessCode(string $code): bool
    {
        if ($this->accessCode === null) {
            return false;
        }
        return $this->accessCode === $code;
    }

    private function logAccess(string $action): void
    {
        $this->accessLog[] = [
            'action' => $action,
            'timestamp' => time(),
        ];
    }

    public function getAccessLog(): array
    {
        return $this->accessLog;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'state' => $this->state,
            'roomId' => $this->roomId,
            'locked' => $this->locked,
            'hasAccessCode' => $this->accessCode !== null,
            'accessLogCount' => count($this->accessLog),
        ];
    }
}

/*
 * To integrate this new device type into the API:
 * 
 * 1. Add the SmartLock class to src/Models/SmartLock.php
 * 
 * 2. Update HomeRepository to include SmartLock devices:
 * 
 *    use HomeControl\Models\SmartLock;
 *    
 *    $this->devices[7] = new SmartLock(7, 'Front Door Lock', true, 1, true, '1234');
 * 
 * 3. Add validation for lock-specific settings in Validator:
 * 
 *    public function validateAccessCode(string $code): bool
 *    {
 *        $length = strlen($code);
 *        if ($length < 4 || $length > 8) {
 *            $this->errors[] = 'Access code must be between 4 and 8 characters';
 *            return false;
 *        }
 *        return true;
 *    }
 * 
 * 4. Update the PATCH /settings endpoint in index.php:
 * 
 *    // Update SmartLock settings
 *    if ($device instanceof SmartLock) {
 *        if (isset($data['locked'])) {
 *            if ($data['locked']) {
 *                $device->lock();
 *            } else {
 *                $device->unlock();
 *            }
 *        }
 *        
 *        if (isset($data['accessCode'])) {
 *            if (!$validator->validateAccessCode($data['accessCode'])) {
 *                return new Response(['errors' => $validator->getErrors()], 400);
 *            }
 *            $device->setAccessCode($data['accessCode']);
 *        }
 *    }
 * 
 * 5. Optionally add a new endpoint for lock-specific operations:
 * 
 *    // POST /lock - Lock a smart lock
 *    $router->post('/lock', function () use ($repository): Response {
 *        $data = JsonMiddleware::getRequestData();
 *        
 *        if (!isset($data['deviceId'])) {
 *            return new Response(['error' => 'Device ID is required'], 400);
 *        }
 *        
 *        $device = $repository->getDeviceById((int)$data['deviceId']);
 *        
 *        if (!$device) {
 *            return new Response(['error' => 'Device not found'], 404);
 *        }
 *        
 *        if (!$device instanceof SmartLock) {
 *            return new Response(['error' => 'Device is not a smart lock'], 400);
 *        }
 *        
 *        $device->lock();
 *        $repository->updateDevice($device);
 *        
 *        return new Response([
 *            'message' => 'Device locked successfully',
 *            'device' => $device->toArray(),
 *        ]);
 *    });
 * 
 * That's it! The new device type is now fully integrated with the API
 * while maintaining the existing clean architecture.
 */
