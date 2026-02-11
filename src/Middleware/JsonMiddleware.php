<?php

declare(strict_types=1);

namespace HomeControl\Middleware;

class JsonMiddleware
{
    public static function apply(): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    public static function getRequestData(): array
    {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            return [];
        }

        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        return $data;
    }
}
