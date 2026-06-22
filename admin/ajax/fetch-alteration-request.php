<?php
include("../config/database.php");
include("../config/auth_check.php");

header("Content-Type: application/json");

$columns = [
    0 => 'ar.id',
    1 => 'ar.alteration_no',
    2 => 'p.name',
    3 => 'ar.from_size',
    4 => 'ar.to_size',
    5 => 'ar.qty',
    6 => 'ar.assigned_to',
    7 => 'ar.priority',
    8 => 'ar.status',
    9 => 'ar.created_at'
];

$draw   = isset($_POST['draw']) ? (int)$_POST['draw'] : 1;
$start  = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$length = isset($_POST['length']) ? (int)$_POST['length'] : 10;

$search = trim($_POST['search']['value'] ?? '');
$status = trim($_POST['status'] ?? '');

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir = $_POST['order'][0]['dir'] ?? 'DESC';

$orderColumn = $columns[$orderColumnIndex] ?? 'ar.id';
$orderDir = strtoupper($orderDir) == 'ASC' ? 'ASC' : 'DESC';

$where = " WHERE 1=1 ";

if ($status != '') {
    $status_safe = mysqli_real_escape_string($con, $status);
    $where .= " AND ar.status = '$status_safe' ";
}

if ($search != '') {

    $search_safe = mysqli_real_escape_string($con, $search);

    $where .= "
        AND (
            ar.alteration_no LIKE '%$search_safe%'
            OR p.sku LIKE '%$search_safe%'
            OR p.name LIKE '%$search_safe%'
            OR ar.assigned_to LIKE '%$search_safe%'
            OR ar.from_size LIKE '%$search_safe%'
            OR ar.to_size LIKE '%$search_safe%'
        )
    ";
}

$baseQuery = "
    FROM alteration_requests ar
    LEFT JOIN product p ON p.id = ar.product_id
";

$totalQuery = mysqli_query($con, "
    SELECT COUNT(*) AS total
    $baseQuery
");

$totalData = mysqli_fetch_assoc($totalQuery)['total'];

$filteredQuery = mysqli_query($con, "
    SELECT COUNT(*) AS total
    $baseQuery
    $where
");

$totalFiltered = mysqli_fetch_assoc($filteredQuery)['total'];

$dataQuery = mysqli_query($con, "
    SELECT 
        ar.*,
        p.sku,
        p.name AS product_name
    $baseQuery
    $where
    ORDER BY $orderColumn $orderDir
    LIMIT $start, $length
");

$data = [];
$srNo = $start + 1;

while ($row = mysqli_fetch_assoc($dataQuery)) {

    $statusBadge = '';

    if ($row['status'] == 'STOCK_RESERVED') {
        $statusBadge = '<span class="badge badge-light-warning">Stock Reserved</span>';
        $nextAction = '<a href="'.$site_path.'/alteration-status-update?id='.my_simple_crypt($row['id'],'encrypt_1').'&status=SENT_FOR_ALTERATION" class="menu-link px-3">Send For Alteration</a>';
    } elseif ($row['status'] == 'SENT_FOR_ALTERATION') {
        $statusBadge = '<span class="badge badge-light-info">Sent For Alteration</span>';
        $nextAction = '<a href="'.$site_path.'/alteration-status-update?id='.my_simple_crypt($row['id'],'encrypt_1').'&status=RECEIVED" class="menu-link px-3">Receive Product</a>';
    } elseif ($row['status'] == 'RECEIVED') {
        $statusBadge = '<span class="badge badge-light-primary">Received</span>';
        $nextAction = '<a href="'.$site_path.'/alteration-status-update?id='.my_simple_crypt($row['id'],'encrypt_1').'&status=QC_APPROVED" class="menu-link px-3">QC Approve</a>';
    } elseif ($row['status'] == 'QC_APPROVED') {
        $statusBadge = '<span class="badge badge-light-success">QC Approved</span>';
        $nextAction = '<a href="'.$site_path.'/alteration-status-update?id='.my_simple_crypt($row['id'],'encrypt_1').'&status=COMPLETED" class="menu-link px-3">Complete Request</a>';
    } elseif ($row['status'] == 'COMPLETED') {
        $statusBadge = '<span class="badge badge-light-success">Completed</span>';
        $nextAction = '';
    } elseif ($row['status'] == 'REJECTED') {
        $statusBadge = '<span class="badge badge-light-danger">Rejected</span>';
        $nextAction = '';
    } else {
        $statusBadge = '<span class="badge badge-light-dark">'.$row['status'].'</span>';
        $nextAction = '';
    }
    
    $alteration_id = $row['id'];
    
     $actions = '<div class="d-flex justify-content-end flex-shrink-0 gap-2">';

/* View Button */
$actions .= '
    <a href="'.$site_path.'/alteration-view?id='.my_simple_crypt($alteration_id,'encrypt_1').'" 
       class="btn btn-light-primary btn-sm d-inline-flex align-items-center gap-2">
        <i class="fa fa-eye"></i>
        <span>View</span>
    </a>
';

/* Next Action Button */
if ($nextAction != '') {
    $actions .= $nextAction;
}

/* Reject Button */
if ($row['status'] != 'COMPLETED' && $row['status'] != 'REJECTED') {

    $actions .= '
        <a href="'.$site_path.'/alteration-status-update?id='.my_simple_crypt($alteration_id,'encrypt_1').'&status=REJECTED"
           class="btn btn-light-danger btn-sm d-inline-flex align-items-center gap-2"
           onclick="return confirm(\'Are you sure you want to reject this request?\')">

            <i class="fa fa-times"></i>
            <span>Reject</span>
        </a>
    ';
}

$actions .= '</div>';
        
    $data[] = [
        'sr_no' => $srNo++,
        'alteration_no' => '
            <div class="fw-bold text-gray-900">
                '.$row['alteration_id'].'
            </div>',
        'product' => '
            <div class="fw-bold text-gray-900">
                '.$row['product_name'].'
            </div>
            <div class="text-muted fs-7">
                '.$row['sku'].'
            </div>',
        'from_size' => '<span class="badge badge-light-danger">'.$row['old_size'].'</span>',
        'to_size' => '<span class="badge badge-light-success">'.$row['new_size'].'</span>',
        'qty' => number_format($row['qty']),
        'assigned_to' => '
            <div class="fw-bold">'.$row['alteration_id'].'</div>',
        'status' => $statusBadge,
        'created_at' => date('d M Y', strtotime($row['created_at'])),
        'actions' => $actions
    ];
}

echo json_encode([
    "draw" => $draw,
    "recordsTotal" => (int)$totalData,
    "recordsFiltered" => (int)$totalFiltered,
    "data" => $data
]);

exit;