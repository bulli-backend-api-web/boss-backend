<?php

include("../config/database.php");

$challan_no = trim($_POST['challan_no']);

$stmt = $con->prepare("
    SELECT id
    FROM stock_inward_batch
    WHERE challan_no = ?
    LIMIT 1
");

$stmt->bind_param("s", $challan_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'status' => false,
        'message' => 'Challan number already exists'
    ]);
} else {
    echo json_encode([
        'status' => true
    ]);
}

$stmt->close();
