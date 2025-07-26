<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * API Helper
 * Provides utility functions for returning standardized JSON responses.
 */

/**
 * Return success response as JSON.
 *
 * @param CI_Output $output CodeIgniter output instance
 * @param array $data Response data
 * @param int $statusCode HTTP status code (default: 200)
 */
function respondSuccess($output, array $data, int $statusCode = 200)
{
    $output
        ->set_content_type('application/json')
        ->set_status_header($statusCode)
        ->set_output(json_encode(['status' => 'success'] + $data));
}

/**
 * Return error response as JSON.
 *
 * @param CI_Output $output CodeIgniter output instance
 * @param string $message Error message
 * @param int $statusCode HTTP status code (default: 400)
 */
function respondError($output, string $message, int $statusCode = 400)
{
    $output
        ->set_content_type('application/json')
        ->set_status_header($statusCode)
        ->set_output(json_encode(['status' => 'error', 'message' => $message]));
}
