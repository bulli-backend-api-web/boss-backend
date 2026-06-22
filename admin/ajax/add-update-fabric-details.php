<?php
include '../config/database.php';
include '../config/auth_check.php';
header('Content-Type: application/json');

/**
 * Bind an array of params to a prepared statement without manually
 * counting type characters, then execute it.
 */
function bindAndExecute(mysqli_stmt $stmt, array $params): bool {
    $types = '';
    foreach ($params as $p) {
        $types .= is_int($p) ? 'i' : (is_float($p) ? 'd' : 's');
    }
    $refs = [$types];
    foreach ($params as $key => $value) {
        $refs[] = &$params[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $refs);
    return $stmt->execute();
}

$id           = (int)($_POST['id'] ?? 0);
$fabric_name  = trim($_POST['fabric_name'] ?? '');
$fabric_code  = trim($_POST['fabric_code'] ?? '');
$fabric_type  = trim($_POST['fabric_type'] ?? '');
$color        = trim($_POST['color'] ?? '');
$composition  = trim($_POST['composition'] ?? '');
$gsm          = trim($_POST['gsm'] ?? '');
$width        = trim($_POST['width'] ?? '');
$unit         = trim($_POST['unit'] ?? 'Meter');
$default_rate = trim($_POST['default_rate'] ?? '');
$supplier_id  = (int)($_POST['supplier_id'] ?? 0);
$stock_qty    = trim($_POST['stock_qty'] ?? '');
$status       = isset($_POST['status']) ? (int)$_POST['status'] : 1;
$remarks      = trim($_POST['remarks'] ?? '');

/* Validation */
$errors = [];

if ($fabric_name === '') {
    $errors[] = 'Fabric name is required.';
} elseif (mb_strlen($fabric_name) > 150) {
    $errors[] = 'Fabric name must be under 150 characters.';
}

if ($fabric_code === '') {
    $errors[] = 'Fabric code is required.';
}

if ($fabric_type === '') {
    $errors[] = 'Fabric type is required.';
}

$allowed_types = ['Woven', 'Knitted', 'Denim', 'Satin', 'Velvet', 'Linen', 'Other'];
if ($fabric_type !== '' && !in_array($fabric_type, $allowed_types, true)) {
    $errors[] = 'Invalid fabric type.';
}

$allowed_units = ['Meter', 'Yard', 'Kg', 'Piece'];
if (!in_array($unit, $allowed_units, true)) {
    $errors[] = 'Invalid unit.';
}

foreach (['GSM' => $gsm, 'Width' => $width, 'Default rate' => $default_rate, 'Stock quantity' => $stock_qty] as $label => $value) {
    if ($value !== '' && !is_numeric($value)) {
        $errors[] = "$label must be a number.";
    } elseif ($value !== '' && (float)$value < 0) {
        $errors[] = "$label cannot be negative.";
    }
}

/* Duplicate fabric code check */
if ($fabric_code !== '') {
    if ($id) {
        $dup = $con->prepare("SELECT id FROM fabric_master WHERE fabric_code = ? AND id != ?");
        bindAndExecute($dup, [$fabric_code, $id]);
    } else {
        $dup = $con->prepare("SELECT id FROM fabric_master WHERE fabric_code = ?");
        bindAndExecute($dup, [$fabric_code]);
    }
    if ($dup->get_result()->num_rows > 0) {
        $errors[] = 'Fabric code already exists.';
    }
}

/* Swatch image upload (optional) */
$swatch_filename = null;
if (!empty($_FILES['swatch_image']['name'])) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
    $max_size    = 2 * 1024 * 1024; // 2MB
    $ext = strtolower(pathinfo($_FILES['swatch_image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext, true)) {
        $errors[] = 'Swatch image must be jpg, jpeg, png, or webp.';
    } elseif ($_FILES['swatch_image']['size'] > $max_size) {
        $errors[] = 'Swatch image must be under 2MB.';
    } elseif ($_FILES['swatch_image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Swatch image failed to upload.';
    } else {
        $upload_dir = '../uploads/fabrics/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $swatch_filename = 'fabric_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (!move_uploaded_file($_FILES['swatch_image']['tmp_name'], $upload_dir . $swatch_filename)) {
            $errors[] = 'Could not save swatch image.';
            $swatch_filename = null;
        }
    }
}

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
    exit;
}

/* Save (insert or update) */
$columns = [
    'fabric_name'  => $fabric_name,
    'fabric_code'  => $fabric_code,
    'fabric_type'  => $fabric_type,
    'color'        => $color,
    'composition'  => $composition,
    'gsm'          => $gsm,
    'width'        => $width,
    'unit'         => $unit,
    'default_rate' => $default_rate,
    'supplier_id'  => $supplier_id,
    'stock_qty'    => $stock_qty,
    'is_active'       => $status,
    'remarks'      => $remarks,
];

// Only touch swatch_image if a new file was actually uploaded,
// so editing a fabric without re-uploading keeps the old image.
if ($swatch_filename) {
    $columns['swatch_image'] = $swatch_filename;
}

try {
    if ($id) {
        $setParts = [];
        foreach (array_keys($columns) as $col) {
            $setParts[] = "$col = ?";
        }
        $setClause = implode(', ', $setParts);
        $stmt = $con->prepare("UPDATE fabric_master SET $setClause WHERE id = ?");

        $params = array_values($columns);
        $params[] = $id;

        bindAndExecute($stmt, $params);
        $message = 'Fabric updated successfully.';
    } else {
        $cols         = implode(', ', array_keys($columns));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $stmt = $con->prepare("INSERT INTO fabric_master ($cols) VALUES ($placeholders)");

        bindAndExecute($stmt, array_values($columns));
        $message = 'Fabric created successfully.';
    }

    echo json_encode(['status' => 'success', 'message' => $message]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}