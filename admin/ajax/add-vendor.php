<?php

include("../config/database.php");

header('Content-Type: application/json');

$vendor_name = trim($_POST['vendor_name'] ?? '');
$mobile      = trim($_POST['mobile'] ?? '');

if ($vendor_name == '') {
    echo json_encode([
        'status' => false,
        'message' => 'Vendor name is required'
    ]);
    exit;
}

$stmt = $con->prepare("
    SELECT id, vendor_name
    FROM vendors
    WHERE vendor_name = ?
    LIMIT 1
");
$stmt->bind_param("s", $vendor_name);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'status' => true,
        'vendor_id' => $row['id'],
        'vendor_name' => $row['vendor_name']
    ]);
    exit;
}

$stmt = $con->prepare("
    INSERT INTO vendors
    (vendor_name, mobile, status, created_at)
    VALUES (?, ?, '1', NOW())
");
$stmt->bind_param("ss", $vendor_name, $mobile);

if ($stmt->execute()) {
    echo json_encode([
        'status' => true,
        'vendor_id' => $stmt->insert_id,
        'vendor_name' => $vendor_name
    ]);
} else {
    echo json_encode([
        'status' => false,
        'message' => 'Vendor insert failed'
    ]);
}