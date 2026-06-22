<?php
include("../config/database.php");
include("../config/auth_check.php");

header("Content-Type: application/json");

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

$response = [
    "status" => false,
    "message" => "Invalid request",
    "size_options" => '<option value="">Select Current Size</option>'
];

if ($product_id <= 0) {
    echo json_encode($response);
    exit;
}

$stmt = $con->prepare("
    SELECT 
        size,
        SUM(
            CASE 
                WHEN movement_type IN ('IN','UNRESERVE') THEN qty
                WHEN movement_type IN ('OUT','RESERVE') THEN -qty
                ELSE 0
            END
        ) AS available_stock
    FROM stock_ledger
    WHERE product_id = ?
    GROUP BY size
    HAVING available_stock > 0
    ORDER BY size ASC
");

$stmt->bind_param("i", $product_id);
$stmt->execute();

$result = $stmt->get_result();

$options = '<option value="">Select Current Size</option>';

while ($row = $result->fetch_assoc()) {

    $size = htmlspecialchars($row['size']);
    $stock = (int)$row['available_stock'];

    $options .= '<option value="'.$size.'">'.$size.' - Available '.$stock.'</option>';
}

$stmt->close();

$response['status'] = true;
$response['message'] = "Sizes loaded";
$response['size_options'] = $options;

echo json_encode($response);
exit;