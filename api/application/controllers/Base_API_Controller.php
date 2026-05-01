<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base_API_Controller
 * All API controllers extend this. Handles CORS preflight and common helpers.
 */
class Base_API_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Handle CORS preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Token');
            http_response_code(200);
            exit;
        }

        // Set JSON content type header by default
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Origin: *');
    }

    protected function input_json(): array {
        return get_json_input();
    }

    protected function success($data = null, string $msg = 'Success', int $code = 200): void {
        success_response($data, $msg, $code);
    }

    protected function error(string $msg = 'Error', int $code = 400, $data = null): void {
        error_response($msg, $code, $data);
    }

    protected function require_auth(): void {
        require_token();
    }
}
