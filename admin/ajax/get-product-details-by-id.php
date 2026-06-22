<?php

include("../config/database.php");

$product_id = $_POST['product_id'];

$action = !empty($_POST['action']) ? $_POST['action'] : "";
$output = '';
function sendJson($data) {
    echo json_encode($data);
    exit;
}

function errorResponse($message) {
    sendJson([
        'found'   => false,
        'message' => $message
    ]);
}
if ($action == 'fetch_variants') {
    if ($product_id) {
        $product_sql = "SELECT size from product_variants where product_id = {$product_id}";
        $product_res = $con->query($product_sql);
        if ($product_res && $product_res->num_rows > 0) {
             $i = 1;
            while ($product_row = $product_res->fetch_assoc()) {
                $size = $product_row['size'];
                ?>
                <input type="radio"
                   class="btn-check"
                   name="size"
                   id="size_<?php echo $i; ?>"
                   value="<?php echo $size; ?>">

                <label class="btn btn-outline btn-outline-dashed btn-active-light-primary"
                       for="size_<?php echo $i; ?>">
                    <?php echo $size; ?>
                </label>
                 <?php

            $i++;

            }
        }
    }
}else if($action == 'fetch_sku'){
    $size = $_POST['size'];
    if ($product_id) {
        $product_sql = "SELECT sku from product_variants where product_id = {$product_id} and size='$size'";
        $product_res = $con->query($product_sql);
        if ($product_res && $product_res->num_rows > 0) {
            $product_row = $product_res->fetch_assoc();
                $sku= $product_row['sku'];
                echo $sku;die;
        }
    }
}else if($action == 'fetch_for_alteration'){
    $sql = "
        SELECT
            u.inward_no as unit_id,
            u.size as current_size,
            u.barcode as qr_code,
            u.product_id,
            p.sku as sku,
            p.name as product_name
        FROM product_wise_stock u
        INNER JOIN product p ON p.id = u.product_id
        WHERE u.product_id = '$product_id'
        LIMIT 1";
    $result = $con->query($sql);
        if (!$result) {
        errorResponse('Database error: ' . $con->error);
    }

    if ($result->num_rows === 0) {
        errorResponse('No unit found with ID: ' . htmlspecialchars($_POST['unit_id']));
    }

    $row = $result->fetch_assoc();



    // 2. Unit must be AVAILABLE (not already ALTERED or SOLD)
    $allowed_statuses = ['AVAILABLE'];
    //if (!in_array(strtoupper($row['unit_status']), $allowed_statuses)) {
    //    errorResponse('This unit cannot be altered. Current status: ' . $row['unit_status']);
    //}

    // ── Parse available sizes ─────────────────────────────────────────────────────
    // Stored as JSON string or comma-separated — handle both formats gracefully

    $available_sizes = [];
    if (!empty($row['available_sizes'])) {
        $decoded = json_decode($row['available_sizes'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $available_sizes = $decoded;
        } else {
            // fallback: comma-separated
            $available_sizes = array_map('trim', explode(',', $row['available_sizes']));
        }
    }

    // ── Build valid new sizes (±1 adjacency rule) ─────────────────────────────────

    $size_order = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Unstitched'];
    $current    = $row['current_size'];
    $valid_new_sizes = [];

    if ($current === 'Unstitched') {
        // Unstitched can go to any size in available_sizes
        $valid_new_sizes = array_values(array_filter($available_sizes, function($s) use ($current) {
            return $s !== $current;
        }));
    } else {
        $current_index = array_search($current, $size_order);
        if ($current_index !== false) {
            foreach ($size_order as $idx => $size) {
                if (abs($idx - $current_index) === 1 && in_array($size, $available_sizes)) {
                    $valid_new_sizes[] = $size;
                }
            }
            // Also allow Unstitched → current (reverse) if Unstitched is available
            // (per the rule: Unstitched → any size)
        }
    }

    // ── Success response ──────────────────────────────────────────────────────────

    sendJson([
        'found'            => true,
        'unit_id'          => $row['unit_id'],
        'product_id'       => $row['product_id'],
        'sku'              => $row['sku'],
        'name'             => $row['product_name'],
        'current_size'     => $row['current_size'],
        'qr_code'          => $row['qr_code'],
        //'unit_status'      => $row['unit_status'],
        'available_sizes'  => $available_sizes,
        'valid_new_sizes'  => $valid_new_sizes,   // pre-computed ±1 sizes for the frontend
        'message'          => 'Unit found successfully.'
    ]);
}
