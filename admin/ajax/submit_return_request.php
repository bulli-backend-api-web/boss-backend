<?php
require_once '../config/database.php';

header('Content-Type: application/json');


// ── Constants ─────────────────────────────────────────────────────
define('OPERATIONAL_CHARGE_PER_QTY', 150.00);
define('COURIER_CHARGE_FLAT',        150.00);
define('COD_CHARGE_FLAT',             50.00);

// ── Read body ─────────────────────────────────────────────────────
// When sent as FormData the JSON payload lands in $_POST['data'].
// Fallback to php://input for plain JSON POST (e.g. Postman testing).
if (!empty($_POST['data'])) {
    $body = json_decode($_POST['data'], true);
} else {
    $raw  = file_get_contents('php://input');
    $body = json_decode($raw, true);
}

if (!$body || !is_array($body)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request body.']);
    exit;
}

// ── Helpers ───────────────────────────────────────────────────────
function str_clean($v, $max = 255) {
    return substr(trim((string)$v), 0, $max);
}

function float_clean($v) {
    return (float) preg_replace('/[^\d.]/', '', (string)$v);
}

// ── Sanitize required fields ──────────────────────────────────────
$mobile       = str_clean($body['mobile']       ?? '', 15);
$orderId      = str_clean($body['orderId']      ?? '', 60);
$orderItem    = str_clean($body['orderItem']    ?? '');
$orderPrice   = float_clean($body['orderPrice'] ?? 0);
$qty          = max(1, (int)($body['qty']       ?? 1));
$requestType  = in_array($body['requestType']  ?? '', ['return','exchange']) ? $body['requestType'] : null;
$refundMethod = in_array($body['refundMethod'] ?? '', ['upi','bank']) ? $body['refundMethod'] : null;
$reasons = $body['reasons'];
if (!$mobile || !$orderId || !$requestType || !$refundMethod) {
    $missing = [];
    if (!$mobile)       $missing[] = 'mobile';
    if (!$orderId)      $missing[] = 'orderId';
    if (!$requestType)  $missing[] = 'requestType';
    if (!$refundMethod) $missing[] = 'refundMethod';
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: ' . implode(', ', $missing)
    ]);
    exit;
}

// ── Charge calculation ────────────────────────────────────────────
$operationalCharge = OPERATIONAL_CHARGE_PER_QTY * $qty;
$courierCharge     = COURIER_CHARGE_FLAT;
$codCharge         = COD_CHARGE_FLAT;
$refundAmount      = max(0, $orderPrice - $operationalCharge - $courierCharge - $codCharge);

// ── Optional courier fields ───────────────────────────────────────
$courierOption = str_clean($body['courierPartner'] ?? '', 10) ?: null;
$awbNumber     = str_clean($body['awbNumber']      ?? '', 100) ?: null;

// ── UPI validation ────────────────────────────────────────────────
$upiId = null;
if ($refundMethod === 'upi') {
    $upiId = str_clean($body['upiId'] ?? '', 100);
    if (!$upiId || strpos($upiId, '@') === false) {
        echo json_encode(['success' => false, 'message' => 'Invalid UPI ID.']);
        exit;
    }
}

// ── Bank validation ───────────────────────────────────────────────
$bankHolder = $bankAcc = $bankIfsc = $bankName = null;
if ($refundMethod === 'bank') {
    $bd         = is_array($body['bankDetails'] ?? null) ? $body['bankDetails'] : [];
    $bankHolder = str_clean($bd['holderName']    ?? '', 120);
    $bankAcc    = str_clean($bd['accountNumber'] ?? '', 30);
    $bankIfsc   = strtoupper(str_clean($bd['ifscCode'] ?? '', 15));
    $bankName   = str_clean($bd['bankName']      ?? '', 100);

    if (!$bankHolder || !$bankAcc || !$bankIfsc || !$bankName) {
        echo json_encode(['success' => false, 'message' => 'Incomplete bank details.']);
        exit;
    }
}

// ── File uploads ──────────────────────────────────────────────────
$uploadDir = __DIR__ . '/../uploads/returns/' . date('Y/m/');
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$photoPaths = [];
$videoPath  = null;

// Photos (field: photos[])
if (!empty($_FILES['photos']['name'][0])) {
    $allowedImg = ['image/jpeg', 'image/png', 'image/webp'];
    foreach ($_FILES['photos']['tmp_name'] as $i => $tmp) {
        if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) continue;
        if (!in_array($_FILES['photos']['type'][$i], $allowedImg)) continue;
        if ($_FILES['photos']['size'][$i] > 5 * 1024 * 1024) continue;

        $ext  = pathinfo($_FILES['photos']['name'][$i], PATHINFO_EXTENSION);
        $name = uniqid('photo_', true) . '.' . strtolower($ext);
        if (move_uploaded_file($tmp, $uploadDir . $name)) {
            $photoPaths[] = 'uploads/returns/' . date('Y/m/') . $name;
        }
        if (count($photoPaths) >= 5) break;
    }
}

// Video (field: video)
if (!empty($_FILES['video']['tmp_name']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
    $allowedVid = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/avi'];
    if (in_array($_FILES['video']['type'], $allowedVid)
        && $_FILES['video']['size'] <= 50 * 1024 * 1024) {
        $ext  = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
        $name = uniqid('video_', true) . '.' . strtolower($ext);
        if (move_uploaded_file($_FILES['video']['tmp_name'], $uploadDir . $name)) {
            $videoPath = 'uploads/returns/' . date('Y/m/') . $name;
        }
    }
}

// ── Insert ────────────────────────────────────────────────────────
$photoJson = !empty($photoPaths) ? json_encode($photoPaths) : null;
$date = date('Y-m-d');
$datee = date('Y-m-d H:i:s');
$customer_name = '';
$fetch_customer_sql = "SELECT customer_name from shopify_order where order_id = '$orderId'";
$fetch_customer_res = $con->query($fetch_customer_sql);
if($fetch_customer_res && $fetch_customer_res->num_rows > 0){
    $customer_row = $fetch_customer_res->fetch_assoc();
    $customer_name = $customer_row['customer_name'];
}

$stmt = $con->prepare("
    INSERT INTO return_order_inquiry
        (name,mobile, order_id, product, total_amount, total_qty, request_type,
         courier_id, awb_number,
         payment_method, upi_id,
         account_name, account_number, ifsc_code, bank_name,
         deduction1, deduction2, cod_charge, refund_amount,
         image_proof, unboxing_video,return_status_id,date,datee,ticket_id)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'DB prepare error: ' . $con->error]);
    exit;
}

$ticket_id = generateTicketId($requestType);

$stmt->bind_param(
    'ssssdisssssssssddddssisss',$customer_name,
    $mobile, $orderId, $orderItem, $orderPrice, $qty, $requestType,
    $courierOption, $awbNumber,
    $refundMethod, $upiId,
    $bankHolder, $bankAcc, $bankIfsc, $bankName,
    $operationalCharge, $courierCharge, $codCharge, $refundAmount,
    $photoJson, $videoPath,$reasons,$date,$datee,$ticket_id);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $stmt->error]);
    exit;
}

$newId = $stmt->insert_id;
$stmt->close();

echo json_encode([
    'success'      => true,
    'requestId'    => $newId,
    'refundAmount' => $refundAmount,
    'charges'      => [
        'operational' => $operationalCharge,
        'courier'     => $courierCharge,
        'cod'         => $codCharge,
    ]
]);