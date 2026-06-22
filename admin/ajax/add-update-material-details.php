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

$id            = (int)($_POST['id'] ?? 0);
$material_name = trim($_POST['material_name'] ?? '');
$unit          = trim($_POST['unit'] ?? 'Piece');
$status        = 1;

/* Validation */
$errors = [];

if ($material_name === '') {
    $errors[] = 'Material name is required.';
}



if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
    exit;
}

/* Save (insert or update) */
$columns = [
    'material_name' => $material_name,
    'unit'          => $unit,
    'status'        => $status,
];

try {
    if ($id) {
        $setParts = [];
        foreach (array_keys($columns) as $col) {
            $setParts[] = "$col = ?";
        }
        $setClause = implode(', ', $setParts);
        $stmt = $con->prepare("UPDATE material_master SET $setClause WHERE id = ?");

        $params = array_values($columns);
        $params[] = $id;

        bindAndExecute($stmt, $params);
        $message = 'Material updated successfully.';
    } else {
        $cols         = implode(', ', array_keys($columns));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $stmt = $con->prepare("INSERT INTO material_master ($cols) VALUES ($placeholders)");

        bindAndExecute($stmt, array_values($columns));
        $message = 'Material created successfully.';
    }

    echo json_encode(['status' => 'success', 'message' => $message]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}