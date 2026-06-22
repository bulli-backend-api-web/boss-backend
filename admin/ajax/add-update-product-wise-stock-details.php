<?php

include("../config/database.php");
include("../config/auth_check.php");

$action = !empty($_POST['action']) ? $_POST['action'] : "";

if ($action == 'add-product-wise-stock') {

    $product_id = !empty($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $outfit_type = trim($_POST['outfit_type'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $qty = !empty($_POST['stock']) ? (int) $_POST['stock'] : 0;
    $challan_no = trim($_POST['challan_no'] ?? '');
    $inward_date = trim($_POST['inward_date'] ?? date('Y-m-d'));
    $remarks = trim($_POST['remarks'] ?? '');
    $redirect_page = $_POST['redirect_page'];
    if ($product_id <= 0) {
        die("Product is required");
    }

//    if ($size == '') {
//        die("Size is required");
//    }

    if ($qty <= 0) {
        die("Quantity is required");
    }

    if ($challan_no == '') {
        $challan_no = "CH-" . date("YmdHis");
    }

    $batch_no = "INW-" . date("YmdHis");

    mysqli_begin_transaction($con);

    try {

        $stmt = $con->prepare("
        INSERT INTO stock_inward_batch
        (
            batch_no,
            challan_no,
            product_id,
            category,
            size,
            qty,
            printed_qty,
            scanned_qty,
            status,
            inward_date,
            remarks,
            created_at
        )
        VALUES
        (?, ?, ?, ?, ?, ?, 0, 0, 'CREATED', ?, ?, NOW())
    ");

        $stmt->bind_param(
                "ssississ",
                $batch_no,
                $challan_no,
                $product_id,
                $outfit_type,
                $size,
                $qty,
                $inward_date,
                $remarks
        );

        $stmt->execute();

        $batch_id = $stmt->insert_id;

        for ($i = 1; $i <= $qty; $i++) {

            $qr_code = $batch_no . "-" . str_pad($i, 4, "0", STR_PAD_LEFT);

            $qr_stmt = $con->prepare("
            INSERT INTO stock_inward_qr
            (
                batch_id,
                qr_code,
                product_id,
                size,
                print_status,
                scan_status
            )
            VALUES
            (?, ?, ?, ?, 0, 0)
        ");

            $qr_stmt->bind_param(
                    "isis",
                    $batch_id,
                    $qr_code,
                    $product_id,
                    $size
            );

            $qr_stmt->execute();
        }

        mysqli_commit($con);
        echo json_encode(["status" => "success", "message" => "Alteration success", "redirect_page" => $redirect_page,"batch_id"=>$batch_id]);

        //header("Location: ../stock-inward-print.php?batch_id=" . $batch_id);
        exit;
    } catch (Exception $e) {

        mysqli_rollback($con);
        die("Error: " . $e->getMessage());
    }
} else if ($action == 'alteration-request') {
    echo '<pre>';
    print_R($_POST);
    die;
    $barcode = $_POST['barcode'];
    $product_id = $_POST['product_id'];
    $to_size = $_POST['to_size'];
    $reason_notes = $_POST['reason_notes'];
    $alt_no = get_alteration_no();
    $redirect_page = $_POST['redirect_page'];
    $inward_id = $unit_id = $old_size = '';
    $sql = "SELECT id,size,inward_no,sku FROM product_wise_stock where barcode = '$barcode'";
    $qry_result = $con->query($sql);
    if ($qry_result && $qry_result->num_rows > 0) {
        $qry_row = $qry_result->fetch_assoc();
        $inward_id = $qry_row['id'];
        $unit_id = $qry_row['inward_no'];
        $old_size = $qry_row['size'];
        $sku = $qry_row['sku'];
    }

    $new_barcode = $sku . "-" . $new_size . "-" . $unit_id;
    $updated_qty = 1;

    $update_sql = "UPDATE product_wise_stock SET available_stock = available_stock - $updated_qty, barcode = '$new_barcode' where id = $inward_id";
    $update_stmt = $con->query($update_sql);

    $insert_query = "INSERT INTO alteration_requests(inward_id,alteration_id,unit_id,product_id,old_size,new_size,new_qrcode,old_qrcode,created_by) VALUES (?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param('ississssi', $inward_id, $alt_no, $unit_id, $product_id, $old_size, $new_size, $new_barcode, $barcode, $uid);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Alteration success", "redirect_page" => $redirect_page]);
    } else {
        echo json_encode(["status" => "error", "message" => "error while alteration request"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}