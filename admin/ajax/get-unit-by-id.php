<?php
include("../config/database.php");
header('Content-Type: application/json');

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Invalid request method.');
}

$unit_id = isset($_POST['unit_id']) ? trim($_POST['unit_id']) : '';

if (empty($unit_id)) {
    errorResponse('Unit ID is required.');
}

$unit_id = $con->real_escape_string($unit_id);

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
    WHERE u.barcode = '$unit_id'
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
