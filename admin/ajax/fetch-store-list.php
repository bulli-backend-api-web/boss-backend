<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$draw   = $_POST['draw'] ?? 1;
$start  = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;

$search = $_POST['search']['value'] ?? '';
$status = $_POST['status'] ?? '';

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir = ($_POST['order'][0]['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

$columns = [
    0 => 'id',
    1 => 'store_name',
    2 => 'store_code'
];

$orderColumn = $columns[$orderColumnIndex] ?? 'd.id';

$where = " WHERE 1=1 ";
$params = [];

/* 🔍 Search */
if (!empty($search)) {
    $where .= " AND store_name LIKE '%$search%'";
}


/* 📊 TOTAL RECORDS (no db_row) */
$stmtTotal = $con->prepare("SELECT COUNT(*) as total FROM store");
$stmtTotal->execute();

$result = $stmtTotal->get_result();
$row = $result->fetch_assoc();

$totalRecords = $row['total'];

/* 📊 FILTERED RECORDS (no db_row) */
$stmtFiltered = $con->prepare("SELECT COUNT(*) as total FROM store $where");

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
$sql = "
    SELECT id,store_name,store_code,ownership_model,store_type,city,state,address,ops_head_name,ops_contact_number,applicable_rate,billing_cycle,opening_date,agreement_expire FROM store
    $where
    ORDER BY $orderColumn $orderDir
    LIMIT $start, $length";

$stmt = $con->prepare($sql);

$stmt->execute();

$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

/* 🎯 FORMAT DATA */
$data = [];
$sr = $start + 1;

foreach ($rows as $row) {

    $id = $row['id'];
    $actions = '';
    $data[] = [
        'sr_no' => $sr++,
        'store_name' => $row['store_name'],
        'store_code' => $row['store_code'],
        'ownership_model' => $row['ownership_model'],
        'store_type' => $row['store_type'],
        'city' => $row['city'],
        'state' => $row['state'],
        'ops_head' => $row['ops_head_name'],
        'ops_contact' => $row['ops_contact_number'],
        'applicable_rate'=>$row['applicable_rate'],
        'billing_cycle' => $row['billing_cycle'],
        'opening_date' => $row['opening_date'],
        'agreement_expire' => $row['agreement_expire'],
        'actions' => $actions
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