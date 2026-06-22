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
    0 => 'sa.id'
];

$orderColumn = $columns[$orderColumnIndex] ?? 'd.id';

$where = " WHERE 1=1 ";

$params = [];

/* 🔍 Search */
if (!empty($search)) {
    $where .= " AND u.name LIKE '%$search%'";
}


/* 📊 TOTAL RECORDS (no db_row) */
$stmtTotal = $con->prepare("SELECT COUNT(*) as total FROM staff_attendance");
$stmtTotal->execute();

$result = $stmtTotal->get_result();
$row = $result->fetch_assoc();

$totalRecords = $row['total'];

/* 📊 FILTERED RECORDS (no db_row) */
$stmtFiltered = $con->prepare("SELECT COUNT(*) as total FROM staff_attendance sa $where");

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
    SELECT 
        sa.id,
        sa.picture,
        sa.datetime,
        sa.ip_address,
        sa.device_info,
        u1.name AS upload_by_name
    FROM staff_attendance sa
    LEFT JOIN user u1 ON u1.id = sa.user_id
    $where
    ORDER BY $orderColumn $orderDir
    LIMIT $start, $length
";

$stmt = $con->prepare($sql);

$stmt->execute();

$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

/* 🎯 FORMAT DATA */
$data = [];
$sr = $start + 1;

foreach ($rows as $row) {

    $id = $row['id'];

    $image = !empty($row['picture']) ? '../../uploads/staff_attendance/' . $row['picture'] : $site_path . '/assets/media/misc/1.png';
    $upload_by = $row['upload_by_name'] ?? 'NA';
    $image_url = "<img data-src='$image' src='$image' style='width:50px; height:50px; border-radius:8px; border:2px solid #ccc; cursor:pointer; object-fit:cover;' onclick='openPopupCentered(this.src)' class='lazy-img' loading='lazy' />";
    $data[] = [
        'sr_no' => $sr++,
        'name' => $upload_by,
        'date' => date('d, M Y H:i a',strtotime($row['datetime'])),
        'photo' => $image_url,
        'ip_address' => $row['ip_address'],
        'device' => $row['device_info']
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