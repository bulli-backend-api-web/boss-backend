<?php
include("../config/database.php");
include("../config/auth_check.php");

header("Content-Type: application/json");

$response = [
    "status"  => false,
    "message" => "Something went wrong",
    "html"    => ""
];

$barcode = trim($_POST['barcode'] ?? '');

if ($barcode == '') {
    $response['message'] = "Barcode is required";
    echo json_encode($response);
    exit;
}

/*
|--------------------------------------------------------------------------
| Find barcode
|--------------------------------------------------------------------------
*/
$stmt = $con->prepare("
    SELECT 
        q.*,
        b.batch_no,
        b.challan_no,
        b.qty AS batch_qty,
        b.scanned_qty,
        b.challan_status,
        p.sku,
        p.name AS product_name
    FROM stock_inward_qr q
    LEFT JOIN stock_inward_batch b ON b.id = q.batch_id
    LEFT JOIN product p ON p.id = q.product_id
    WHERE q.qr_code = ?
    LIMIT 1
");

$stmt->bind_param("s", $barcode);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    $response['message'] = "Barcode not found";
    echo json_encode($response);
    exit;
}

if ($item['challan_status'] == 'CANCELLED') {
    $response['message'] = "This challan is cancelled";
    echo json_encode($response);
    exit;
}

if ((int)$item['scan_status'] == 1) {
    $response['message'] = "This barcode already scanned";
    echo json_encode($response);
    exit;
}

/*
|--------------------------------------------------------------------------
| Insert stock ledger + update scan
|--------------------------------------------------------------------------
*/
mysqli_begin_transaction($con);

try {

    $batch_id   = (int)$item['batch_id'];
    $product_id = (int)$item['product_id'];
    $size       = $item['size'];
    $qr_code    = $item['qr_code'];

    /*
    |--------------------------------------------------------------------------
    | Prevent duplicate stock ledger entry
    |--------------------------------------------------------------------------
    */
    $stmt = $con->prepare("
        SELECT id 
        FROM stock_ledger
        WHERE qr_code = ?
        AND movement_type = 'IN'
        AND reference_type = 'INWARD_CHALLAN'
        LIMIT 1
    ");
    $stmt->bind_param("s", $qr_code);
    $stmt->execute();
    $ledger_check = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($ledger_check) {
        throw new Exception("Stock already added for this barcode");
    }

    /*
    |--------------------------------------------------------------------------
    | Add stock ledger IN
    |--------------------------------------------------------------------------
    */
    $movement_type  = "IN";
    $reference_type = "INWARD_CHALLAN";
    $qty = 1;

    $stmt = $con->prepare("
        INSERT INTO stock_ledger
        (
            product_id,
            size,
            qr_code,
            movement_type,
            qty,
            reference_type,
            reference_id,
            created_at
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "isssisi",
        $product_id,
        $size,
        $qr_code,
        $movement_type,
        $qty,
        $reference_type,
        $batch_id
    );

    $stmt->execute();
    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | Update barcode scan status
    |--------------------------------------------------------------------------
    */
    $stmt = $con->prepare("
        UPDATE stock_inward_qr
        SET 
            scan_status = 1,
            stock_status = 'AVAILABLE',
            scanned_at = NOW()
        WHERE id = ?
    ");

    $barcode_id = (int)$item['id'];

    $stmt->bind_param("i", $barcode_id);
    $stmt->execute();
    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | Update batch scanned qty
    |--------------------------------------------------------------------------
    */
    $stmt = $con->prepare("
        UPDATE stock_inward_batch
        SET 
            scanned_qty = scanned_qty + 1,
            challan_status = 'SCANNING',
            status = 'SCANNING'
        WHERE id = ?
    ");
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | Check completed
    |--------------------------------------------------------------------------
    */
    $stmt = $con->prepare("
        SELECT 
            COUNT(*) AS total_units,
            SUM(CASE WHEN scan_status = 1 THEN 1 ELSE 0 END) AS scanned_units
        FROM stock_inward_qr
        WHERE batch_id = ?
    ");
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $total_units   = (int)$count['total_units'];
    $scanned_units = (int)$count['scanned_units'];
    $pending_units = $total_units - $scanned_units;

    if ($total_units > 0 && $total_units == $scanned_units) {
        $stmt = $con->prepare("
            UPDATE stock_inward_batch
            SET 
                challan_status = 'COMPLETED',
                status = 'COMPLETED',
                scanned_qty = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ii", $scanned_units, $batch_id);
        $stmt->execute();
        $stmt->close();

        $final_status = "COMPLETED";
    } else {
        $final_status = "SCANNING";
    }

    mysqli_commit($con);

    /*
    |--------------------------------------------------------------------------
    | Result HTML
    |--------------------------------------------------------------------------
    */
    $html = '
    <div class="card card-flush shadow-sm">
        <div class="card-body p-7">

            <div class="d-flex align-items-center mb-6">
                <span class="badge badge-light-success me-3">SCANNED</span>
                <span class="fw-bold text-gray-900">'.$qr_code.'</span>
            </div>

            <div class="row g-5">

                <div class="col-md-4">
                    <div class="text-muted fw-semibold mb-1">Batch No</div>
                    <div class="fw-bold">'.$item['batch_no'].'</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted fw-semibold mb-1">Challan No</div>
                    <div class="fw-bold">'.$item['challan_no'].'</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted fw-semibold mb-1">Batch Status</div>
                    <span class="badge badge-light-primary">'.$final_status.'</span>
                </div>

                <div class="col-md-4">
                    <div class="text-muted fw-semibold mb-1">SKU</div>
                    <div class="fw-bold">'.$item['sku'].'</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted fw-semibold mb-1">Product</div>
                    <div class="fw-bold">'.$item['product_name'].'</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted fw-semibold mb-1">Size</div>
                    <span class="badge badge-light-dark">'.$size.'</span>
                </div>

                <div class="col-md-4">
                    <div class="text-muted fw-semibold mb-1">Total Units</div>
                    <div class="fw-bold">'.$total_units.'</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted fw-semibold mb-1">Scanned Units</div>
                    <div class="fw-bold text-success">'.$scanned_units.'</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted fw-semibold mb-1">Pending Units</div>
                    <div class="fw-bold text-danger">'.$pending_units.'</div>
                </div>

            </div>

        </div>
    </div>';

    $response['status']  = true;
    $response['message'] = "Barcode scanned successfully";
    $response['html']    = $html;

    echo json_encode($response);
    exit;

} catch (Exception $e) {

    mysqli_rollback($con);

    $response['message'] = $e->getMessage();
    echo json_encode($response);
    exit;
}