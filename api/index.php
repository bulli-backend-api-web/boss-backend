<?php
/**
 * CodeIgniter 3 – Front Controller
 */
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

switch (ENVIRONMENT) {
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 0);
        break;
    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;
    default:
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// Path to the "system" folder
define('BASEPATH', FCPATH . 'system' . DIRECTORY_SEPARATOR);

// Path to the "application" folder
define('APPPATH', FCPATH . 'application' . DIRECTORY_SEPARATOR);

// Path to the "views" folder
define('VIEWPATH', APPPATH . 'views' . DIRECTORY_SEPARATOR);

// Load the bootstrap file
require_once BASEPATH . 'core/CodeIgniter.php';
