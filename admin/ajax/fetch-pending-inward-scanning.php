<?php
include("../config/database.php");
include("../config/auth_check.php");

header("Content-Type: application/json");

$columns = [
    0 => "sib.id",
    1 => "sib.batch_no",
    2 => "sib.inward_date",
    3 => "sib.qty",
    4 => "sib.scanned_qty",
    5 => "sib.challan_status"
];

$draw   = isset($_POST['draw']) ? (int)$_POST['draw'] : 1;
$start  = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$length = isset($_POST['length']) ? (int)$_POST['length'] : 10;

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir         = $_POST['order'][0]['dir'] ?? 'DESC';
$orderColumn      = $columns[$orderColumnIndex] ?? "sib.id";
$orderDir         = strtoupper($orderDir) === "ASC" ? "ASC" : "DESC";

$searchValue = trim($_POST['search']['value'] ?? '');
$status      = trim($_POST['status'] ?? '');
$from_date   = trim($_POST['from_date'] ?? '');
$to_date     = trim($_POST['to_date'] ?? '');

$where = " WHERE sib.is_deleted = 0 
           AND sib.challan_status IN ('CREATED','PRINTED','SCANNING') ";

if ($status != '') {
    $where .= " AND sib.challan_status = '".$con->real_escape_string($status)."' ";
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
            sib.batch_no LIKE '%$search%'
            OR sib.challan_no LIKE '%$search%'
            OR sib.outfit_type LIKE '%$search%'
        )
    ";
}

$totalQuery = $con->query("
    SELECT COUNT(*) AS total
    FROM stock_inward_batch sib
    WHERE sib.is_deleted = 0
    AND sib.challan_status IN ('CREATED','PRINTED','SCANNING')
");

$totalRecords = (int)$totalQuery->fetch_assoc()['total'];

$filteredQuery = $con->query("
    SELECT COUNT(*) AS total
    FROM stock_inward_batch sib
    $where
");

$totalFiltered = (int)$filteredQuery->fetch_assoc()['total'];

$dataQuery = $con->query("
    SELECT sib.*
    FROM stock_inward_batch sib
    $where
    ORDER BY $orderColumn $orderDir
    LIMIT $start, $length
");

$data = [];
$sr = $start + 1;

while ($row = $dataQuery->fetch_assoc()) {

    $batch_id = (int)$row['id'];
    $qty = (int)$row['qty'];
    $scanned_qty = (int)$row['scanned_qty'];
    $pending_qty = max(0, $qty - $scanned_qty);

    $progress_percent = $qty > 0 ? round(($scanned_qty / $qty) * 100) : 0;

    $product_count = 0;
    $productRes = $con->query("
        SELECT COUNT(DISTINCT product_id) AS total_products
        FROM stock_inward_qr
        WHERE batch_id = '$batch_id'
    ");

    if ($productRow = $productRes->fetch_assoc()) {
        $product_count = (int)$productRow['total_products'];
    }

    if ($row['challan_status'] == 'CREATED') {
        $statusBadge = '<span class="badge badge-light-warning">Created - Print Pending</span>';
    } elseif ($row['challan_status'] == 'PRINTED') {
        $statusBadge = '<span class="badge badge-light-primary">Printed - Scan Pending</span>';
    } elseif ($row['challan_status'] == 'SCANNING') {
        $statusBadge = '<span class="badge badge-light-info">Scanning Started</span>';
    } else {
        $statusBadge = '<span class="badge badge-light-dark">'.$row['challan_status'].'</span>';
    }

    $actions = '
    <div class="d-flex justify-content-end gap-2">

        <a href="'.$site_path.'/stock-inward-view?batch_id='.my_simple_crypt($batch_id,'encrypt_1').'"
           class="btn btn-icon btn-light-primary btn-sm"
           title="View">
            <i class="ki-duotone ki-eye fs-3">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
        </a>

        <a href="'.$site_path.'/stock-inward-print?batch_id='.my_simple_crypt($batch_id,'encrypt_1').'"
           class="btn btn-icon btn-light-success btn-sm"
           title="Print / Reprint">
            <i class="ki-duotone ki-printer fs-3">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </a>

        <a href="'.$site_path.'/stock-inward-scan?batch_id='.my_simple_crypt($batch_id,'encrypt_1').'"
           class="btn btn-icon btn-light-info btn-sm"
           title="Scan Stock">
            <i class="ki-duotone ki-barcode fs-3">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
        </a>

    </div>';

    $data[] = [
        "sr_no" => $sr++,

        "challan_info" => '
            <div class="fw-bold text-gray-900">'.$row['challan_no'].'</div>
            <div class="text-muted fs-7">'.$row['batch_no'].'</div>
        ',

        "inward_date" => !empty($row['inward_date'])
            ? date("d M Y", strtotime($row['inward_date']))
            : "-",

        "products" => '
            <span class="badge badge-light-primary">
                '.$product_count.' Products
            </span>
        ',

        "qty" => number_format($qty),

        "scanned_qty" => '
            <span class="badge badge-light-success">
                '.number_format($scanned_qty).'
            </span>
        ',

        "pending_qty" => '
            <span class="badge badge-light-danger">
                '.number_format($pending_qty).'
            </span>
        ',

        "progress" => '
            <div class="d-flex align-items-center flex-column mt-1 w-100">
                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                    <span class="fw-semibold fs-7 text-gray-600">'.$progress_percent.'%</span>
                </div>
                <div class="h-6px mx-3 w-100 bg-light rounded">
                    <div class="bg-primary rounded h-6px"
                         role="progressbar"
                         style="width: '.$progress_percent.'%;"></div>
                </div>
            </div>
        ',

        "status" => $statusBadge,

        "actions" => $actions
    ];
}

$summaryQuery = $con->query("
    SELECT
        COUNT(id) AS pending_challans,

        IFNULL(SUM(qty - scanned_qty),0) AS pending_units,

        IFNULL(SUM(
            CASE WHEN challan_status = 'PRINTED'
            THEN qty - scanned_qty ELSE 0 END
        ),0) AS printed_pending,

        IFNULL(SUM(
            CASE WHEN challan_status = 'SCANNING'
            THEN 1 ELSE 0 END
        ),0) AS scanning_started

    FROM stock_inward_batch
    WHERE is_deleted = 0
    AND challan_status IN ('CREATED','PRINTED','SCANNING')
");

$summaryRow = $summaryQuery->fetch_assoc();

$summary = [
    "pending_challans" => number_format((int)$summaryRow['pending_challans']),
    "pending_units" => number_format((int)$summaryRow['pending_units']),
    "printed_pending" => number_format((int)$summaryRow['printed_pending']),
    "scanning_started" => number_format((int)$summaryRow['scanning_started'])
];

echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "summary" => $summary,
    "data" => $data
]);
exit;