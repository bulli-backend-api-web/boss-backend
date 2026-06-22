<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$draw   = $_POST['draw'] ?? 1;
$start  = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;

$search = $_POST['search']['value'] ?? '';
$status = $_POST['status'] ?? '';
$design_type = isset($_POST['design_type']) ? $_POST['design_type'] : "";

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir = ($_POST['order'][0]['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

$columns = [
    0 => 'id',
    1 => 'fabric_name',
    2 => 'fabric_code'
];

$orderColumn = $columns[$orderColumnIndex] ?? 'd.id';
$where = " WHERE 1=1 ";
$params = [];

/* 🔍 Search */
if (!empty($search)) {
    $where .= " AND fabric_name LIKE '%$search%' OR fabric_code LIKE '%$search%'";
}

$stmtTotal = $con->prepare("SELECT COUNT(*) as total FROM fabric_master");
$stmtTotal->execute();

$result = $stmtTotal->get_result();
$row = $result->fetch_assoc();

$totalRecords = $row['total'];

/* 📊 FILTERED RECORDS (no db_row) */
$stmtFiltered = $con->prepare("SELECT COUNT(*) as total FROM fabric_master $where");

/* bind params dynamically */
if (!empty($params)) {
    $types = '';
    $values = [];

    foreach ($params as $val) {
        $types .= 's';
        $values[] = $val;
    }

    $stmtFiltered->bind_param($types, ...$values);
}

$stmtFiltered->execute();

$result = $stmtFiltered->get_result();
$row = $result->fetch_assoc();

$totalFiltered = $row['total'];

/* 📦 MAIN DATA QUERY */
$sql = "SELECT id,fabric_name,fabric_code,default_unit,default_rate,fabric_type,color,composition FROM fabric_master  $where ORDER BY $orderColumn $orderDir LIMIT $start, $length";

$stmt = $con->prepare($sql);

$stmt->execute();

$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

/* 🎯 FORMAT DATA */
$data = [];
$sr = $start + 1;

foreach ($rows as $row) {
     $actions ='<a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"  data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions <i class="ki-outline ki-down fs-5 ms-1"></i></a><div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600  menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                 <div class="menu-item px-3">
                    <a href="'.$site_path.'/edit-rule-book?id='.my_simple_crypt($row['id'],'encrypt_1').'" class="menu-link px-3">Edit</a>
                </div>
            </div>';
    $id = $row['id'];
    $data[] = [
        'sr_no' => $sr++,
        'name' => $row['fabric_name'],
        'code' => $row['fabric_code'],
        'type' => $row['fabric_type'],
        'composition' => $row['composition'],
        'color' => $row['color'],
        'unit' => $row['default_unit'],
        'rate' => $row['default_rate'],
        'actions' => $actions,
    ];
}

/* 📤 OUTPUT */
echo json_encode([
    "draw" => (int)$draw,
    "recordsTotal" => (int)$totalRecords,
    "recordsFiltered" => (int)$totalFiltered,
    "data" => $data
]);
exit;