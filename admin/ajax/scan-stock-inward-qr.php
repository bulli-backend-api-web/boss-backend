<?php
include "../config/database.php";
include "../config/auth_check.php";

header('Content-Type: application/json');

$response = [
    "status" => false,
    "message" => "Something went wrong"
];

$batch_id = !empty($_POST['batch_id']) ? (int)$_POST['batch_id'] : 0;
$qr_code  = trim($_POST['qr_code'] ?? '');

if ($batch_id <= 0) {
    $response['message'] = "Invalid batch";
    echo json_encode($response);
    exit;
}

if ($qr_code == '') {
    $response['message'] = "QR code is required";
    echo json_encode($response);
    exit;
}

$batch_q = mysqli_query($con, "
    SELECT * FROM stock_inward_batch 
    WHERE id = '$batch_id'
    LIMIT 1
");

if (mysqli_num_rows($batch_q) == 0) {
    $response['message'] = "Batch not found";
    echo json_encode($response);
    exit;
}

$batch = mysqli_fetch_assoc($batch_q);

if ($batch['status'] == 'CREATED') {
    $response['message'] = "Please print labels first";
    echo json_encode($response);
    exit;
}

$qr_safe = mysqli_real_escape_string($con, $qr_code);

$qr_q = mysqli_query($con, "
    SELECT * FROM stock_inward_qr
    WHERE batch_id = '$batch_id'
    AND qr_code = '$qr_safe'
    LIMIT 1
");

if (mysqli_num_rows($qr_q) == 0) {
    $response['message'] = "Invalid QR for this batch";
    echo json_encode($response);
    exit;
}

$qr = mysqli_fetch_assoc($qr_q);

if ($qr['scan_status'] == 1) {
    $response['message'] = "This QR already scanned";
    echo json_encode($response);
    exit;
}

mysqli_begin_transaction($con);

try {

    mysqli_query($con, "
        UPDATE stock_inward_qr
        SET 
            scan_status = 1,
            stock_status = 'AVAILABLE',
            scanned_at = NOW()
        WHERE id = '{$qr['id']}'
    ");

    mysqli_query($con, "
        INSERT INTO stock_ledger
        (
            product_id,
            size,
            qr_code,
            movement_type,
            qty,
            reference_type,
            reference_id,
            created_at
        )
        VALUES
        (
            '{$qr['product_id']}',
            '{$qr['size']}',
            '{$qr['qr_code']}',
            'IN',
            1,
            'INWARD_BATCH',
            '$batch_id',
            NOW()
        )
    ");

    mysqli_query($con, "
        UPDATE stock_inward_batch
        SET 
            scanned_qty = scanned_qty + 1,
            status = 'SCANNING'
        WHERE id = '$batch_id'
    ");

    $count_q = mysqli_query($con, "
        SELECT 
            COUNT(*) AS total_qty,
            SUM(CASE WHEN scan_status = 1 THEN 1 ELSE 0 END) AS scanned_qty
        FROM stock_inward_qr
        WHERE batch_id = '$batch_id'
    ");

    $count = mysqli_fetch_assoc($count_q);

    if ((int)$count['total_qty'] == (int)$count['scanned_qty']) {
        mysqli_query($con, "
            UPDATE stock_inward_batch
            SET status = 'COMPLETED'
            WHERE id = '$batch_id'
        ");
    }

    mysqli_commit($con);

    $response['status'] = true;
    $response['message'] = "QR scanned successfully";
    $response['total_qty'] = (int)$count['total_qty'];
    $response['scanned_qty'] = (int)$count['scanned_qty'];
    $response['pending_qty'] = (int)$count['total_qty'] - (int)$count['scanned_qty'];

    echo json_encode($response);
    exit;

} catch (Exception $e) {

    mysqli_rollback($con);

    $response['message'] = "Scan failed";
    echo json_encode($response);
    exit;
}