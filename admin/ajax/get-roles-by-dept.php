<?php
/**
 * AJAX: Get roles belonging to a department
 * GET /ajax/get-roles-by-dept?dept_id=3
 *
 * Response: { "roles": [ { "id": 1, "role_name": "Designer" }, ... ] }
 */

include("../config/database.php");
include("../config/auth_check.php");

header('Content-Type: application/json');

// Only allow GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$dept_id = isset($_GET['dept_id']) ? (int)$_GET['dept_id'] : 0;

if (!$dept_id) {
    echo json_encode(['roles' => []]);
    exit;
}

$dept_id = mysqli_real_escape_string($con, $dept_id);

$sql = "SELECT r.id, r.role_name
        FROM role r
        WHERE r.department_id = '$dept_id'
        ORDER BY r.role_name ASC";

$result = mysqli_query($con, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . mysqli_error($con)]);
    exit;
}

$roles = [];
while ($row = mysqli_fetch_assoc($result)) {
    $roles[] = [
        'id'        => (int)$row['id'],
        'role_name' => $row['role_name'],
    ];
}

echo json_encode(['roles' => $roles]);
exit;