# Smart Home API

A professional PHP Smart Home API built with PSR-12 standards, featuring a router-based architecture for managing smart home devices.

## Features

- ✅ **PSR-12 Compliant** - Follows PHP coding standards
- ✅ **Type-Hinting** - Full type declarations for better code quality
- ✅ **Router-Based Architecture** - Clean URL routing with parameter support
- ✅ **JSON Middleware** - Automatic JSON headers and CORS support
- ✅ **Input Validation** - Robust validation with meaningful error messages
- ✅ **HTTP Status Codes** - Proper status codes for all responses
- ✅ **Mock Repository** - Decoupled data layer for easy testing
- ✅ **Multiple Device Types** - Support for lights, thermostats, and more
- ✅ **Modular Design** - Clean, maintainable code structure

## Project Structure

```
homecontrolapi/
├── src/
│   ├── Http/
│   │   ├── Router.php       # URL routing and request dispatching
│   │   ├── Response.php     # HTTP response handling
│   │   └── Validator.php    # Input validation
│   ├── Middleware/
│   │   └── JsonMiddleware.php  # JSON headers and CORS
│   ├── Models/
│   │   ├── Device.php       # Base device class
│   │   ├── Light.php        # Light device implementation
│   │   ├── Thermostat.php   # Thermostat device implementation
│   │   └── Room.php         # Room model
│   └── Repository/
│       └── HomeRepository.php  # Mock data storage
├── public/
│   └── index.php            # API entry point
└── composer.json            # Dependencies and autoloading
```

## Installation

1. Clone the repository:
```bash
git clone https://github.com/tandrezone/homecontrolapi.git
cd homecontrolapi
```

2. Install dependencies:
```bash
composer install
```

3. Start the development server:
```bash
cd public
php -S localhost:8080 index.php
```

## API Endpoints

### Health Check
**GET /health**

Check API health status.

**Response:**
```json
{
  "status": "healthy",
  "timestamp": 1770822851,
  "service": "Smart Home API",
  "version": "1.0.0"
}
```

---

### Get All Rooms
**GET /rooms**

Retrieve all rooms with device counts.

**Response:**
```json
{
  "rooms": [
    {
      "id": 1,
      "name": "Living Room",
      "deviceCount": 3
    }
  ]
}
```

---

### Get Specific Room
**GET /rooms/{id}**

Get detailed information about a specific room including all devices.

**Response:**
```json
{
  "room": {
    "id": 1,
    "name": "Living Room",
    "deviceCount": 3,
    "devices": [
      {
        "id": 1,
        "name": "Ceiling Light",
        "type": "light",
        "state": true,
        "roomId": 1,
        "brightness": 80,
        "color": "#FFFF00"
      }
    ]
  }
}
```

---

### Get Room Devices
**GET /rooms/{id}/devices**

Get all devices in a specific room.

**Response:**
```json
{
  "devices": [
    {
      "id": 1,
      "name": "Ceiling Light",
      "type": "light",
      "state": true,
      "roomId": 1,
      "brightness": 80,
      "color": "#FFFF00"
    }
  ]
}
```

---

### Toggle Device
**POST /toggle**

Toggle a device's state (on/off).

**Request:**
```json
{
  "deviceId": 1
}
```

**Response:**
```json
{
  "message": "Device toggled successfully",
  "device": {
    "id": 1,
    "name": "Ceiling Light",
    "type": "light",
    "state": false,
    "roomId": 1,
    "brightness": 80,
    "color": "#FFFF00"
  }
}
```

---

### Update Device State
**PUT /state**

Explicitly set a device's state.

**Request:**
```json
{
  "deviceId": 2,
  "state": true
}
```

**Response:**
```json
{
  "message": "Device state updated successfully",
  "device": {
    "id": 2,
    "name": "Desk Lamp",
    "type": "light",
    "state": true,
    "roomId": 1,
    "brightness": 60,
    "color": "#FFFFFF"
  }
}
```

---

### Update Device Settings
**PATCH /settings**

Update device-specific settings (brightness, color, temperature, mode, etc.).

**For Lights:**
```json
{
  "deviceId": 1,
  "brightness": 50,
  "color": "#FF0000"
}
```

**For Thermostats:**
```json
{
  "deviceId": 3,
  "targetTemperature": 24.5,
  "mode": "cool"
}
```

**Response:**
```json
{
  "message": "Device settings updated successfully",
  "device": {
    "id": 1,
    "name": "Ceiling Light",
    "type": "light",
    "state": true,
    "roomId": 1,
    "brightness": 50,
    "color": "#FF0000"
  }
}
```

---

## Device Types

### Light
Properties:
- `brightness` (0-100): Light intensity
- `color` (hex): Color in hex format (e.g., #FFFFFF)

### Thermostat
Properties:
- `temperature` (float): Current temperature
- `targetTemperature` (10-35): Desired temperature
- `mode` (string): Operating mode (auto, heat, cool, off)

## Validation Rules

- **Device ID**: Must be a positive integer
- **Room ID**: Must be a positive integer
- **State**: Must be a boolean value
- **Brightness**: Must be between 0 and 100
- **Color**: Must be a valid hex color code (#RRGGBB)
- **Temperature**: Must be between 10°C and 35°C
- **Mode**: Must be one of: auto, heat, cool, off

## Error Responses

### 400 Bad Request
Invalid input or validation error:
```json
{
  "errors": [
    "Brightness must be between 0 and 100"
  ]
}
```

### 404 Not Found
Resource not found:
```json
{
  "error": "Device not found"
}
```

## Code Quality

Check PSR-12 compliance:
```bash
composer lint
```

Auto-fix code style issues:
```bash
composer lint-fix
```

## Development

### Adding a New Device Type

1. Create a new class extending `Device`:
```php
<?php

declare(strict_types=1);

namespace HomeControl\Models;

class SecurityCamera extends Device
{
    private bool $recording;

    public function __construct(
        int $id,
        string $name,
        bool $state,
        int $roomId,
        bool $recording = false
    ) {
        parent::__construct($id, $name, 'security_camera', $state, $roomId);
        $this->recording = $recording;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'state' => $this->state,
            'roomId' => $this->roomId,
            'recording' => $this->recording,
        ];
    }
}
```

2. Add mock data in `HomeRepository`
3. Update validation rules if needed in `Validator`
4. Handle settings in the `/settings` endpoint

## Requirements

- PHP >= 8.0
- Composer

## License

MIT