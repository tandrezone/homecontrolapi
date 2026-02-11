<?php

declare(strict_types=1);

namespace HomeControl\Http;

class Validator
{
    private array $errors = [];

    public function validateDeviceId(mixed $id): bool
    {
        if (!is_numeric($id) || (int)$id <= 0) {
            $this->errors[] = 'Invalid device ID';
            return false;
        }
        return true;
    }

    public function validateRoomId(mixed $id): bool
    {
        if (!is_numeric($id) || (int)$id <= 0) {
            $this->errors[] = 'Invalid room ID';
            return false;
        }
        return true;
    }

    public function validateState(array $data): bool
    {
        if (!isset($data['state'])) {
            $this->errors[] = 'State is required';
            return false;
        }

        if (!is_bool($data['state'])) {
            $this->errors[] = 'State must be a boolean';
            return false;
        }

        return true;
    }

    public function validateBrightness(mixed $brightness): bool
    {
        if (!is_numeric($brightness)) {
            $this->errors[] = 'Brightness must be a number';
            return false;
        }

        $brightness = (int)$brightness;
        if ($brightness < 0 || $brightness > 100) {
            $this->errors[] = 'Brightness must be between 0 and 100';
            return false;
        }

        return true;
    }

    public function validateColor(string $color): bool
    {
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            $this->errors[] = 'Color must be a valid hex color code (e.g., #FFFFFF)';
            return false;
        }
        return true;
    }

    public function validateTemperature(mixed $temperature): bool
    {
        if (!is_numeric($temperature)) {
            $this->errors[] = 'Temperature must be a number';
            return false;
        }

        $temperature = (float)$temperature;
        if ($temperature < 10 || $temperature > 35) {
            $this->errors[] = 'Temperature must be between 10 and 35';
            return false;
        }

        return true;
    }

    public function validateMode(string $mode): bool
    {
        $validModes = ['auto', 'heat', 'cool', 'off'];
        if (!in_array($mode, $validModes)) {
            $this->errors[] = 'Invalid mode. Must be one of: ' . implode(', ', $validModes);
            return false;
        }
        return true;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
