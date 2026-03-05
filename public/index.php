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
use HomeControl\Features\FeatureRegistry;
use HomeControl\Features\OnOffFeature;
use HomeControl\Features\BrightnessFeature;
use HomeControl\Features\ColorFeature;
use HomeControl\Features\TemperatureFeature;
use HomeControl\Features\ModeFeature;

// Register available features
FeatureRegistry::register(OnOffFeature::class);
FeatureRegistry::register(BrightnessFeature::class);
FeatureRegistry::register(ColorFeature::class);
FeatureRegistry::register(TemperatureFeature::class);
FeatureRegistry::register(ModeFeature::class);

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

// GET /features - Get all available features
$router->get('/features', function (): Response {
    return new Response([
        'features' => FeatureRegistry::getAllFeatureInfo(),
    ]);
});

// GET /features/{id} - Get a specific feature
$router->get('/features/{id}', function (string $id): Response {
    $feature = FeatureRegistry::getFeatureInfo($id);
    if (!$feature) {
        return new Response(['error' => 'Feature not found'], 404);
    }
    return new Response(['feature' => $feature]);
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

    if (!$validator->validateDeviceId($data['deviceId'])) {
        return new Response(['errors' => $validator->getErrors()], 400);
    }

    // For backward compatibility, accept 'state' parameter
    if (isset($data['state'])) {
        $data['on_off'] = $data['state'];
    }

    $device = $repository->getDeviceById((int)$data['deviceId']);

    if (!$device) {
        return new Response(['error' => 'Device not found'], 404);
    }

    try {
        $feature = $device->getFeature('on_off');
        if (!$feature) {
            return new Response(['error' => 'Device does not support on/off functionality'], 400);
        }

        $feature->setValue($data['on_off'] ?? $data['state']);
        $repository->updateDevice($device);

        return new Response([
            'message' => 'Device state updated successfully',
            'device' => $device->toArray(),
        ]);
    } catch (\InvalidArgumentException $e) {
        return new Response(['error' => $e->getMessage()], 400);
    }
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
        // Update features dynamically
        foreach ($data as $key => $value) {
            if ($key === 'deviceId') {
                continue; // Skip deviceId
            }

            $feature = $device->getFeature($key);
            if ($feature) {
                $feature->setValue($value);
            } else {
                return new Response(['error' => "Feature '{$key}' not supported by this device"], 400);
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
