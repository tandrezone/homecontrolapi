<?php

/**
 * Simple Test Runner
 * 
 * This demonstrates basic testing without external dependencies.
 * In production, you would use PHPUnit or similar testing framework.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use HomeControl\Models\Light;
use HomeControl\Models\Thermostat;
use HomeControl\Models\Room;
use HomeControl\Repository\HomeRepository;
use HomeControl\Http\Validator;

class TestRunner
{
    private int $passed = 0;
    private int $failed = 0;
    private array $failures = [];

    public function test(string $name, callable $testFunc): void
    {
        try {
            $testFunc();
            $this->passed++;
            echo "✓ {$name}\n";
        } catch (\Exception $e) {
            $this->failed++;
            $this->failures[] = $name . ': ' . $e->getMessage();
            echo "✗ {$name}\n";
        }
    }

    public function assert(bool $condition, string $message = 'Assertion failed'): void
    {
        if (!$condition) {
            throw new \Exception($message);
        }
    }

    public function assertEquals($expected, $actual, string $message = 'Values not equal'): void
    {
        if ($expected !== $actual) {
            throw new \Exception("{$message}: expected " . json_encode($expected) . " but got " . json_encode($actual));
        }
    }

    public function printSummary(): void
    {
        echo "\n" . str_repeat('=', 50) . "\n";
        echo "Test Summary\n";
        echo str_repeat('=', 50) . "\n";
        echo "Passed: {$this->passed}\n";
        echo "Failed: {$this->failed}\n";
        
        if (!empty($this->failures)) {
            echo "\nFailures:\n";
            foreach ($this->failures as $failure) {
                echo "  - {$failure}\n";
            }
        }
        
        echo str_repeat('=', 50) . "\n";
        
        if ($this->failed === 0) {
            echo "All tests passed! ✓\n";
        } else {
            echo "Some tests failed.\n";
            exit(1);
        }
    }
}

$test = new TestRunner();

echo "Running Smart Home API Tests...\n\n";

// Light Model Tests
$test->test('Light: Create with default values', function () use ($test) {
    $light = new Light(1, 'Test Light', true, 1);
    $test->assertEquals(1, $light->getId());
    $test->assertEquals('Test Light', $light->getName());
    $test->assertEquals(true, $light->getState());
    $test->assertEquals(100, $light->getBrightness());
    $test->assertEquals('#FFFFFF', $light->getColor());
});

$test->test('Light: Toggle state', function () use ($test) {
    $light = new Light(1, 'Test Light', true, 1);
    $test->assertEquals(true, $light->getState());
    $light->toggle();
    $test->assertEquals(false, $light->getState());
    $light->toggle();
    $test->assertEquals(true, $light->getState());
});

$test->test('Light: Set brightness', function () use ($test) {
    $light = new Light(1, 'Test Light', true, 1);
    $light->setBrightness(50);
    $test->assertEquals(50, $light->getBrightness());
});

$test->test('Light: Invalid brightness throws exception', function () use ($test) {
    $light = new Light(1, 'Test Light', true, 1);
    try {
        $light->setBrightness(150);
        throw new \Exception('Should have thrown exception for invalid brightness');
    } catch (\InvalidArgumentException $e) {
        $test->assert(true);
    }
});

$test->test('Light: Set color', function () use ($test) {
    $light = new Light(1, 'Test Light', true, 1);
    $light->setColor('#FF0000');
    $test->assertEquals('#FF0000', $light->getColor());
});

$test->test('Light: Invalid color throws exception', function () use ($test) {
    $light = new Light(1, 'Test Light', true, 1);
    try {
        $light->setColor('red');
        throw new \Exception('Should have thrown exception for invalid color');
    } catch (\InvalidArgumentException $e) {
        $test->assert(true);
    }
});

// Thermostat Model Tests
$test->test('Thermostat: Create with default values', function () use ($test) {
    $thermostat = new Thermostat(1, 'Test Thermostat', true, 1);
    $test->assertEquals(1, $thermostat->getId());
    $test->assertEquals('Test Thermostat', $thermostat->getName());
    $test->assertEquals(true, $thermostat->getState());
    $test->assertEquals(20.0, $thermostat->getTemperature());
    $test->assertEquals(22.0, $thermostat->getTargetTemperature());
    $test->assertEquals('auto', $thermostat->getMode());
});

$test->test('Thermostat: Set target temperature', function () use ($test) {
    $thermostat = new Thermostat(1, 'Test Thermostat', true, 1);
    $thermostat->setTargetTemperature(25.0);
    $test->assertEquals(25.0, $thermostat->getTargetTemperature());
});

$test->test('Thermostat: Invalid temperature throws exception', function () use ($test) {
    $thermostat = new Thermostat(1, 'Test Thermostat', true, 1);
    try {
        $thermostat->setTargetTemperature(50.0);
        throw new \Exception('Should have thrown exception for invalid temperature');
    } catch (\InvalidArgumentException $e) {
        $test->assert(true);
    }
});

$test->test('Thermostat: Set mode', function () use ($test) {
    $thermostat = new Thermostat(1, 'Test Thermostat', true, 1);
    $thermostat->setMode('heat');
    $test->assertEquals('heat', $thermostat->getMode());
});

$test->test('Thermostat: Invalid mode throws exception', function () use ($test) {
    $thermostat = new Thermostat(1, 'Test Thermostat', true, 1);
    try {
        $thermostat->setMode('invalid');
        throw new \Exception('Should have thrown exception for invalid mode');
    } catch (\InvalidArgumentException $e) {
        $test->assert(true);
    }
});

// Room Model Tests
$test->test('Room: Create empty room', function () use ($test) {
    $room = new Room(1, 'Test Room');
    $test->assertEquals(1, $room->getId());
    $test->assertEquals('Test Room', $room->getName());
    $test->assertEquals(0, count($room->getDevices()));
});

$test->test('Room: Add devices', function () use ($test) {
    $room = new Room(1, 'Test Room');
    $light = new Light(1, 'Test Light', true, 1);
    $room->addDevice($light);
    $test->assertEquals(1, count($room->getDevices()));
});

// Repository Tests
$test->test('Repository: Get all rooms', function () use ($test) {
    $repo = new HomeRepository();
    $rooms = $repo->getAllRooms();
    $test->assertEquals(3, count($rooms));
});

$test->test('Repository: Get room by ID', function () use ($test) {
    $repo = new HomeRepository();
    $room = $repo->getRoomById(1);
    $test->assert($room !== null);
    $test->assertEquals('Living Room', $room->getName());
});

$test->test('Repository: Get non-existent room', function () use ($test) {
    $repo = new HomeRepository();
    $room = $repo->getRoomById(999);
    $test->assert($room === null);
});

$test->test('Repository: Get device by ID', function () use ($test) {
    $repo = new HomeRepository();
    $device = $repo->getDeviceById(1);
    $test->assert($device !== null);
    $test->assertEquals('Ceiling Light', $device->getName());
});

$test->test('Repository: Get devices by room ID', function () use ($test) {
    $repo = new HomeRepository();
    $devices = $repo->getDevicesByRoomId(1);
    $test->assertEquals(3, count($devices));
});

// Validator Tests
$test->test('Validator: Valid device ID', function () use ($test) {
    $validator = new Validator();
    $test->assert($validator->validateDeviceId(1));
    $test->assert(!$validator->hasErrors());
});

$test->test('Validator: Invalid device ID', function () use ($test) {
    $validator = new Validator();
    $test->assert(!$validator->validateDeviceId(-1));
    $test->assert($validator->hasErrors());
});

$test->test('Validator: Valid brightness', function () use ($test) {
    $validator = new Validator();
    $test->assert($validator->validateBrightness(50));
    $test->assert(!$validator->hasErrors());
});

$test->test('Validator: Invalid brightness', function () use ($test) {
    $validator = new Validator();
    $test->assert(!$validator->validateBrightness(150));
    $test->assert($validator->hasErrors());
});

$test->test('Validator: Valid color', function () use ($test) {
    $validator = new Validator();
    $test->assert($validator->validateColor('#FF0000'));
    $test->assert(!$validator->hasErrors());
});

$test->test('Validator: Invalid color', function () use ($test) {
    $validator = new Validator();
    $test->assert(!$validator->validateColor('red'));
    $test->assert($validator->hasErrors());
});

$test->test('Validator: Valid temperature', function () use ($test) {
    $validator = new Validator();
    $test->assert($validator->validateTemperature(22.5));
    $test->assert(!$validator->hasErrors());
});

$test->test('Validator: Invalid temperature', function () use ($test) {
    $validator = new Validator();
    $test->assert(!$validator->validateTemperature(50));
    $test->assert($validator->hasErrors());
});

$test->test('Validator: Valid mode', function () use ($test) {
    $validator = new Validator();
    $test->assert($validator->validateMode('heat'));
    $test->assert(!$validator->hasErrors());
});

$test->test('Validator: Invalid mode', function () use ($test) {
    $validator = new Validator();
    $test->assert(!$validator->validateMode('invalid'));
    $test->assert($validator->hasErrors());
});

$test->printSummary();
