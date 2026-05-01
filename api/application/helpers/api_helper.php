<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Send JSON response and exit
 */
function json_response(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Token');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function success_response($data = null, string $message = 'Success', int $code = 200): void {
    json_response([
        'status'  => true,
        'code'    => $code,
        'message' => $message,
        'data'    => $data,
    ], $code);
}

function error_response(string $message = 'Error', int $code = 400, $data = null): void {
    json_response([
        'status'  => false,
        'code'    => $code,
        'message' => $message,
        'data'    => $data,
    ], $code);
}

/**
 * Get bearer token from Authorization header
 */
function get_bearer_token(): ?string {
    $headers = apache_request_headers();
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (preg_match('/Bearer\s+(.+)/i', $auth, $m)) {
        return trim($m[1]);
    }
    // Also check X-API-Token header
    return $headers['X-API-Token'] ?? $headers['x-api-token'] ?? null;
}

/**
 * Verify API token from DB
 */
function verify_api_token(): bool {
    $token = get_bearer_token();
    if (!$token) return false;

    $CI =& get_instance();
    $row = $CI->db->get_where('api_tokens', ['token' => $token, 'status' => 1])->row_array();
    return !empty($row);
}

/**
 * Require valid token or abort
 */
function require_token(): void {
    if (!verify_api_token()) {
        error_response('Unauthorized. Provide a valid API token.', 401);
    }
}

/**
 * Get raw JSON input body as array
 */
function get_json_input(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}
