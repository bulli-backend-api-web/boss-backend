<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/Base_API_Controller.php';

/**
 * Auth Controller
 * POST /v1/auth/login
 */
class Auth extends Base_API_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('api/Auth_model');
    }

    /**
     * POST /v1/auth/login
     * Body: { "username": "admin", "password": "admin123" }
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->error('Method not allowed.', 405);
        }

        $input    = $this->input_json();
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';

        if (!$username || !$password) {
            $this->error('Username and password are required.', 422);
        }

        $user = $this->Auth_model->find_by_username($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->error('Invalid credentials.', 401);
        }

        if (!$user['status']) {
            $this->error('Your account is disabled.', 403);
        }

        // Generate / return a token for this user
        $token = $this->Auth_model->get_or_create_token($user['id']);

        unset($user['password']);
        $this->success([
            'user'  => $user,
            'token' => $token,
        ], 'Login successful.');
    }
}
