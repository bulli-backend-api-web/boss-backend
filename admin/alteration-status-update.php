<?php

include("config/database.php");
include("config/auth_check.php");

$id = isset($_GET['id']) ? my_simple_crypt($_GET['id'], 'decrypt_1') : 0;
$new_status = isset($_GET['status']) ? trim($_GET['status']) : '';

if ($id <= 0 || $new_status == '') {
    die("Invalid request");
}

$allowed_status = [
    'SENT_FOR_ALTERATION',
    'READY_FOR_RECEIVE',
    'RECEIVED',
    'QC_APPROVED',
    'COMPLETED',
    'REJECTED'
];

if (!in_array($new_status, $allowed_status)) {
    die("Invalid status");
}

/* Get Alteration Request */
$stmt = $con->prepare("
    SELECT *
    FROM alteration_requests
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$request) {
    die("Alteration request not found");
}

$current_status = $request['status'];

if ($current_status == 'COMPLETED' || $current_status == 'REJECTED') {
    die("This request is already closed");
}

/* Validate Flow */
$valid_next = [
    'STOCK_RESERVED'       => ['SENT_FOR_ALTERATION', 'REJECTED'],
    'SENT_FOR_ALTERATION'  => ['RECEIVED', 'REJECTED'],
    'READY_FOR_RECEIVE' => ['RECEIVED','REJECTED'],
    'RECEIVED'             => ['QC_APPROVED', 'REJECTED'],
    'QC_APPROVED'          => ['COMPLETED', 'REJECTED']
];

if (!isset($valid_next[$current_status]) || !in_array($new_status, $valid_next[$current_status])) {
    die("Invalid status flow");
}

mysqli_begin_transaction($con);

try {

    $product_id = (int)$request['product_id'];
    $from_size  = $request['old_size'];
    $to_size    = $request['new_size'];
    $qty        = (int)$request['qty'];

    /*
    |--------------------------------------------------------------------------
    | COMPLETED STOCK IMPACT
    |--------------------------------------------------------------------------
    | No new inward batch
    | No new inward QR row
    | Replace old QR with new QR
    | Deduct old inward batch qty
    |--------------------------------------------------------------------------
    */
    if ($new_status == 'COMPLETED') {

        /* Check duplicate completion */
        $stmt = $con->prepare("
            SELECT id
            FROM stock_ledger
            WHERE reference_type = 'ALTERATION_COMPLETE'
            AND reference_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $already_done = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($already_done) {
            throw new Exception("Stock already completed for this alteration");
        }

        $reference_type = "ALTERATION_COMPLETE";

        /*
        |--------------------------------------------------------------------------
        | Get old QR code from alteration request
        |--------------------------------------------------------------------------
        | Change column name if your table uses different column.
        |--------------------------------------------------------------------------
        */
        $old_qr_code = '';

        if (!empty($request['old_qrcode'])) {
            $old_qr_code = $request['old_qrcode'];
        } elseif (!empty($request['old_qr_code'])) {
            $old_qr_code = $request['old_qr_code'];
        } elseif (!empty($request['barcode'])) {
            $old_qr_code = $request['barcode'];
        }

        if ($old_qr_code == '') {
            throw new Exception("Old QR code not found in alteration request");
        }

        /*
        |--------------------------------------------------------------------------
        | Find old QR row from stock_inward_qr
        |--------------------------------------------------------------------------
        */
        $stmt = $con->prepare("
            SELECT 
                id,
                batch_id,
                qr_code,
                product_id,
                size,
                stock_status
            FROM stock_inward_qr
            WHERE qr_code = ?
            AND product_id = ?
            LIMIT 1
            FOR UPDATE
        ");
        $stmt->bind_param("si", $old_qr_code, $product_id);
        $stmt->execute();
        $old_qr = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$old_qr) {
            throw new Exception("Old QR not found in stock inward QR");
        }

        /*
        |--------------------------------------------------------------------------
        | Generate New QR Code
        |--------------------------------------------------------------------------
        */
        $new_qr_code = "ALT-" . $request['alteration_id'] . "-" . date("YmdHis");

        /*
        |--------------------------------------------------------------------------
        | Replace old QR with new QR in SAME stock_inward_qr row
        |--------------------------------------------------------------------------
        */
        $stmt = $con->prepare("
            UPDATE stock_inward_qr
            SET 
                qr_code = ?,
                size = ?,
                stock_status = 'AVAILABLE',
                scan_status = 1,
                scanned_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("ssi", $new_qr_code, $to_size, $old_qr['id']);

        if (!$stmt->execute()) {
            throw new Exception("Failed to replace old QR with new QR");
        }

        $stmt->close();

        /*
        |--------------------------------------------------------------------------
        | Minus stock from old inward batch
        |--------------------------------------------------------------------------
        */
        $stmt = $con->prepare("
            UPDATE stock_inward_batch
            SET 
                qty = qty - ?,
                scanned_qty = scanned_qty - ?
            WHERE id = ?
            AND qty >= ?
        ");
        $stmt->bind_param(
            "iiii",
            $qty,
            $qty,
            $old_qr['batch_id'],
            $qty
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to deduct stock from inward batch");
        }

        if ($stmt->affected_rows == 0) {
            throw new Exception("Insufficient stock in inward batch");
        }

        $stmt->close(); 

        /*
        |--------------------------------------------------------------------------
        | Ledger OUT old size
        |--------------------------------------------------------------------------
        */
        $movement_type = "OUT";

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
            $from_size,
            $old_qr_code,
            $movement_type,
            $qty,
            $reference_type,
            $id
        );
        $stmt->execute();
        $stmt->close();

        /*
        |--------------------------------------------------------------------------
        | Ledger altered QR information
        |--------------------------------------------------------------------------
        */
        $movement_type = "ALTERED";

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
            (?, ?, ?, ?, 1, ?, ?, NOW())
        ");
        $stmt->bind_param(
            "issssi",
            $product_id,
            $to_size,
            $new_qr_code,
            $movement_type,
            $reference_type,
            $id
        );
        $stmt->execute();
        $stmt->close();

        /*
        |--------------------------------------------------------------------------
        | Save new QR in alteration request if column exists
        |--------------------------------------------------------------------------
        | If your table has new_qr_code column, this will work.
        |--------------------------------------------------------------------------
        */
        $stmt = $con->prepare("
            UPDATE alteration_requests
            SET new_qrcode = ?
            WHERE id = ?
        ");
        if ($stmt) {
            $stmt->bind_param("si", $new_qr_code, $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REJECTED STOCK IMPACT
    |--------------------------------------------------------------------------
    */
    if ($new_status == 'REJECTED') {

        $stmt = $con->prepare("
            SELECT id
            FROM stock_ledger
            WHERE reference_type = 'ALTERATION_REJECT'
            AND reference_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $already_rejected = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$already_rejected) {

            $movement_type = "UNRESERVE";
            $reference_type = "ALTERATION_REJECT";

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
                $id
            );
            $stmt->execute();
            $stmt->close();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Alteration Status
    |--------------------------------------------------------------------------
    */
    $stmt = $con->prepare("
        UPDATE alteration_requests
        SET status = ?
        WHERE id = ?
    ");
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
    $stmt->close();

    mysqli_commit($con);

    header("Location: alteration-view.php?id=" . my_simple_crypt($id, 'encrypt_1'));
    exit;

} catch (Exception $e) {

    mysqli_rollback($con);
    die("Error: " . $e->getMessage());
}