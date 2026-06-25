<?php
/**
 * AJAX: Get full staff list for User Assignment tab dropdowns
 * GET /ajax/get-staff-list
 *
 * Optional filters:
 *   ?dept_id=3          → only staff in that department
 *   ?role_id=2          → only staff with that role
 *   ?status=active      → active only (default: all)
 *   ?status=inactive    → inactive only
 *   ?q=priya            → search by name or staff code
 *
 * Response:
 * {
 *   "total": 6,
 *   "staff": [
 *     {
 *       "id": 10,
 *       "name": "Priya Mehta",
 *       "staff_id": "BK-STF-2025-039",
 *       "role_id": 3,
 *       "role_name": "QC Supervisor",
 *       "department_id": 2,
 *       "department_name": "Quality Control",
 *       "is_active": 1,
 *       "has_overrides": 0,
 *       "last_login": "Today 09:04"
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

/* ── Filters ── */
$dept_id = isset($_GET['dept_id']) ? (int)$_GET['dept_id'] : 0;
$role_id = isset($_GET['role_id']) ? (int)$_GET['role_id'] : 0;
$status  = isset($_GET['status'])  ? trim($_GET['status'])  : '';
$search  = isset($_GET['q'])       ? trim($_GET['q'])       : '';

/* ── Build WHERE clauses ── */
$where = ['1=1'];

if ($dept_id) {
    $dept_id = mysqli_real_escape_string($con, $dept_id);
    $where[] = "s.department_id = '$dept_id'";
}

if ($role_id) {
    $role_id = mysqli_real_escape_string($con, $role_id);
    $where[] = "ur.role_id = '$role_id'";
}

if ($status === 'active') {
    $where[] = "s.is_active = 1";
} elseif ($status === 'inactive') {
    $where[] = "s.is_active = 0";
}

if ($search !== '') {
    $search_esc = mysqli_real_escape_string($con, $search);
    $where[] = "(CONCAT(s.first_name,' ',s.last_name) LIKE '%$search_esc%'
                 OR s.staff_code LIKE '%$search_esc%')";
}

$where_sql = implode(' AND ', $where);

/* ── Main query ── */
/*
 * Assumed schema:
 *   staff            → id, first_name, last_name, staff_code, department_id, is_active, last_login_at
 *   user_roles       → staff_id, role_id          (role assigned to staff)
 *   role             → id, role_name
 *   departments      → id, department_name
 *   staff_permissions→ staff_id                   (has at least 1 row = has overrides)
 *
 * If role is stored directly on staff (e.g. staff.role_id) instead of
 * a separate user_roles table, replace the LEFT JOIN user_roles with
 * a direct column reference and remove the JOIN.
 *
 * Adjust table/column names to match your schema.
 */
$sql = "SELECT
            s.id,
            name,
            ur.role_id,
            r.role_name,
            s.department_id,
            d.department_name,
            (
                SELECT COUNT(*)
                FROM role_modules sp
                WHERE sp.role_id = s.typee_id
            ) AS override_count
        FROM user s
        LEFT JOIN role_modules  ur ON ur.role_id    = s.typee_id
        LEFT JOIN role         r  ON r.id           = ur.role_id
        LEFT JOIN departments  d  ON d.id           = s.department_id
        WHERE $where_sql
        ORDER BY name ASC";

$result = mysqli_query($con, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . mysqli_error($con)]);
    exit;
}

/* ── Format last_login ── */
function formatLastLogin($datetime) {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return 'Never';
    }

    $ts   = strtotime($datetime);
    $now  = time();
    $diff = $now - $ts;

    $today_start     = strtotime('today');
    $yesterday_start = strtotime('yesterday');

    if ($ts >= $today_start) {
        return 'Today ' . date('H:i', $ts);
    } elseif ($ts >= $yesterday_start) {
        return 'Yesterday';
    } elseif ($diff < 7 * 86400) {
        return date('l', $ts); // e.g. "Monday"
    } else {
        return date('d M Y', $ts);
    }
}

/* ── Build response ── */
$staff = [];
while ($row = mysqli_fetch_assoc($result)) {
    $staff[] = [
        'id'              => (int)$row['id'],
        'name'            => $row['name'],
        'staff_id'        => $row['staff_id'] ?? '',
        'role_id'         => $row['role_id']  ? (int)$row['role_id']  : null,
        'role_name'       => $row['role_name'] ?? null,
        'department_id'   => $row['department_id'] ? (int)$row['department_id'] : null,
        'department_name' => $row['department_name'] ?? null,
        'is_active'       => (int)$row['is_active'],
        'has_overrides'   => (int)$row['override_count'] > 0 ? 1 : 0,
        'last_login'      => formatLastLogin($row['last_login_at']),
    ];
}

echo json_encode([
    'total' => count($staff),
    'staff' => $staff,
]);
exit;