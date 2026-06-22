<?php
include("../config/database.php");
include("../config/auth_check.php");

error_reporting(E_ALL);
ini_set('display_errors',1);

header('Content-Type: application/json');


$id = $_POST['id'] ?? 0;
$status = $_POST['status'] ?? '';
$approve_date = date('Y-m-d H:i:s');
$approve_by = $uid;

// validate input
if (!$id || !in_array($status, [1, 2])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

try {
    
    
    
    $sql = "UPDATE design SET status = ?, approved_by = ?, approved_date = ?  WHERE id = ?";
    
    $stmt = $con->prepare($sql);

    $stmt->execute([
        $status,
        $approve_by,
        $approve_date,
        $id
    ]);

    $affected = $stmt->affected_rows;

    echo json_encode([
        'success' => $affected > 0,
        'message' => $affected ? 'Status updated' : 'No change'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}