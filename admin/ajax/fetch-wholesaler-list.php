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
    1 => 'business_name',
    2 => 'gst_number'
];

$orderColumn = $columns[$orderColumnIndex] ?? 'd.id';

$where = " WHERE 1=1 ";
$params = [];

/* 🔍 Search */
if (!empty($search)) {
    $where .= " AND business_name LIKE '%$search%'";
}


/* 📊 TOTAL RECORDS (no db_row) */
$stmtTotal = $con->prepare("SELECT COUNT(*) as total FROM wholesaler");
$stmtTotal->execute();

$result = $stmtTotal->get_result();
$row = $result->fetch_assoc();

$totalRecords = $row['total'];

/* 📊 FILTERED RECORDS (no db_row) */
$stmtFiltered = $con->prepare("SELECT COUNT(*) as total FROM wholesaler $where");

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
    SELECT id,business_name,gst_number,business_type,city,contact_person,contact_person_mobile,contact_person_email,contact_person_whatsapp FROM wholesaler
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
        'business_name' => $row['business_name'],
        'gst_number' => $row['gst_number'],
        'business_type' => $row['business_type'],
        'city' => $row['city'],
        'contact_person' => $row['contact_person'],
        'mobile' => $row['contact_person_mobile'],
        'email' => $row['contact_person_email'],
        'whatsapp' => $row['contact_person_whatsapp'],
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