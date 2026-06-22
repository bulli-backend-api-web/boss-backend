<?php
include("../config/database.php");
include("../config/auth_check.php");

header('Content-Type: application/json');

$id     = isset($_POST['id']) ? my_simple_crypt($_POST['id'], 'decrypt_1') : null;
$action = $_POST['action'] ?? '';
$note   = isset($_POST['note']) ? trim($_POST['note']) : '';

if (!$id || !ctype_digit((string)$id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid sample ID']);
    exit;
}

$status_map = [
    'approve' => 2,
    'reject'  => 3,
    'rework'  => 4,
];

if (!isset($status_map[$action])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

if ($action === 'rework' && $note === '') {
    echo json_encode(['success' => false, 'message' => 'Note is required for rework']);
    exit;
}

$new_status = $status_map[$action];

$con->begin_transaction();
try {
    $upd = $con->prepare("UPDATE sampling SET status = ? WHERE id = ?");
    $upd->bind_param('ii', $new_status, $id);
    $upd->execute();

    if ($action === 'rework') {
        $user_id = $_SESSION['user_id'] ?? null; // adjust to match how auth_check stores the logged-in user
        $note_stmt = $con->prepare("INSERT INTO sampling_notes (sampling_id, note, created_by) VALUES (?, ?, ?)");
        $note_stmt->bind_param('isi', $id, $note, $user_id);
        $note_stmt->execute();
    }

    $con->commit();
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
} catch (Exception $e) {
    $con->rollback();
    echo json_encode(['success' => false, 'message' => 'Something went wrong']);
}