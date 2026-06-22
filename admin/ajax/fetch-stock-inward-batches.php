<?php

include("../config/database.php");
include("../config/auth_check.php");

$columns = [
    0 => "sib.id",
    1 => "sib.batch_no",
    2 => "sib.inward_date",
    3 => "sib.qty",
    4 => "sib.printed_qty",
    5 => "sib.scanned_qty",
    6 => "sib.challan_status"
];

$draw   = isset($_POST['draw']) ? (int)$_POST['draw'] : 1;
$start  = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$length = isset($_POST['length']) ? (int)$_POST['length'] : 10;

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir         = $_POST['order'][0]['dir'] ?? 'DESC';

$orderColumn = $columns[$orderColumnIndex] ?? "sib.id";

$searchValue = trim($_POST['search']['value'] ?? '');

$status_filter = trim($_POST['status'] ?? '');
$from_date     = trim($_POST['from_date'] ?? '');
$to_date       = trim($_POST['to_date'] ?? '');

$where = " WHERE sib.is_deleted = 0 ";

if ($status_filter != '') {
    $where .= " AND sib.challan_status = '".$con->real_escape_string($status_filter)."' ";
}

if ($from_date != '') {
    $where .= " AND DATE(sib.inward_date) >= '".$con->real_escape_string($from_date)."' ";
}

if ($to_date != '') {
    $where .= " AND DATE(sib.inward_date) <= '".$con->real_escape_string($to_date)."' ";
}

if ($searchValue != '') {

    $search = $con->real_escape_string($searchValue);

    $where .= "
        AND (
            sib.batch_no LIKE '%{$search}%'
            OR sib.challan_no LIKE '%{$search}%'
            OR sib.outfit_type LIKE '%{$search}%'
        )
    ";
}

