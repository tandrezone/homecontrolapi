<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use HomeControl\Http\Router;
use HomeControl\Http\Response;
use HomeControl\Http\Validator;
use HomeControl\Middleware\JsonMiddleware;
use HomeControl\Repository\HomeRepository;
use HomeControl\Models\Light;
use HomeControl\Models\Thermostat;

// Apply JSON middleware
JsonMiddleware::apply();

// Initialize dependencies
$router = new Router();
$repository = new HomeRepository();

// GET /health - Health check endpoint
$router->get('/health', function () use ($repository): Response {
    return new Response([
        'status' => 'healthy',
        'timestamp' => time(),
        'service' => 'Smart Home API',
        'version' => '1.0.0',
    ]);
});

// GET /rooms - Get all rooms
$router->get('/rooms', function () use ($repository): Response {
    $rooms = $repository->getAllRooms();
    $roomsData = array_map(function ($room) {
        return $room->toArray();
    }, $rooms);

    return new Response(['rooms' => $roomsData]);
});

// GET /rooms/{id} - Get a specific room
$router->get('/rooms/{id}', function (string $id) use ($repository): Response {
    $validator = new Validator();

    if (!$validator->validateRoomId($id)) {
        return new Response(['errors' => $validator->getErrors()], 400);
    }

    $room = $repository->getRoomById((int)$id);

    if (!$room) {
        return new Response(['error' => 'Room not found'], 404);
    }

    return new Response(['room' => $room->toArray(true)]);
});

// GET /rooms/{id}/devices - Get devices in a room
$router->get('/rooms/{id}/devices', function (string $id) use ($repository): Response {
    $validator = new Validator();

    if (!$validator->validateRoomId($id)) {
        return new Response(['errors' => $validator->getErrors()], 400);
    }

    $room = $repository->getRoomById((int)$id);

    if (!$room) {
        return new Response(['error' => 'Room not found'], 404);
    }

    $devices = array_map(function ($device) {
        return $device->toArray();
    }, $room->getDevices());

    return new Response(['devices' => $devices]);
});

// POST /toggle - Toggle device state
$router->post('/toggle', function () use ($repository): Response {
    $data = JsonMiddleware::getRequestData();
    $validator = new Validator();

    if (!isset($data['deviceId'])) {
        return new Response(['error' => 'Device ID is required'], 400);
    }

    if (!$validator->validateDeviceId($data['deviceId'])) {
        return new Response(['errors' => $validator->getErrors()], 400);
    }

    $device = $repository->getDeviceById((int)$data['deviceId']);

    if (!$device) {
        return new Response(['error' => 'Device not found'], 404);
    }

    $device->toggle();
    $repository->updateDevice($device);

    return new Response([
        'message' => 'Device toggled successfully',
        'device' => $device->toArray(),
    ]);
});

// PUT /state - Update device state
$router->put('/state', function () use ($repository): Response {
    $data = JsonMiddleware::getRequestData();
    $validator = new Validator();

    if (!isset($data['deviceId'])) {
        return new Response(['error' => 'Device ID is required'], 400);
    }

    if (!$validator->validateDeviceId($data['deviceId']) || !$validator->validateState($data)) {
        return new Response(['errors' => $validator->getErrors()], 400);
    }

    $device = $repository->getDeviceById((int)$data['deviceId']);

    if (!$device) {
        return new Response(['error' => 'Device not found'], 404);
    }

    $device->setState($data['state']);
    $repository->updateDevice($device);

    return new Response([
        'message' => 'Device state updated successfully',
        'device' => $device->toArray(),
    ]);
});

// PATCH /settings - Update device settings (brightness, color, temperature, etc.)
$router->patch('/settings', function () use ($repository): Response {
    $data = JsonMiddleware::getRequestData();
    $validator = new Validator();

    if (!isset($data['deviceId'])) {
        return new Response(['error' => 'Device ID is required'], 400);
    }

    if (!$validator->validateDeviceId($data['deviceId'])) {
        return new Response(['errors' => $validator->getErrors()], 400);
    }

    $device = $repository->getDeviceById((int)$data['deviceId']);

    if (!$device) {
        return new Response(['error' => 'Device not found'], 404);
    }

    try {
        // Update Light settings
        if ($device instanceof Light) {
            if (isset($data['brightness'])) {
                if (!$validator->validateBrightness($data['brightness'])) {
                    return new Response(['errors' => $validator->getErrors()], 400);
                }
                $device->setBrightness((int)$data['brightness']);
            }

            if (isset($data['color'])) {
                if (!$validator->validateColor($data['color'])) {
                    return new Response(['errors' => $validator->getErrors()], 400);
                }
                $device->setColor($data['color']);
            }
        }

        // Update Thermostat settings
        if ($device instanceof Thermostat) {
            if (isset($data['targetTemperature'])) {
                if (!$validator->validateTemperature($data['targetTemperature'])) {
                    return new Response(['errors' => $validator->getErrors()], 400);
                }
                $device->setTargetTemperature((float)$data['targetTemperature']);
            }

            if (isset($data['mode'])) {
                if (!$validator->validateMode($data['mode'])) {
                    return new Response(['errors' => $validator->getErrors()], 400);
                }
                $device->setMode($data['mode']);
            }
        }

        $repository->updateDevice($device);

        return new Response([
            'message' => 'Device settings updated successfully',
            'device' => $device->toArray(),
        ]);
    } catch (\InvalidArgumentException $e) {
        return new Response(['error' => $e->getMessage()], 400);
    }
});

// Dispatch the request
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$response = $router->dispatch($method, $uri);
$response->send();
