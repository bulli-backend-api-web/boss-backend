<?php
include("../config/database.php");
include("../config/auth_check.php");

header("Content-Type: application/json");

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$size       = trim($_POST['size'] ?? '');

$response = [
    "status" => false,
    "stock" => 0
];

if ($product_id <= 0 || $size == '') {
    echo json_encode($response);
    exit;
}

$stmt = $con->prepare("
    SELECT 
        SUM(
            CASE 
                WHEN movement_type IN ('IN','UNRESERVE') THEN qty
                WHEN movement_type IN ('OUT','RESERVE') THEN -qty
                ELSE 0
            END
        ) AS available_stock
    FROM stock_ledger
    WHERE product_id = ?
    AND size = ?
");

$stmt->bind_param("is", $product_id, $size);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$stmt->close();

$response['status'] = true;
$response['stock'] = (int)$row['available_stock'];

echo json_encode($response);
exit;