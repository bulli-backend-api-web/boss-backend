<?php
/**
 * AJAX: Get permission view for View Permissions tab
 * GET /ajax/get-view-permissions?dept_id=1&role_id=2&staff_id=10
 *
 * - dept_id only   → combined highest permissions across all roles in dept
 * - dept_id+role   → permissions for that role
 * - dept_id+role+staff → individual staff permissions (with overrides applied)
 *
 * Response:
 * {
 *   "scope_label": "Design Studio — department overview",
 *   "role_count": 2,
 *   "staff_count": 4,
 *   "categories": [
 *     {
 *       "name": "Design",
 *       "modules": [
 *         { "name": "Design Tasks", "view":true, "add":true, "edit":true, "delete":false, "approve":false, "export":false },
 *         ...
 *       ]
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

$dept_id  = isset($_GET['dept_id'])  ? (int)$_GET['dept_id']  : 0;
$role_id  = isset($_GET['role_id'])  ? (int)$_GET['role_id']  : 0;
$staff_id = isset($_GET['staff_id']) ? (int)$_GET['staff_id'] : 0;

if (!$dept_id) {
    echo json_encode(['categories' => [], 'scope_label' => '']);
    exit;
}

/* ── 1. Resolve scope label, role_count, staff_count ── */
$dept_name  = '';
$role_count = 0;
$staff_count = 0;

$deptRow = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT department_name FROM departments WHERE id = '$dept_id' LIMIT 1"
));
$dept_name = $deptRow['department_name'] ?? '';

if ($staff_id) {
    // Single staff member
    $staffRow = mysqli_fetch_assoc(mysqli_query($con,
        "SELECT  name FROM user WHERE id = '$staff_id' LIMIT 1"
    ));
    $scope_label = ($staffRow['name'] ?? 'Staff member') . ' — individual permissions';
    $role_count  = null;
    $staff_count = null;

} elseif ($role_id) {
    // Single role
    $roleRow = mysqli_fetch_assoc(mysqli_query($con,
        "SELECT role_name FROM role WHERE id = '$role_id' LIMIT 1"
    ));
    $scope_label = $dept_name . ' — ' . ($roleRow['role_name'] ?? 'Role');
    $role_count  = null;
    $r = mysqli_fetch_assoc(mysqli_query($con,
        "SELECT COUNT(*) AS c FROM user s
         INNER JOIN role_modules ur ON ur.role_id = s.typee_id
         WHERE ur.role_id = '$role_id'"
    ));
    $staff_count = (int)($r['c'] ?? 0);

} else {
    $scope_label = $dept_name . ' — department overview';
    $rc = mysqli_fetch_assoc(mysqli_query($con,
        "SELECT COUNT(*) AS c FROM role WHERE department_id = '$dept_id'"
    ));
    $role_count = (int)($rc['c'] ?? 0);
    $sc = mysqli_fetch_assoc(mysqli_query($con,
        "SELECT COUNT(DISTINCT s.id) AS c
         FROM user s
         INNER JOIN role r ON r.id = ur.role_id
         WHERE r.department_id = '$dept_id' AND s.is_active = 1"
    ));
    $staff_count = (int)($sc['c'] ?? 0);
}

// Collect permitted module IDs
$permitted_module_ids = [];

if ($staff_id) {
    $baseResult = mysqli_query($con,
        "SELECT rp.module_id
         FROM role_permissions rp
         INNER JOIN user_roles ur ON ur.role_id = rp.role_id
         WHERE ur.staff_id = '$staff_id'"
    );
    while ($row = mysqli_fetch_assoc($baseResult)) {
        $permitted_module_ids[(int)$row['module_id']] = true;
    }

    // Overrides: check if staff_permissions table exists
    $overrideResult = mysqli_query($con,
        "SELECT module_id, has_permission
         FROM staff_permissions
         WHERE staff_id = '$staff_id'"
    );
    if ($overrideResult) {
        while ($row = mysqli_fetch_assoc($overrideResult)) {
            if ((int)$row['has_permission'] === 1) {
                $permitted_module_ids[(int)$row['module_id']] = true;
            } else {
                unset($permitted_module_ids[(int)$row['module_id']]);
            }
        }
    }

} elseif ($role_id) {
    // Single role permissions
    $result = mysqli_query($con,
        "SELECT module_id FROM role_modules WHERE role_id = '$role_id'"
    );
    while ($row = mysqli_fetch_assoc($result)) {
        $permitted_module_ids[(int)$row['module_id']] = true;
    }

} else {
    // Department: union of ALL role permissions in this dept (highest combined)
    $result = mysqli_query($con,
        "SELECT DISTINCT rp.module_id
         FROM role_permissions rp
         INNER JOIN role r ON r.id = rp.role_id
         WHERE r.department_id = '$dept_id' AND r.status = 1"
    );
    while ($row = mysqli_fetch_assoc($result)) {
        $permitted_module_ids[(int)$row['module_id']] = true;
    }
}

/* ── 3. Load categories + modules and map permissions ── */
$catResult = mysqli_query($con,
    "SELECT id, category_name FROM module_category ORDER BY id ASC"
);
$categories = [];

while ($cat = mysqli_fetch_assoc($catResult)) {
    $cat_id = (int)$cat['id'];

    $modResult = mysqli_query($con,
        "SELECT id, module_name FROM modules
         WHERE category_id = '$cat_id'
         ORDER BY module_name ASC"
    );

    $modules = [];
    while ($mod = mysqli_fetch_assoc($modResult)) {
        $mid     = (int)$mod['id'];
        $allowed = isset($permitted_module_ids[$mid]);

        /*
         * Since your DB stores permission as a single flag per module
         * (not per action), the same flag applies to all action columns.
         *
         * If you later add per-action columns (view/add/edit/delete/approve/export)
         * to role_permissions, replace $allowed with per-action checks here.
         */
        $modules[] = [
            'name'    => $mod['module_name'],
            'view'    => $allowed,
            'add'     => $allowed,
            'edit'    => $allowed,
            'delete'  => false,   // delete/approve/export default to false
            'approve' => false,   // until you add per-action DB columns
            'export'  => false,
        ];
    }

    if (!empty($modules)) {
        $categories[] = [
            'name'    => $cat['category_name'],
            'modules' => $modules,
        ];
    }
}

/* ── 4. Return response ── */
$response = [
    'scope_label' => $scope_label,
    'categories'  => $categories,
];
if ($role_count  !== null) $response['role_count']  = $role_count;
if ($staff_count !== null) $response['staff_count'] = $staff_count;

echo json_encode($response);
exit;