/*
|--------------------------------------------------------------------------
| Total Records
|--------------------------------------------------------------------------
*/
$totalQuery = $con->query("
    SELECT COUNT(id) as total
    FROM stock_inward_batch
    WHERE is_deleted = 0
");

$totalRecords = $totalQuery->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| Filtered Records
|--------------------------------------------------------------------------
*/
$filteredQuery = $con->query("
    SELECT COUNT(sib.id) as total
    FROM stock_inward_batch sib
    {$where}
");

$filteredRecords = $filteredQuery->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| Main Data
|--------------------------------------------------------------------------
*/
$sql = "
SELECT
    sib.*
FROM stock_inward_batch sib
{$where}
ORDER BY {$orderColumn} {$orderDir}
LIMIT {$start}, {$length}
";

$result = $con->query($sql);

$data = [];

$sr = $start + 1;

while ($row = $result->fetch_assoc()) {

    $pending_qty = (int)$row['qty'] - (int)$row['scanned_qty'];

    /*
    |--------------------------------------------------------------------------
    | Status Badge
    |--------------------------------------------------------------------------
    */
    switch ($row['challan_status']) {

        case 'CREATED':
            $status = '<span class="badge badge-light-warning">Created</span>';
            break;

        case 'PRINTED':
            $status = '<span class="badge badge-light-primary">Printed</span>';
            break;

        case 'SCANNING':
            $status = '<span class="badge badge-light-info">Scanning</span>';
            break;

        case 'COMPLETED':
            $status = '<span class="badge badge-light-success">Completed</span>';
            break;

        case 'CANCELLED':
            $status = '<span class="badge badge-light-danger">Cancelled</span>';
            break;

        default:
            $status = '<span class="badge badge-light-dark">Unknown</span>';
    }

    /*
    |--------------------------------------------------------------------------
    | Products Count
    |--------------------------------------------------------------------------
    */
    $product_count = 0;

    $product_sql = "
        SELECT COUNT(DISTINCT product_id) AS total_products
        FROM stock_inward_qr
        WHERE batch_id = '".$row['id']."'
    ";

    $product_res = $con->query($product_sql);

    if ($product_row = $product_res->fetch_assoc()) {
        $product_count = $product_row['total_products'];
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */
    $actions = '
<div class="d-flex justify-content-end flex-shrink-0 gap-2">

    <a href="'.$site_path.'/stock-inward-view?batch_id='.my_simple_crypt($row['id'],'encrypt_1').'"
       class="btn btn-icon btn-light-primary btn-sm"
       data-bs-toggle="tooltip"
       title="View">
        <i class="ki-duotone ki-eye fs-3">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
        </i>
    </a>

    <a href="'.$site_path.'/stock-inward-print?batch_id='.my_simple_crypt($row['id'],'encrypt_1').'"
       class="btn btn-icon btn-light-success btn-sm"
       data-bs-toggle="tooltip"
       title="Print Labels">
        <i class="ki-duotone ki-printer fs-3">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </a>

    <a href="'.$site_path.'/stock-inward-print?batch_id='.my_simple_crypt($row['id'],'encrypt_1').'"
       class="btn btn-icon btn-light-warning btn-sm"
       data-bs-toggle="tooltip"
       title="Reprint Labels">
        <i class="ki-duotone ki-arrows-circle fs-3">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </a>

    <a href="'.$site_path.'/stock-inward-scan?batch_id='.my_simple_crypt($row['id'],'encrypt_1').'"
       class="btn btn-icon btn-light-info btn-sm"
       data-bs-toggle="tooltip"
       title="Scan Stock">
        <i class="ki-duotone ki-barcode fs-3">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
        </i>
    </a>

    <a href="'.$site_path.'/edit-inward-challan?batch_id='.my_simple_crypt($row['id'],'encrypt_1').'"
       class="btn btn-icon btn-light-dark btn-sm"
       data-bs-toggle="tooltip"
       title="Edit Challan">
        <i class="ki-duotone ki-pencil fs-3">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </a>

    <a href="javascript:void(0)"
       class="btn btn-icon btn-light-danger btn-sm delete-challan"
       data-id="'.$row['id'].'"
       data-bs-toggle="tooltip"
       title="Delete">
        <i class="ki-duotone ki-trash fs-3">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
        </i>
    </a>

</div>';
    $data[] = [
        'sr_no' => $sr++,

        'challan_info' => '
            <div class="fw-bold">'.$row['challan_no'].'</div>
            <div class="text-muted fs-7">'.$row['batch_no'].'</div>
        ',

        'inward_date' => date(
            'd M Y',
            strtotime($row['inward_date'])
        ),

        'products' => '
            <span class="badge badge-light-primary">
                '.$product_count.' Products
            </span>
        ',

        'qty' => number_format($row['qty']),

        'printed_qty' => number_format($row['printed_qty']),

        'scanned_qty' => number_format($row['scanned_qty']),

        'pending_qty' => '
            <span class="badge badge-light-warning">
                '.$pending_qty.'
            </span>
        ',

        'status' => $status,

        'actions' => $actions
    ];
}

/*
|--------------------------------------------------------------------------
| Summary Cards
|--------------------------------------------------------------------------
*/
$summary = [
    'total_challans' => 0,
    'pending_print' => 0,
    'pending_scan' => 0,
    'completed_challans' => 0
];

$summary_sql = "
SELECT
    COUNT(id) AS total_challans,

    SUM(
        CASE
            WHEN challan_status='CREATED'
            THEN 1 ELSE 0
        END
    ) AS pending_print,

    SUM(
        CASE
            WHEN challan_status IN ('PRINTED','SCANNING')
            THEN 1 ELSE 0
        END
    ) AS pending_scan,

    SUM(
        CASE
            WHEN challan_status='COMPLETED'
            THEN 1 ELSE 0
        END
    ) AS completed_challans

FROM stock_inward_batch
WHERE is_deleted = 0
";

$summary_result = $con->query($summary_sql);

if ($summary_row = $summary_result->fetch_assoc()) {

    $summary = [
        'total_challans'     => (int)$summary_row['total_challans'],
        'pending_print'      => (int)$summary_row['pending_print'],
        'pending_scan'       => (int)$summary_row['pending_scan'],
        'completed_challans' => (int)$summary_row['completed_challans']
    ];
}

echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "summary" => $summary,
    "data" => $data
]);
exit;