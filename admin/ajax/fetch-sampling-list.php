<?php
require_once '../config/database.php';
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors',1);

$draw   = $_POST['draw'] ?? 1;
$start  = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;

$search = $_POST['search']['value'] ?? '';
$status = $_POST['status'] ?? '';

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir = ($_POST['order'][0]['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

$budget = isset($_POST['budget']) ? $_POST['budget'] : "";
$category = isset($_POST['category']) ? $_POST['category'] : "";
$from_date = !empty($_POST['from_date']) ? $_POST['from_date']." 00:00:00" : "";
$to_date = !empty($_POST['to_date']) ? $_POST['to_date']." 23:59:59" : "";
$columns = [
    0 => 's.id',
    1 => 's.sample_code',
    2 => 's.status'
];

$orderColumn = $columns[$orderColumnIndex] ?? 'd.id';
$where = " WHERE 1=1 ";
if($budget && $budget!='all'){
    $explode_budget = explode("-",$budget);
    $min_budget = $explode_budget[0];
    $max_budget = $explode_budget[1];
    $where .= " AND budget between '$min_budget' and '$max_budget'";
}

if($category && $category!='all'){
    $where .= " AND category = $category";
}

if($from_date && $to_date){
    $where .= " AND created_at between '$from_date' and '$to_date'";
}

$params = [];

/* 🔍 Search */
if (!empty($search)) {
    $where .= " AND s.sample_code LIKE '%$search%' OR s.design_code LIKE '%$search%'";
}

/* 📌 Status filter */
if ($status !== '') {
    $where .= " AND s.status = $status";
}

/* 📊 TOTAL RECORDS (no db_row) */
$stmtTotal = $con->prepare("SELECT COUNT(*) as total FROM sampling");
$stmtTotal->execute();

$result = $stmtTotal->get_result();
$row = $result->fetch_assoc();

$totalRecords = $row['total'];

/* 📊 FILTERED RECORDS (no db_row) */
$stmtFiltered = $con->prepare("SELECT COUNT(*) as total FROM sampling s $where");

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
        s.id,
        s.sample_code,
        s.design_code,
        s.name,
        s.category,
        s.assign_to,
        s.budget,
        s.target_days,
        s.fabric,
        s.status,
        s.created_at,
        c.name as cat_name,
        DATEDIFF(NOW(), s.created_at) AS days_elapsed,
        u1.name AS assign_by_name
    FROM sampling s
    LEFT JOIN user u1 ON u1.id = s.assign_to
    LEFT JOIN category c on c.id = s.category
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
    /* Status badge */
    if ($row['status'] == 1) {
        $statusBadge = '<span class="badge badge-light-warning fw-bold px-4 py-3">In Progress</span>';
    } elseif ($row['status'] == 2) {
        $statusBadge = '<span class="badge badge-light-success fw-bold px-4 py-3">Under review</span>';
    } elseif ($row['status'] == 3) {
        $statusBadge = '<span class="badge badge-light-danger fw-bold px-4 py-3">Rework</span>';
    }elseif ($row['status'] == 4) {
        $statusBadge = '<span class="badge badge-light-primary fw-bold px-4 py-3">Over Budget</span>';
    } else {
        $statusBadge = '<span class="badge badge-light-secondary">Unknown</span>';
    }

    $assign_by_name = $row['assign_by_name'] ?? 'NA';

    /* Actions */
     $actions = '<a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions <i class="ki-outline ki-down fs-5 ms-1"></i></a>
            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3 action_start_sampling" data-id="'.$id.'">Start Sampling</a>
                </div>
                <div class="menu-item px-3">
                    <a href="'.$site_path.'/view-sample?id='.my_simple_crypt($row['id'],'encrypt_1').'" class="menu-link px-3">View</a>
                </div>
            </div>';

    $data[] = [
        'sr_no' => $sr++,
        'sample_id' => $row['sample_code'],
        'design_code' => $row['design_code'],
        'category' => $row['cat_name'],
        'sampler' => $assign_by_name,
        'timeline' => "Day ".(int)$row['days_elapsed']."/".(int)$row['target_days'],
        'fabric_issued' => $row['fabric'],
        'budget'=>$row['budget'],
        'spent' => $row['budget'],
        'status' => $statusBadge,
        'created_date' => !empty($row['created_at']) ? date('d-m-Y', strtotime($row['created_at'])) : "NA",
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