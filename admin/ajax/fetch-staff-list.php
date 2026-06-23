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
    1 => 'firstname',
    2 => 'mobile_number'
];

$orderColumn = $columns[$orderColumnIndex] ?? 'd.id';
$where = " WHERE 1=1 ";
$params = [];

if (!empty($search)) {
    $where .= " AND fullname LIKE '%$search%' OR mobile_number LIKE '%$search%' OR email LIKE '%$search%'";
}

$stmtTotal = $con->prepare("SELECT COUNT(*) as total FROM staff_register");
$stmtTotal->execute();

$result = $stmtTotal->get_result();
$row = $result->fetch_assoc();

$totalRecords = $row['total'];

$stmtFiltered = $con->prepare("SELECT COUNT(*) as total FROM staff_register $where");

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
$sql = "SELECT id,firstname,middlename,lastname,mobile_number,email,gender,dob,doj FROM staff_register $where ORDER BY $orderColumn $orderDir LIMIT $start, $length";
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$data = [];
$sr = $start + 1;
foreach ($rows as $row) {
    $actions ='<a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"  data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions <i class="ki-outline ki-down fs-5 ms-1"></i></a><div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600  menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                 <div class="menu-item px-3">
                    <a href="'.$site_path.'/edit-staff-register?id='.my_simple_crypt($row['id'],'encrypt_1').'" class="menu-link px-3">Edit</a>
                </div>
                <div class="menu-item px-3">
                    <a href="'.$site_path.'/view-staff?id='.my_simple_crypt($row['id'],'encrypt_1').'" class="menu-link px-3">View</a>
                </div>
            </div>';
    $id = $row['id'];
    $image = !empty($row['profile_picture']) ? '../../uploads/staff/' . $row['profile_picture'] : $site_path . '/assets/media/misc/1.png';
    $image_url = "<img data-src='$image' src='$image' style='width:50px; height:50px; border-radius:8px; border:2px solid #ccc; cursor:pointer; object-fit:cover;' onclick='openPopupCentered(this.src)' class='lazy-img' loading='lazy' />";
    $data[] = [
        'sr_no' => $sr++,
        'name' => $row['firstname']. " ".$row['middlename']." ".$row['lastname'],
        'mobile_number' => $row['mobile_number'],
        'email' => $row['email'],
        'gender' => $row['gender'],
        'dob' => $row['dob'],
        'doj' => $row['doj'],
        'actions' => $actions
    ];
}
echo json_encode([
    "draw" => (int)$draw,
    "recordsTotal" => (int)$totalRecords,
    "recordsFiltered" => (int)$totalFiltered,
    "data" => $data
]);
exit;