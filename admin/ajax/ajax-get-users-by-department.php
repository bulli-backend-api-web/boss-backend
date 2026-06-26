<?php
header('Content-Type: application/json');
include("../config/database.php");
include("../config/auth_check.php");

$department_id = $_GET['department_id'] ?? '';

if (empty($department_id)) {
    echo json_encode(['success' => false, 'message' => 'Department ID required']);
    exit;
}

try {
    $stmt = $con->prepare("SELECT id, name FROM user WHERE department_id = ? AND status = '1'");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $stmt->bind_result($id, $name);

    $users = [];

    while ($stmt->fetch()) {
        $users[] = [
            'id' => $id,
            'name' => $name
        ];
    }

    echo json_encode(['success' => true, 'users' => $users]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}