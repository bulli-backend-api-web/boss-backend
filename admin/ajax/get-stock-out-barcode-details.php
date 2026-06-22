<?php
include("../config/database.php");
include("../config/auth_check.php");

header("Content-Type: application/json");

$barcode = trim($_POST['barcode'] ?? '');

if ($barcode == '') {
    echo json_encode([
        "status" => false,
        "message" => "Barcode is required"
    ]);
    exit;
}

$stmt = $con->prepare("
    SELECT 
        q.*,
        p.sku,
        p.name AS product_name
    FROM stock_inward_qr q
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
    echo json_encode([
        "status" => false,
        "message" => "Barcode not found"
    ]);
    exit;
}

//if ($item['stock_status'] == 'OUT') {
//    echo json_encode([
//        "status" => false,
//        "message" => "This barcode already stock out"
//    ]);
//    exit;
//}

/*
|--------------------------------------------------------------------------
| Linked Order Details
| Change table/fields as per your order table
|--------------------------------------------------------------------------
*/
$order = null;

if (!empty($item['order_id'])) {

    $stmt = $con->prepare("
        SELECT 
            order_id,
            customer_name,
            channel,
            payment_method
        FROM all_orders
        WHERE order_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $item['order_id']);
    $stmt->execute();
    $order_result = $stmt->get_result();
    $order = $order_result->fetch_assoc();
    $stmt->close();
}

$order_id       = $order['order_id'] ?? '-';
$customer_name  = $order['customer_name'] ?? '-';
$channel        = $order['channel'] ?? '-';
$payment_method = $order['payment_method'] ?? '-';

$html = '
<div class="unit-card">

    <div class="d-flex align-items-center mb-5">
        <span class="badge badge-light-success me-3">✓ UNIT FOUND</span>
        <span class="fw-bold">'.$item['qr_code'].'</span>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <div class="text-muted fs-8 text-uppercase">Product</div>
            <div class="fw-bold fs-5">'.$item['product_name'].'</div>
            <div class="text-muted">'.$item['sku'].'</div>
        </div>

        <div class="col-md-6">
            <div class="text-muted fs-8 text-uppercase">Size / Status</div>
            <div class="fw-bold fs-5">'.$item['size'].' — AVAILABLE</div>
        </div>
    </div>

    <div class="link-box mb-5">
        <div class="text-muted text-uppercase mb-3">🔗 Linked Booking</div>

        <div class="row">
            <div class="col-md-6 mb-3">
                Order ID: <b>'.$order_id.'</b>
            </div>

            <div class="col-md-6 mb-3">
                Customer: <b>'.$customer_name.'</b>
            </div>

            <div class="col-md-6">
                Channel: <span class="badge badge-light-warning">'.$channel.'</span>
            </div>

            <div class="col-md-6">
                Payment: <span class="badge badge-light-dark">'.$payment_method.'</span>
            </div>
        </div>
    </div>

    <form id="dispatch_form">
        <input type="hidden" name="barcode_id" value="'.$item['id'].'">
        <input type="hidden" name="barcode" value="'.$item['qr_code'].'">

        <div class="row g-4">

            <div class="col-md-6">
                <label class="form-label text-muted text-uppercase">Courier Service</label>
                <select name="courier_name" class="form-select form-select-solid" required>
                    <option value="">Select Courier</option>
                    <option value="BlueDart">BlueDart</option>
                    <option value="Delhivery">Delhivery</option>
                    <option value="DTDC">DTDC</option>
                    <option value="Ecom Express">Ecom Express</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted text-uppercase">Tracking Number</label>
                <input type="text" name="tracking_number" class="form-control form-control-solid" placeholder="AWB / Tracking ID" required>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted text-uppercase">Dispatch Date</label>
                <input type="date" name="dispatch_date" class="form-control form-control-solid" value="'.date('Y-m-d').'" required>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted text-uppercase">Expected Delivery</label>
                <input type="date" name="expected_delivery_date" class="form-control form-control-solid">
            </div>

            <div class="col-md-12 text-end mt-5">
                <button type="button" class="btn btn-light me-3">Cancel</button>
                <button type="button" id="confirm_dispatch_btn" class="btn btn-warning">
                    ✓ Confirm Dispatch — Mark Stock Out
                </button>
            </div>

        </div>
    </form>

</div>';

echo json_encode([
    "status" => true,
    "html" => $html
]);
exit;