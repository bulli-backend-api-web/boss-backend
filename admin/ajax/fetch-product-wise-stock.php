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
    0 => 'ps.id',
    1 => 'p.name',
    2 => 'd.status'
];

$orderColumn = $columns[$orderColumnIndex] ?? 'ps.id';

$where = " WHERE 1=1 ";
$params = [];

/* 🔍 Search */
if (!empty($search)) {
    $where .= " AND p.name LIKE '%$search%'";
}

/* 📌 Status filter */
if ($status !== '') {
    $where .= " AND d.status = $status";
}

/* 📊 TOTAL RECORDS (no db_row) */
$stmtTotal = $con->prepare("SELECT COUNT(*) as total FROM product_wise_stock");
$stmtTotal->execute();

$result = $stmtTotal->get_result();
$row = $result->fetch_assoc();

$totalRecords = $row['total'];

/* 📊 FILTERED RECORDS (no db_row) */
$stmtFiltered = $con->prepare("SELECT COUNT(*) as total FROM product_wise_stock ps $where");

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
        p.id,
        p.name,
        p.sku,
        p.img1,
        ps.available_stock,
        ps.created_at,
        ps.size,
        ps.inward_no,
        ps.inward_date,
        ps.barcode
    FROM product_wise_stock ps
    LEFT JOIN product p ON p.id = ps.product_id
    $where
    ORDER BY $orderColumn $orderDir
    LIMIT $start, $length
";

$stmt = $con->prepare($sql);

$stmt->execute();

$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

$data = [];
$sr = $start + 1;

foreach ($rows as $row) {
    $id = $row['barcode'];
    
    /* Actions */
    $actions = '
    <div class="d-flex gap-2">

        <!-- VIEW BUTTON -->
        <a href="'.$site_path.'/print-barcode?id='.my_simple_crypt($id,'encrypt_1').'" 
           class="btn btn-light-primary btn-sm d-inline-flex align-items-center gap-2" target="_blank">

            <i class="fa fa-print">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>

            <span>Print</span>
        </a>
    </div>';

    $data[] = [
        'sr_no' => $sr++,
        'entry_id' => $row['inward_no'],
        'date' => $row['inward_date'],
        'sku' => $row['sku'],
        'product' => $row['name'],
        'size' => $row['size'],
        'stock' => $row['available_stock'],
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