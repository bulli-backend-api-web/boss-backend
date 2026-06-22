<?php
include("../config/database.php");
include("../config/auth_check.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$batch_id    = (int)($_POST['batch_id'] ?? 0);
$items       = $_POST['items'] ?? [];
$outfit_type = trim($_POST['outfit_type'] ?? '');
$challan_no  = trim($_POST['challan_no'] ?? '');
$inward_date = trim($_POST['inward_date'] ?? date('Y-m-d'));
$remarks     = trim($_POST['remarks'] ?? '');
$assign_to   = (int)($_POST['assign_to'] ?? 0);
$redirect    = trim($_POST['redirect_page'] ?? '../challan-list');

/*
 |--------------------------------------------------------------------------
 | Validate
 |--------------------------------------------------------------------------
 */
if ($batch_id <= 0) {
    die("Invalid batch.");
}

if (empty($items)) {
    die("Please add at least one product.");
}

if ($challan_no == '') {
    $challan_no = "CH-" . date("YmdHis");
}

/*
 |--------------------------------------------------------------------------
 | Fetch Existing Batch
 |--------------------------------------------------------------------------
 */
$stmt = $con->prepare("SELECT * FROM stock_inward_batch WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$batch = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$batch) {
    die("Batch not found.");
}

/*
 |--------------------------------------------------------------------------
 | Fetch Existing QR Rows for this Batch
 | We'll use these to figure out what to add / remove
 |--------------------------------------------------------------------------
 */
$stmt = $con->prepare("
    SELECT id, product_id, size, qr_code
    FROM stock_inward_qr
    WHERE batch_id = ?
    ORDER BY id ASC
");
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$res = $stmt->get_result();

$existing_qr_rows = [];
while ($row = $res->fetch_assoc()) {
    $existing_qr_rows[] = $row;
}
$stmt->close();

/*
 |--------------------------------------------------------------------------
 | Build a map of existing qty per product+size
 | e.g. ["123_M"] => [ids...]
 |--------------------------------------------------------------------------
 */
$existing_map = [];
foreach ($existing_qr_rows as $qr) {
    $key = $qr['product_id'] . '_' . $qr['size'];
    if (!isset($existing_map[$key])) {
        $existing_map[$key] = [];
    }
    $existing_map[$key][] = $qr['id'];
}

/*
 |--------------------------------------------------------------------------
 | Build new requested qty map from POST
 |--------------------------------------------------------------------------
 */
$new_map = [];
foreach ($items as $item) {
    $product_id = (int)($item['product_id'] ?? 0);
    $size       = trim($item['size'] ?? '');
    $qty        = (int)($item['qty'] ?? 0);

    if ($product_id <= 0 || $size == '' || $qty <= 0) {
        continue;
    }

    $key = $product_id . '_' . $size;
    $new_map[$key] = [
        'product_id' => $product_id,
        'size'       => $size,
        'qty'        => $qty,
    ];
}

if (empty($new_map)) {
    die("Please add at least one valid product.");
}

/*
 |--------------------------------------------------------------------------
 | Calculate total qty
 |--------------------------------------------------------------------------
 */
$total_qty = 0;
foreach ($new_map as $entry) {
    $total_qty += $entry['qty'];
}

/*
 |--------------------------------------------------------------------------
 | Get the highest existing unit number suffix so new codes don't clash
 |--------------------------------------------------------------------------
 */
$max_unit = 0;
foreach ($existing_qr_rows as $qr) {
    // QR format: BK-IN-YYYYMMDD-XXXX-NNN
    $parts = explode('-', $qr['qr_code']);
    $last_part = (int)end($parts);
    if ($last_part > $max_unit) {
        $max_unit = $last_part;
    }
}

$unit_counter = $max_unit + 1;

/*
 |--------------------------------------------------------------------------
 | Begin Transaction
 |--------------------------------------------------------------------------
 */
mysqli_begin_transaction($con);

try {

    /*
     |------------------------------------------------------------------
     | 1. Update batch header
     |------------------------------------------------------------------
     */
    $stmt = $con->prepare("
        UPDATE stock_inward_batch
        SET
            challan_no       = ?,
            category         = ?,
            qty              = ?,
            inward_date      = ?,
            remarks          = ?,
            assigned_user_id = ?,
            updated_at       = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param(
        "ssisiii",
        $challan_no,
        $outfit_type,
        $total_qty,
        $inward_date,
        $remarks,
        $assign_to,
        $batch_id
    );
    $stmt->execute();
    $stmt->close();

    /*
     |------------------------------------------------------------------
     | 2. For each new_map entry decide: keep / add / trim
     |------------------------------------------------------------------
     */
    $ids_to_delete = [];

    foreach ($new_map as $key => $entry) {

        $product_id  = $entry['product_id'];
        $size        = $entry['size'];
        $new_qty     = $entry['qty'];
        $existing_ids = $existing_map[$key] ?? [];
        $existing_qty = count($existing_ids);

        if ($new_qty > $existing_qty) {

            /*
             | Need to INSERT extra QR rows
             */
            $add_count = $new_qty - $existing_qty;

            for ($i = 0; $i < $add_count; $i++) {

                $unit_no    = str_pad($unit_counter, 3, "0", STR_PAD_LEFT);
                $barcode_no = "BK-" . $batch['batch_no'] . "-" . $unit_no;

                $stmt = $con->prepare("
                    INSERT INTO stock_inward_qr
                    (batch_id, qr_code, product_id, size, print_status, scan_status, stock_status, created_at)
                    VALUES
                    (?, ?, ?, ?, 0, 0, 'PENDING_SCAN', NOW())
                ");
                $stmt->bind_param("isis", $batch_id, $barcode_no, $product_id, $size);
                $stmt->execute();
                $stmt->close();

                $unit_counter++;
            }

        } elseif ($new_qty < $existing_qty) {

            /*
             | Need to DELETE extra QR rows (only unscanned ones)
             | Remove from the end of the list
             */
            $remove_count  = $existing_qty - $new_qty;
            $ids_to_remove = array_slice($existing_ids, -$remove_count);

            foreach ($ids_to_remove as $del_id) {
                $ids_to_delete[] = $del_id;
            }
        }

        // If equal — nothing to do, keep as is
    }

    /*
     |------------------------------------------------------------------
     | 3. Delete QR rows that were removed from new_map entirely
     |    (product+size combo removed by user)
     |------------------------------------------------------------------
     */
    foreach ($existing_map as $key => $existing_ids) {
        if (!isset($new_map[$key])) {
            // This product+size was removed completely
            foreach ($existing_ids as $del_id) {
                $ids_to_delete[] = $del_id;
            }
        }
    }

    /*
     |------------------------------------------------------------------
     | 4. Perform deletes (only PENDING_SCAN / unprinted rows)
     |    Scanned or printed rows are protected
     |------------------------------------------------------------------
     */
    if (!empty($ids_to_delete)) {

        foreach ($ids_to_delete as $del_id) {

            $stmt = $con->prepare("
                DELETE FROM stock_inward_qr
                WHERE id = ?
                AND print_status = 0
                AND scan_status  = 0
                AND stock_status = 'PENDING_SCAN'
            ");
            $stmt->bind_param("i", $del_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    /*
     |------------------------------------------------------------------
     | 5. Re-calculate actual qty after deletes and update batch
     |------------------------------------------------------------------
     */
    $stmt = $con->prepare("
        SELECT COUNT(id) AS actual_qty
        FROM stock_inward_qr
        WHERE batch_id = ?
    ");
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $actual_qty = $stmt->get_result()->fetch_assoc()['actual_qty'];
    $stmt->close();

    $stmt = $con->prepare("UPDATE stock_inward_batch SET qty = ? WHERE id = ?");
    $stmt->bind_param("ii", $actual_qty, $batch_id);
    $stmt->execute();
    $stmt->close();

    mysqli_commit($con);

    header("Location: " . $redirect);
    exit;

} catch (Exception $e) {

    mysqli_rollback($con);
    die("Error: " . $e->getMessage());
}