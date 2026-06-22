<?php
include("../config/database.php");
include("../config/auth_check.php");

error_reporting(E_ALL);
ini_set('display_errors',0);

header("Content-Type: application/json");

$barcode = trim($_POST['barcode'] ?? '');

if ($barcode == '') {
    echo json_encode([
        "status" => false,
        "message" => "Barcode is required"
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Find barcode product
|--------------------------------------------------------------------------
*/
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

$product_id = $item['product_id'];

if (!$item) {
    echo json_encode([
        "status" => false,
        "message" => "Barcode not found"
    ]);
    exit;
}

//if (!empty($item['stock_status']) && $item['stock_status'] == 'OUT') {
//    echo json_encode([
//        "status" => false,
//        "message" => "This barcode already stock out"
//    ]);
//    exit;
//}

/*
|--------------------------------------------------------------------------
| Get linked order
|--------------------------------------------------------------------------
| If you store order_id in stock_inward_qr, this will work.
|--------------------------------------------------------------------------
*/
$order = null;

if ($product_id) {

    $stmt = $con->prepare("SELECT 
        order_id, order_date, customer_name, cmobile, amount, payment_method, order_from, status
        FROM shopify_order_product op
       INNER JOIN shopify_order o ON o.order_id = op.orderr_id
       where op.product_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $order_result = $stmt->get_result();
    $order = $order_result->fetch_assoc();
    $stmt->close();
}

$order_id        = $order['order_id'] ?? '-';
$order_date      = !empty($order['order_date']) ? date('d M Y', strtotime($order['order_date'])) : '-';
$customer_name   = $order['customer_name'] ?? '-';
$customer_mobile = $order['cmobile'] ?? '-';
$amount          = isset($order['amount']) ? number_format($order['amount'], 2) : '-';
$payment_method  = ($order['payment_method'] == 1) ? 'Prepaid' : 'COD';
$channel         = ($order['order_from'] == 1) ? 'Shopify' : 'U3K';
$order_status    = $order['status'] ?? '-';

$html = '
<div class="card card-flush shadow-sm">
    <div class="card-header">
        <div class="card-title">
            <h3 class="fw-bold">Order & Product Details</h3>
        </div>

        <div class="card-toolbar">
            <span class="badge badge-light-success">Barcode Found</span>
        </div>
    </div>

    <div class="card-body border-top p-9">

        <div class="row mb-8">
            <div class="col-lg-4">
                <div class="text-muted fw-semibold mb-1">Barcode</div>
                <div class="fw-bold text-gray-900">'.$item['qr_code'].'</div>
            </div>

            <div class="col-lg-4">
                <div class="text-muted fw-semibold mb-1">Product</div>
                <div class="fw-bold text-gray-900">'.$item['product_name'].'</div>
                <div class="text-muted fs-7">'.$item['sku'].'</div>
            </div>

            <div class="col-lg-4">
                <div class="text-muted fw-semibold mb-1">Size / Stock Status</div>
                <div class="fw-bold text-gray-900">'.$item['size'].' / AVAILABLE</div>
            </div>
        </div>

        <div class="separator my-6"></div>

        <h4 class="fw-bold mb-6">Linked Order Details</h4>

        <div class="row mb-6">
            <div class="col-lg-4">
                <div class="text-muted fw-semibold mb-1">Order ID</div>
                <div class="fw-bold text-gray-900">'.$order_id.'</div>
            </div>

            <div class="col-lg-4">
                <div class="text-muted fw-semibold mb-1">Order Date</div>
                <div class="fw-bold">'.$order_date.'</div>
            </div>

            <div class="col-lg-4">
                <div class="text-muted fw-semibold mb-1">Order Status</div>
                <span class="badge badge-light-primary">'.$order_status.'</span>
            </div>
        </div>

        <div class="row mb-6">
            <div class="col-lg-4">
                <div class="text-muted fw-semibold mb-1">Customer Name</div>
                <div class="fw-bold">'.$customer_name.'</div>
            </div>

            <div class="col-lg-4">
                <div class="text-muted fw-semibold mb-1">Mobile</div>
                <div class="fw-bold">'.$customer_mobile.'</div>
            </div>

            <div class="col-lg-4">
                <div class="text-muted fw-semibold mb-1">Amount</div>
                <div class="fw-bold">₹ '.$amount.'</div>
            </div>
        </div>

        <div class="row mb-8">
            <div class="col-lg-4">
                <div class="text-muted fw-semibold mb-1">Payment</div>
                <span class="badge badge-light-dark">'.$payment_method.'</span>
            </div>

            <div class="col-lg-4">
                <div class="text-muted fw-semibold mb-1">Channel</div>
                <span class="badge badge-light-warning">'.$channel.'</span>
            </div>
        </div>

        <form id="dispatch_form">

            <input type="hidden" name="barcode_id" value="'.$item['id'].'">
            <input type="hidden" name="barcode" value="'.$item['qr_code'].'">

            <div class="separator my-6"></div>

            <h4 class="fw-bold mb-6">Dispatch Details</h4>

            <div class="row mb-6">
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">Courier Service</label>
                    <select name="courier_name" class="form-select form-select-solid" required>
                        <option value="">Select Courier</option>
                        <option value="BlueDart">BlueDart</option>
                        <option value="Delhivery">Delhivery</option>
                        <option value="DTDC">DTDC</option>
                        <option value="Ecom Express">Ecom Express</option>
                    </select>
                </div>

                <div class="col-lg-6">
                    <label class="form-label fw-semibold">Tracking Number</label>
                    <input type="text" name="tracking_number" class="form-control form-control-solid" placeholder="AWB / Tracking ID" required>
                </div>
            </div>

            <div class="row mb-6">
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">Dispatch Date</label>
                    <input type="date" name="dispatch_date" class="form-control form-control-solid" value="'.date('Y-m-d').'" required>
                </div>

                <div class="col-lg-6">
                    <label class="form-label fw-semibold">Expected Delivery</label>
                    <input type="date" name="expected_delivery_date" class="form-control form-control-solid">
                </div>
            </div>

            <div class="text-end">
                <button type="button" id="confirm_dispatch_btn" class="btn btn-primary">
                    Confirm Dispatch - Mark Stock Out
                </button>
            </div>

        </form>

    </div>
</div>
';

echo json_encode([
    "status" => true,
    "html" => $html
]);
exit;