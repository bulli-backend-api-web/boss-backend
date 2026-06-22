<?php
require_once '../config/database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$draw   = $_POST['draw'] ?? 1;
$start  = (int)($_POST['start'] ?? 0);
$length = (int)($_POST['length'] ?? 10);

$search = trim($_POST['search']['value'] ?? '');
$status = trim($_POST['status'] ?? '');

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir = ($_POST['order'][0]['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
$payment_type = !empty($_POST['payment_type']) ? $_POST['payment_type'] : '';
$order_from = !empty($_POST['order_from']) ? $_POST['order_from'] : '';
$from_date = !empty($_POST['from_date']) ? $_POST['from_date']." 00:00:00" : "";
$to_date = !empty($_POST['to_date']) ? $_POST['to_date']." 23:59:59" : "";
/*
|--------------------------------------------------------------------------
| COLUMN MAPPING
|--------------------------------------------------------------------------
*/
$columns = [
    0 => 'id',
    1 => 'fullname',
    2 => 'order_from',
    3 => 'grandtotal',
    4 => 'status'
];

$orderColumn = $columns[$orderColumnIndex] ?? 'id';

/*
|--------------------------------------------------------------------------
| WHERE CONDITIONS
|--------------------------------------------------------------------------
*/
$where1 = " WHERE status=3 ";
$where2 = " WHERE status=3 ";

if($payment_type!='' && $payment_type!='all'){
    $where2 .= " AND payment_method = '$payment_type'";
}

if($order_from!='' && $order_from!='all'){
    $where2 .= " AND order_from = '$order_from'";
}

if(!empty($from_date) && !empty($to_date)){
    $where2 .= " AND order_date between '$from_date' and '$to_date'";
}

if(!empty($from_date) && !empty($to_date)){
    $where1 .= " AND datee between '$from_date' and '$to_date'";
}

if($payment_type!='' && $payment_type!='all'){
    $where1 .= " AND payment_method = '$payment_type'";
}

/* 🔍 SEARCH */
if (!empty($search)) {

    $search = mysqli_real_escape_string($con, $search);

    $where1 .= " AND fullname LIKE '%$search%'";
    $where2 .= " AND customer_name LIKE '%$search%'";
}

/* 📌 STATUS FILTER */
if ($status !== '') {

    $status = (int)$status;

    $where1 .= " AND status = $status";
    $where2 .= " AND status = $status";
}

/*
|--------------------------------------------------------------------------
| TOTAL RECORDS
|--------------------------------------------------------------------------
*/
$totalSql = "
SELECT
(
    (SELECT COUNT(*) FROM orderr)
    +
    (SELECT COUNT(*) FROM shopify_order)
) AS total
";

$totalResult = $con->query($totalSql);
$totalRow = $totalResult->fetch_assoc();

$totalRecords = (int)$totalRow['total'];

/*
|--------------------------------------------------------------------------
| FILTERED RECORDS
|--------------------------------------------------------------------------
*/
$filteredSql = "
SELECT COUNT(*) as total FROM
(
    SELECT id FROM orderr $where1

    UNION ALL

    SELECT id FROM shopify_order $where2

) as combined
";

$filteredResult = $con->query($filteredSql);
$filteredRow = $filteredResult->fetch_assoc();

$totalFiltered = (int)$filteredRow['total'];

/*
|--------------------------------------------------------------------------
| MAIN QUERY
|--------------------------------------------------------------------------
*/
$sql = "

(
    SELECT
        id,
        datee as order_date,
        invoice_no,
        fullname,
        order_from,
        grandtotal,
        status,
        payment_method
    FROM orderr
    $where1
)

UNION ALL

(
    SELECT
        id,
        order_date,
        order_id AS invoice_no,
        customer_name AS fullname,
        order_from,
        amount AS grandtotal,
        status,
        payment_method
    FROM shopify_order
    $where2
)

ORDER BY $orderColumn $orderDir
LIMIT $start, $length

";

$stmt = $con->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'error' => $con->error
    ]);
    exit;
}

$stmt->execute();

$result = $stmt->get_result();

$rows = $result->fetch_all(MYSQLI_ASSOC);

/*
|--------------------------------------------------------------------------
| FORMAT DATA
|--------------------------------------------------------------------------
*/
$data = [];

foreach ($rows as $row) {

    /* STATUS BADGE */
    if ($row['status'] == 1) {
        $statusBadge = '<span class="badge badge-light-warning fw-bold px-4 py-3">Pending</span>';
    } elseif ($row['status'] == 2) {
        $statusBadge = '<span class="badge badge-light-success fw-bold px-4 py-3">Confirm</span>';
    } elseif ($row['status'] == 3) {
        $statusBadge = '<span class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>';
    }  else {
        $statusBadge = '<span class="badge badge-light-secondary">Unknown</span>';
    }

    /* PAYMENT BADGE */
    if($row['order_from'] == 1 || $row['order_from'] == 2){
        if ($row['payment_method'] == 1) {
            $paymentBadge = '<span class="badge badge-light-primary fw-bold px-4 py-3">Prepaid</span>';
        } elseif ($row['payment_method'] == 2) {
            $paymentBadge = '<span class="badge badge-light-success fw-bold px-4 py-3">COD</span>';
        } else {
            $paymentBadge = '<span class="badge badge-light-danger fw-bold px-4 py-3">Unknown</span>';
        }
    }else{
        if ($row['payment_method'] == 1) {
            $paymentBadge = '<span class="badge badge-light-primary fw-bold px-4 py-3">COD</span>';
        } elseif ($row['payment_method'] == 2) {
            $paymentBadge = '<span class="badge badge-light-success fw-bold px-4 py-3">Prepaid</span>';
        } else {
            $paymentBadge = '<span class="badge badge-light-danger fw-bold px-4 py-3">Unknown</span>';
        }
    }
    

    $actions = '<a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions 
                        <i class="ki-outline ki-down fs-5 ms-1"></i></a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="'.$site_path.'/edit-order?id='.my_simple_crypt($row['id'],'encrypt_1').'" class="menu-link px-3">Edit Order</a>
                            </div>
                        </div>';
    $data[] = [
        'order_id'       => $row['invoice_no'],
        'order_date'       => date('Y-m-d H:i',strtotime($row['order_date'])),
        'customer_name'  => $row['fullname'],
        'order_type'     => ($row['order_from'] == 1) ? 'Shopify' : 'U3K',
        'product'        => '',
        'size'           => '',
        'payment_type'   => $paymentBadge,
        'grand_total'    => $row['grandtotal'],
        'delivery_date'  => '',
        'status'         => $statusBadge,
        'actions'        => $actions
    ];
}

/*
|--------------------------------------------------------------------------
| JSON RESPONSE
|--------------------------------------------------------------------------
*/
echo json_encode([
    "draw"            => (int)$draw,
    "recordsTotal"    => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data"            => $data
]);

exit;
?>