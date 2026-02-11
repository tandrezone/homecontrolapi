# API Testing Examples

This document provides examples of testing all API endpoints using curl.

## Prerequisites

Start the development server:
```bash
cd public
php -S localhost:8080 index.php
```

## Test Commands

### 1. Health Check
```bash
curl -X GET http://localhost:8080/health | jq .
```

### 2. Get All Rooms
```bash
curl -X GET http://localhost:8080/rooms | jq .
```

### 3. Get Specific Room
```bash
# Get Living Room (ID: 1)
curl -X GET http://localhost:8080/rooms/1 | jq .

# Get Bedroom (ID: 2)
curl -X GET http://localhost:8080/rooms/2 | jq .

# Get Kitchen (ID: 3)
curl -X GET http://localhost:8080/rooms/3 | jq .
```

### 4. Get Room Devices
```bash
# Get devices in Living Room
curl -X GET http://localhost:8080/rooms/1/devices | jq .
```

### 5. Toggle Device
```bash
# Toggle Ceiling Light (ID: 1)
curl -X POST http://localhost:8080/toggle \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 1}' | jq .
```

### 6. Update Device State
```bash
# Turn on Desk Lamp (ID: 2)
curl -X PUT http://localhost:8080/state \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 2, "state": true}' | jq .

# Turn off Desk Lamp
curl -X PUT http://localhost:8080/state \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 2, "state": false}' | jq .
```

### 7. Update Light Settings
```bash
# Update brightness
curl -X PATCH http://localhost:8080/settings \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 1, "brightness": 75}' | jq .

# Update color
curl -X PATCH http://localhost:8080/settings \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 1, "color": "#00FF00"}' | jq .

# Update both brightness and color
curl -X PATCH http://localhost:8080/settings \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 1, "brightness": 50, "color": "#FF0000"}' | jq .
```

### 8. Update Thermostat Settings
```bash
# Update target temperature
curl -X PATCH http://localhost:8080/settings \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 3, "targetTemperature": 23.5}' | jq .

# Update mode
curl -X PATCH http://localhost:8080/settings \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 3, "mode": "cool"}' | jq .

# Update both temperature and mode
curl -X PATCH http://localhost:8080/settings \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 3, "targetTemperature": 24.5, "mode": "heat"}' | jq .
```

## Error Testing

### Invalid Device ID
```bash
curl -X POST http://localhost:8080/toggle \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 999}' | jq .
```

Expected response (404):
```json
{
  "error": "Device not found"
}
```

### Invalid Brightness
```bash
curl -X PATCH http://localhost:8080/settings \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 1, "brightness": 150}' | jq .
```

Expected response (400):
```json
{
  "errors": [
    "Brightness must be between 0 and 100"
  ]
}
```

### Invalid Color Format
```bash
curl -X PATCH http://localhost:8080/settings \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 1, "color": "red"}' | jq .
```

Expected response (400):
```json
{
  "errors": [
    "Color must be a valid hex color code (e.g., #FFFFFF)"
  ]
}
```

### Invalid Temperature
```bash
curl -X PATCH http://localhost:8080/settings \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 3, "targetTemperature": 50}' | jq .
```

Expected response (400):
```json
{
  "errors": [
    "Temperature must be between 10 and 35"
  ]
}
```

### Invalid Mode
```bash
curl -X PATCH http://localhost:8080/settings \
  -H "Content-Type: application/json" \
  -d '{"deviceId": 3, "mode": "invalid"}' | jq .
```

Expected response (400):
```json
{
  "errors": [
    "Invalid mode. Must be one of: auto, heat, cool, off"
  ]
}
```

### Missing Required Field
```bash
curl -X POST http://localhost:8080/toggle \
  -H "Content-Type: application/json" \
  -d '{}' | jq .
```

Expected response (400):
```json
{
  "error": "Device ID is required"
}
```

### Invalid Room ID
```bash
curl -X GET http://localhost:8080/rooms/999 | jq .
```

Expected response (404):
```json
{
  "error": "Room not found"
}
```

## Mock Data Reference

### Rooms
- **ID 1**: Living Room (3 devices)
- **ID 2**: Bedroom (2 devices)
- **ID 3**: Kitchen (1 device)

### Devices
- **ID 1**: Ceiling Light (Light in Living Room)
- **ID 2**: Desk Lamp (Light in Living Room)
- **ID 3**: Main Thermostat (Thermostat in Living Room)
- **ID 4**: Bedside Light (Light in Bedroom)
- **ID 5**: Bedroom Thermostat (Thermostat in Bedroom)
- **ID 6**: Kitchen Light (Light in Kitchen)
