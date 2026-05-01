<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    public function find_by_username(string $username): ?array {
        $row = $this->db->get_where('admin_users', ['username' => $username])->row_array();
        return $row ?: null;
    }

    /**
     * Get existing API token for the user or create a new one.
     * This ties admin users to api_tokens table.
     */
    public function get_or_create_token(int $userId): string {
        // Look for an existing token for this user (app_name = 'admin_user_{id}')
        $appName = 'admin_user_' . $userId;
        $existing = $this->db->get_where('api_tokens', ['app_name' => $appName, 'status' => 1])->row_array();

        if ($existing) {
            return $existing['token'];
        }

        // Create a new token
        $token = bin2hex(random_bytes(32));
        $this->db->insert('api_tokens', [
            'app_name'   => $appName,
            'token'      => $token,
            'status'     => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $token;
    }
}
