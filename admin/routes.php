<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';
$route['404_override']       = '';
$route['translate_uri_dashes'] = FALSE;

// ── API Routes ──────────────────────────────────────────────
// Products
$route['v1/products']             = 'api/Products/index';
$route['v1/products/(:num)']      = 'api/Products/show/$1';
$route['v1/products/store']       = 'api/Products/store';
$route['v1/products/update/(:num)'] = 'api/Products/update/$1';
$route['v1/products/delete/(:num)'] = 'api/Products/destroy/$1';

// Auth
$route['v1/auth/login']  = 'api/Auth/login';
$route['v1/auth/user_module']  = 'api/Auth/get_user_module';

//Design
$route['v1/design/design_list']  = 'api/Design/get_design_list';
$route['v1/design/home_page_list']  = 'api/Design/home_page_list';
$route['v1/design/create_new_design']  = 'api/Design/upload_new_design';