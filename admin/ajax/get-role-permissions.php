<?php
/**
 * AJAX: Get permissions assigned to a specific role
 * GET /ajax/get-role-permissions?role_id=2
 *
 * Response:
 * {
 *   "role_id": 2,
 *   "role_name": "Designer",
 *   "permissions": [101, 102, 105],   ← flat array of permitted module IDs
 *   "categories": [
 *     {
 *       "name": "Design",
 *       "permissions": ["Design Tasks", "Reference Library"]
 *     },
 *     ...
 *   ]
 * }
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
    echo json_encode([
        'role_id'     => 0,
        'role_name'   => '',
        'permissions' => [],
        'categories'  => [],
    ]);
    exit;
}

$role_id = mysqli_real_escape_string($con, $role_id);

/* ── 1. Get role name ── */
$roleRow = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id, role_name FROM role WHERE id = '$role_id' LIMIT 1"
));

if (!$roleRow) {
    http_response_code(404);
    echo json_encode(['error' => 'Role not found']);
    exit;
}

/* ── 2. Get flat list of permitted module IDs ── */
/*
 * Assumes table: role_permissions (role_id, module_id)
 * If your table is named differently (e.g. role_modules, role_access),
 * update the table name below.
 */
$permResult = mysqli_query($con,
    "SELECT module_id FROM role_modules WHERE role_id = '$role_id'"
);

if (!$permResult) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . mysqli_error($con)]);
    exit;
}

$permission_ids = [];
while ($row = mysqli_fetch_assoc($permResult)) {
    $permission_ids[] = (int)$row['module_id'];
}

/* ── 3. Build categorised list (for View Permissions display) ── */
/*
 * Assumes tables:
 *   permission_category → id, category_name
 *   modules             → id, module_name, category_id
 *
 * Adjust table/column names to match your schema.
 */
$categories = [];

if (!empty($permission_ids)) {
    $ids_in = implode(',', $permission_ids);

    $catResult = mysqli_query($con,
        "SELECT id, category_name FROM module_category ORDER BY id ASC"
    );

    while ($cat = mysqli_fetch_assoc($catResult)) {
        $cat_id = (int)$cat['id'];

        $modResult = mysqli_query($con,
            "SELECT module_name
             FROM modules
             WHERE category_id = '$cat_id'
               AND id IN ($ids_in)
             ORDER BY module_name ASC"
        );

        $module_names = [];
        while ($mod = mysqli_fetch_assoc($modResult)) {
            $module_names[] = $mod['module_name'];
        }

        if (!empty($module_names)) {
            $categories[] = [
                'name'        => $cat['category_name'],
                'permissions' => $module_names,
            ];
        }
    }
}

/* ── 4. Return response ── */
echo json_encode([
    'role_id'     => (int)$roleRow['id'],
    'role_name'   => $roleRow['role_name'],
    'permissions' => $permission_ids,   // flat ID array — used by Permission Matrix JS
    'categories'  => $categories,       // grouped names — used by View Permissions tab
]);
exit;