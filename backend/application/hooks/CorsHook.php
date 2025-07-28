<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CorsHook
 * Handles CORS headers dynamically based on environment
 */
class CorsHook {

    public function handle()
    {
        // Get frontend URL from ENV or default for dev
        $allowedOrigins = (ENVIRONMENT === 'development')
            ? ['*']
            : [getenv('FRONTEND_URL')];

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        // Allow if origin matches allowed or in dev mode
        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: " . ($allowedOrigins[0] === '*' ? '*' : $origin));
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
            header("Access-Control-Allow-Credentials: true");
        }

        // Respond to preflight request immediately
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
