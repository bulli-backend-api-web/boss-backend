<?php
include("../config/database.php"); // $con is mysqli connection

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// ── Allowed sort columns (whitelist — never trust user input for ORDER BY) ──
$columns = [
    1 => "return_order_inquiry.datee",
    2 => "return_order_inquiry.name",
    3 => "return_order_inquiry.mobile",
];

// ── DataTables parameters ────────────────────────────────────────────────────
$draw            = intval($_POST['draw']                ?? 1);
$start           = intval($_POST['start']               ?? 0);
$length          = intval($_POST['length']              ?? 10);
$searchValue     = trim($_POST['search']['value']       ?? '');
$orderColumnIdx  = intval($_POST['order'][0]['column']  ?? 1);
$orderDir        = strtoupper($_POST['order'][0]['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
$orderColumn     = $columns[$orderColumnIdx] ?? "return_order_inquiry.datee";

// ── Base FROM + JOIN + fixed WHERE ───────────────────────────────────────────
$baseQry = "FROM return_order_inquiry
            LEFT JOIN m_order_return_remark
                   ON m_order_return_remark.id = return_order_inquiry.return_status_id
            WHERE return_order_inquiry.customer_inquiry_status = 1";

// ── Dynamic search filter ────────────────────────────────────────────────────
$searchSql  = "";
$bindTypes  = "";
$bindValues = [];

if ($searchValue !== '') {
    $like = "%{$searchValue}%";
    $searchSql   = " AND (
                        return_order_inquiry.name      LIKE ? OR
                        return_order_inquiry.ticket_id LIKE ? OR
                        return_order_inquiry.mobile    LIKE ?
                    )";
    $bindTypes  .= "sss";
    $bindValues  = [$like, $like, $like];
}

// ── Helper: run a COUNT query ─────────────────────────────────────────────────
function runCount(mysqli $con, string $sql, string $types, array $values): int
{
    $stmt = $con->prepare($sql);
    if (!$stmt) return 0;

    if ($types !== '') {
        $refs = [];
        foreach ($values as $k => $v) { $refs[$k] = &$values[$k]; }
        array_unshift($refs, $types);
        call_user_func_array([$stmt, 'bind_param'], $refs);
    }

    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return (int) $count;
}

// ── Total records (no search filter) ─────────────────────────────────────────
$totalRecords = runCount(
    $con,
    "SELECT COUNT(*) {$baseQry}",
    '',
    []
);

// ── Filtered records (with search filter) ────────────────────────────────────
$totalFiltered = runCount(
    $con,
    "SELECT COUNT(*) {$baseQry}{$searchSql}",
    $bindTypes,
    $bindValues
);

// ── Fetch paginated data ──────────────────────────────────────────────────────
$dataSql = "SELECT
                return_order_inquiry.id,
                return_order_inquiry.datee,
                return_order_inquiry.ticket_id,
                return_order_inquiry.name,
                return_order_inquiry.mobile,
                return_order_inquiry.order_id,
                return_order_inquiry.return_status_id,
                m_order_return_remark.name AS return_name
            {$baseQry}{$searchSql}
            ORDER BY {$orderColumn} {$orderDir}
            LIMIT ?, ?";

$stmt = $con->prepare($dataSql);
if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed: ' . $con->error]);
    exit;
}

// Bind: search params + LIMIT params
$allTypes  = $bindTypes . 'ii';
$allValues = array_merge($bindValues, [$start, $length]);

$refs = [];
foreach ($allValues as $k => $v) { $refs[$k] = &$allValues[$k]; }
array_unshift($refs, $allTypes);
call_user_func_array([$stmt, 'bind_param'], $refs);

$stmt->execute();
$result = $stmt->get_result();

$data = [];
$srNo = $start + 1; // continuous serial across pages

while ($r = $result->fetch_assoc()) {

    // ── Actions dropdown (Metronic / KTMenu) ─────────────────────────────
    $encId   = my_simple_crypt($r['id'], 'encrypt_1');
    $editUrl = $site_path . '/edit-return-order-inquiry?id=' . $encId;

    $actions = '
        <a href="#"
           class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm"
           data-kt-menu-trigger="click"
           data-kt-menu-placement="bottom-end">
            Actions <i class="ki-outline ki-down fs-5 ms-1"></i>
        </a>
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded
                    menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7
                    w-125px py-4"
             data-kt-menu="true">
            <div class="menu-item px-3">
                <a href="' . $editUrl . '" class="menu-link px-3">Edit</a>
            </div>
        </div>';

    $data[] = [
        'sr_no'         => $srNo,
        'date'          => $r['datee'],
        'ticket_id'     => $r['ticket_id'],
        'name'          => htmlspecialchars($r['name'],          ENT_QUOTES),
        'mobile'        => htmlspecialchars($r['mobile'],        ENT_QUOTES),
        'order_id'      => htmlspecialchars($r['order_id'],      ENT_QUOTES),
        'return_status' => htmlspecialchars($r['return_name'] ?? '—', ENT_QUOTES),
        'actions'       => $actions,
    ];

    $srNo++;
}

$stmt->close();

// ── Response ──────────────────────────────────────────────────────────────────
echo json_encode([
    'draw'            => $draw,
    'recordsTotal'    => $totalRecords,
    'recordsFiltered' => $totalFiltered,
    'data'            => $data,
]);
exit;