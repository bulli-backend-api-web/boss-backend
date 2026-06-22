<?php
require_once '../config/database.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$otp = trim($data['otp'] ?? '');
$mobile = trim($data['mobile'] ?? '');
$datee = date('Y-m-d H:i:s');
if (empty($otp)) {
    echo json_encode([
        'success' => false,
        'message' => 'OTP is required'
    ]);
    exit;
}
$is_valid_otp = false;
$sql = "SELECT id,otp FROM return_request_otp WHERE mobile_number = ?  AND datetime >= '".$datee."' - INTERVAL 5 MINUTE ORDER BY id DESC LIMIT 1";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $mobile);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows === 1) {
    $user_row = $result->fetch_assoc();
    if($user_row['otp'] == $otp){
        $is_valid_otp = true;
    }
}

if ($is_valid_otp) {
    echo json_encode([
        'success' => true,
        'message' => 'OTP verified successfully'
    ]);
} else {

    echo json_encode([
        'success' => false,
        'message' => 'Incorrect OTP'
    ]);
}