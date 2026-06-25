<?php
/**
 * AJAX: Get staff members assigned to a specific role
 */

include("../config/database.php");
include("../config/auth_check.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$role_id = isset($_GET['role_id']) ? (int)$_GET['role_id'] : 0;

if (!$role_id) {
    echo json_encode(['staff' => []]);
    exit;
}

$role_id = mysqli_real_escape_string($con, $role_id);
$sql = "SELECT s.id as staff_id,s.id,
               name
        FROM user s
        INNER JOIN role ur ON ur.id = s.typee_id
        WHERE ur.id = '$role_id'
        ORDER BY name ASC";

$result = mysqli_query($con, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . mysqli_error($con)]);
    exit;
}

$staff = [];
while ($row = mysqli_fetch_assoc($result)) {
    $staff[] = [
        'id'       => (int)$row['id'],
        'name'     => $row['name'],
        'staff_id' => $row['staff_id'],
    ];
}

echo json_encode(['staff' => $staff]);
exit;