<?php
date_default_timezone_set('Asia/Kolkata');
$isCli = PHP_SAPI === 'cli';
$envFile = dirname(__DIR__, 2) . '/.env';
if (!is_readable($envFile)) {
    $envFile = dirname(__DIR__, 3) . '/.env';
}

//if (is_readable($envFile)) {
//    foreach (file($envFile, FILE_IGNORE_NEW_LINES) as $line) {
//        $line = trim($line);
//
//        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
//            continue;
//        }
//
//        [$key, $val] = explode('=', $line, 2);
//        $key = trim($key);
//        $val = trim($val);
//
//        if (
//            (str_starts_with($val, '"') && str_ends_with($val, '"')) ||
//            (str_starts_with($val, "'") && str_ends_with($val, "'"))
//        ) {
//            $val = substr($val, 1, -1);
//        }
//
//        $_ENV[$key] = $val;
//        putenv("$key=$val");
//    }
//}

$host = 'localhost';
$username = 'root';
$password = '';
$db_name = 'bullionknot';

$con = mysqli_connect($host, $username, $password, $db_name);
mysqli_set_charset($con, "utf8mb4");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}  


date_default_timezone_set('Asia/Kolkata');
$lifetime = 10800;
session_set_cookie_params($lifetime);
ini_set('session.gc_maxlifetime', $lifetime);

session_start();

// --- Common values ---
$define_company_name    = 'BullionKnot';
$define_company_website = 'http://localhost/boss-backend/';
$site_path = 'http://localhost/boss-backend/admin';
$softtitle="E-commerce Web Panel";

$task_type = [
    ['id' => 'daily', 'name' => 'Daily'],
    ['id' => 'weekly', 'name' => 'Weekly'],
    ['id' => 'monthly', 'name' => 'Monthly'],
    ['id' => 'specific', 'name' => 'Specific']
];

$recurrence = [
  ['id' => 1, 'name'=>'Everyday'],  
  ['id' => 2, 'name'=>'Weekdays Only'],  
  ['id' => 3, 'name'=>'Mon/wed/Fri'],  
  ['id' => 4, 'name'=>'Custom']  
];

$task_proof_required = [
  ['id' => 1, 'name'=>'Photo Proof'],  
  ['id' => 2, 'name'=>'Notes / Remarks'],  
  ['id' => 3, 'name'=>'Time Taken'],  
  ['id' => 4, 'name'=>'Output Qty']  
];

require_once(__DIR__ . '/../Functions/CommonFunctions.php');
require_once(__DIR__ . '/../Functions/AwsImageUpload.php');
