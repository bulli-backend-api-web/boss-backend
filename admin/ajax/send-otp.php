<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);



$mobile = trim($input['mobile'] ?? '');
$countryCode = trim($input['countryCode'] ?? '+91');
$datee = date('Y-m-d H:i:s');
if (empty($mobile)) {
    echo json_encode([
        'success' => false,
        'message' => 'Mobile number is required'
    ]);
    exit;
}

if (!preg_match('/^[0-9]{10}$/', $mobile)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid mobile number'
    ]);
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);

$otp_stmt = $con->prepare("INSERT INTO return_request_otp (otp, mobile_number, datetime) VALUES (?, ?, ?)");
        $otp_stmt->bind_param("sss", $otp, $mobile, $datee);
        $otp_stmt->execute();

error_log("OTP for {$mobile}: {$otp}");

echo json_encode([
    'success' => true,
    'message' => 'OTP sent successfully'
]);