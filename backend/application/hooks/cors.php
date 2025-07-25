<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CorsHook
 * Handles CORS headers dynamically based on environment
 */
class CorsHook {

    public function handle()
    {
        $allowedOrigins = [];

        if (ENVIRONMENT === 'development') {
            // Allow everything in development
            $allowedOrigins = ['*'];
        } else {
            // Restrict in production
            $allowedOrigins = ['https://localhost:5173/'];
        }

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        printf($origin);
        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: " . ($allowedOrigins[0] === '*' ? '*' : $origin));
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
            header("Access-Control-Allow-Credentials: true");
        }

        // Handle OPTIONS preflight request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
    }
}
