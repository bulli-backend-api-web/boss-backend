<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
    public function index() {
        header('Content-Type: application/json');
        echo json_encode([
            'status'  => true,
            'message' => 'Project API is running',
            'version' => 'v1',
            'docs'    => 'See /api/README.md for endpoint documentation',
        ]);
    }
}
