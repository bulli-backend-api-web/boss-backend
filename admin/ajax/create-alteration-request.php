<?php
include("../config/database.php");
include("../config/auth_check.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}
$product_id           = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$from_size            = trim($_POST['from_size'] ?? '');
$to_size              = trim($_POST['to_size'] ?? '');
$qty                  = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
$assigned_to          = trim($_POST['assigned_to'] ?? '');
$assigned_mobile      = trim($_POST['assigned_mobile'] ?? '');
$expected_return_date = trim($_POST['expected_return_date'] ?? '');
$priority             = trim($_POST['priority'] ?? 'Medium');
$remarks              = trim($_POST['remarks'] ?? '');

if ($product_id <= 0) {
    die("Product is required");
}

if ($from_size == '') {
    die("From size is required");
}

if ($to_size == '') {
    die("To size is required");
}

if ($from_size == $to_size) {
    die("From size and To size cannot be same");
}

if ($qty <= 0) {
    die("Quantity is required");
}

if ($assigned_to == '') {
    die("Assigned person/vendor is required");
}

if ($expected_return_date == '') {
    $expected_return_date = null;
}

mysqli_begin_transaction($con);

try {

    /*
    |--------------------------------------------------------------------------
    | Check available stock from ledger
    |--------------------------------------------------------------------------
    */
    $stmt = $con->prepare("
        SELECT 
            SUM(
                CASE 
                    WHEN movement_type IN ('IN','UNRESERVE') THEN qty
                    WHEN movement_type IN ('OUT','RESERVE') THEN -qty
                    ELSE 0
                END
            ) AS available_stock
        FROM stock_ledger
        WHERE product_id = ?
        AND size = ?
    ");

    $stmt->bind_param("is", $product_id, $from_size);
    $stmt->execute();
    $stock_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $available_stock = (int)($stock_row['available_stock'] ?? 0);

    if ($qty > $available_stock) {
        throw new Exception("Only ".$available_stock." stock available for selected size");
    }

    /*
    |--------------------------------------------------------------------------
    | Get available QR codes from stock_inward_qr
    |--------------------------------------------------------------------------
    */
    $stmt = $con->prepare("
        SELECT 
            id,
            batch_id,
            qr_code
        FROM stock_inward_qr
        WHERE product_id = ?
        AND size = ?
        AND stock_status = 'AVAILABLE'
        AND scan_status = 1
        ORDER BY id ASC
        LIMIT ?
        FOR UPDATE
    ");

    $stmt->bind_param("isi", $product_id, $from_size, $qty);
    $stmt->execute();
    $qr_result = $stmt->get_result();

    $selected_qrs = [];

    while ($qr = $qr_result->fetch_assoc()) {
        $selected_qrs[] = $qr;
    }

    $stmt->close();

    if (count($selected_qrs) < $qty) {
        throw new Exception("Only ".count($selected_qrs)." QR stock available for selected size");
    }

    /*
    |--------------------------------------------------------------------------
    | Store old QR code and inward batch id
    |--------------------------------------------------------------------------
    | If qty is more than 1, QR codes are stored comma separated.
    |--------------------------------------------------------------------------
    */
    $old_qr_codes_arr = [];
    $batch_ids_arr    = [];

    foreach ($selected_qrs as $qr) {
        $old_qr_codes_arr[] = $qr['qr_code'];
        $batch_ids_arr[]    = $qr['batch_id'];
    }

    $old_qr_code     = implode(",", $old_qr_codes_arr);
    $inward_batch_id = implode(",", array_unique($batch_ids_arr));

    /*
    |--------------------------------------------------------------------------
    | Generate alteration number
    |--------------------------------------------------------------------------
    */
    $today  = date('Ymd');
    $prefix = "ALT-" . $today . "-";

    $stmt = $con->prepare("
        SELECT alteration_id
        FROM alteration_requests
        WHERE alteration_id LIKE CONCAT(?, '%')
        ORDER BY id DESC
        LIMIT 1
    ");

    $stmt->bind_param("s", $prefix);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $last_no  = $row['alteration_id'];
        $last_seq = (int)substr($last_no, -4);
        $new_seq  = $last_seq + 1;
    } else {
        $new_seq = 1;
    }

    $alteration_no = $prefix . str_pad($new_seq, 4, "0", STR_PAD_LEFT);

    /*
    |--------------------------------------------------------------------------
    | Insert alteration request
    |--------------------------------------------------------------------------
    */
    $stmt = $con->prepare("
        INSERT INTO alteration_requests
        (
            alteration_id,
            product_id,
            old_size,
            new_size,
            qty,
            old_qrcode,
            inward_id,
            assign_to,
            expected_return_date,
            priority,
            status,
            created_by,
            remarks,
            created_at
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'STOCK_RESERVED',?, ?, NOW())
    ");

    $stmt->bind_param(
        "sississssiss",
        $alteration_no,
        $product_id,
        $from_size,
        $to_size,
        $qty,
        $old_qr_code,
        $inward_batch_id,
        $assigned_to,
        $expected_return_date,
        $priority,
        $uid,
        $remarks
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to create alteration request: " . $stmt->error);
    }

    $alteration_id = $stmt->insert_id;
    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | Reserve stock in stock ledger
    |--------------------------------------------------------------------------
    */
    $reference_type = "ALTERATION_RESERVE";
    $movement_type  = "RESERVE";

    $stmt = $con->prepare("
        INSERT INTO stock_ledger
        (
            product_id,
            size,
            movement_type,
            qty,
            reference_type,
            reference_id,
            created_at
        )
        VALUES
        (?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "issisi",
        $product_id,
        $from_size,
        $movement_type,
        $qty,
        $reference_type,
        $alteration_id
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to reserve stock: " . $stmt->error);
    }

    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | Mark selected QR as RESERVED
    |--------------------------------------------------------------------------
    */
    foreach ($selected_qrs as $qr) {

        $stmt = $con->prepare("
            UPDATE stock_inward_qr
            SET 
                stock_status = 'RESERVED'
            WHERE id = ?
        ");

        $stmt->bind_param("i", $qr['id']);

        if (!$stmt->execute()) {
            throw new Exception("Failed to reserve QR: " . $stmt->error);
        }

        $stmt->close();
    }

    mysqli_commit($con);

    header("Location: ../alteration");
    exit;

} catch (Exception $e) {

    mysqli_rollback($con);
    die("Error: " . $e->getMessage());
} 