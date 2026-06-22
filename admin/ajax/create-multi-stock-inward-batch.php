<?php
include("../config/database.php");
include("../config/auth_check.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$items       = $_POST['items'] ?? [];
$outfit_type = trim($_POST['outfit_type'] ?? '');
$challan_no  = trim($_POST['challan_no'] ?? '');
$inward_date = trim($_POST['inward_date'] ?? date('Y-m-d'));
$remarks     = trim($_POST['remarks'] ?? '');

if (empty($items)) {
    die("Please add at least one product.");
}

if ($challan_no == '') {
    $challan_no = "CH-" . date("YmdHis");
}

$today  = date("Ymd");
$prefix = "IN-" . $today . "-";

$stmt = $con->prepare("
    SELECT batch_no
    FROM stock_inward_batch
    WHERE batch_no LIKE CONCAT(?, '%')
    ORDER BY id DESC
    LIMIT 1
");
$stmt->bind_param("s", $prefix);
$stmt->execute();
$last = $stmt->get_result()->fetch_assoc();
$stmt->close();

$new_seq = $last ? ((int)substr($last['batch_no'], -4) + 1) : 1;
$batch_no = $prefix . str_pad($new_seq, 4, "0", STR_PAD_LEFT);

$total_qty = 0;

foreach ($items as $item) {
    $total_qty += (int)$item['qty'];
}

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
            challan_status,
            inward_date,
            remarks,
            created_at
        )
        VALUES
        (?, ?, 0, ?, 'MULTI', ?, 0, 0, 'CREATED', 'CREATED', ?, ?, NOW())
    ");

    $stmt->bind_param(
        "sssiss",
        $batch_no,
        $challan_no,
        $outfit_type,
        $total_qty,
        $inward_date,
        $remarks
    );

    $stmt->execute();
    $batch_id = $stmt->insert_id;
    $stmt->close();

    $unit_counter = 1;

    foreach ($items as $item) {

        $product_id = (int)$item['product_id'];
        $size       = trim($item['size'] ?? '');
        $qty        = (int)$item['qty'];

        if ($product_id <= 0 || $size == '' || $qty <= 0) {
            throw new Exception("Invalid item data");
        }

        for ($i = 1; $i <= $qty; $i++) {

            $unit_no = str_pad($unit_counter, 3, "0", STR_PAD_LEFT);

            $barcode_no = "BK-" . $batch_no . "-" . $unit_no;

            $stmt = $con->prepare("
                INSERT INTO stock_inward_qr
                (
                    batch_id,
                    qr_code,
                    product_id,
                    size,
                    print_status,
                    scan_status,
                    stock_status,
                    created_at
                )
                VALUES
                (?, ?, ?, ?, 0, 0, 'PENDING_SCAN', NOW())
            ");

            $stmt->bind_param(
                "isis",
                $batch_id,
                $barcode_no,
                $product_id,
                $size
            );

            $stmt->execute();
            $stmt->close();

            $unit_counter++;
        }
    }

    mysqli_commit($con);

    header("Location: ../challan-list");
    exit;

} catch (Exception $e) {

    mysqli_rollback($con);
    die("Error: " . $e->getMessage());
}