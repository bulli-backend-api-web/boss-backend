<?php
include("../config/database.php");
include("../config/auth_check.php");

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id     = isset($_POST['id']) ? my_simple_crypt($_POST['id'], 'decrypt_1') : null;
$action = isset($_POST['action'])  ? $_POST['action'] : '';
$note   = isset($_POST['note']) ? trim($_POST['note']) : '';

if (!$id || !ctype_digit((string)$id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Design ID']);
    exit;
}

$status_map = [
    'approve' => 1,
    'reject'  => 2,
    'rework'  => 3,
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
    $upd = $con->prepare("UPDATE design SET status = ? WHERE id = ?");
    $upd->bind_param('ii', $new_status, $id);
    $upd->execute();

    if ($action === 'rework') {
        $user_id = $uid;
        $note_stmt = $con->prepare("INSERT INTO design_notes (design_id, note, created_by) VALUES (?, ?, ?)");
        $note_stmt->bind_param('isi', $id, $note, $user_id);
        $note_stmt->execute();
    }
    
    if($new_status == 1){
        /* Fetch existing data */
        $target_days = 3;
        $checkSql = "SELECT design_code,design_name,style,budget FROM design WHERE id = ?";
        $stmt = mysqli_prepare($con, $checkSql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $design_code = $row['design_code'];
        $design_name = $row['design_name'];
        $style = $row['style'];
        $budget = $row['budget'];
        $sample_no = generate_sample_no();
        $sample_insert_query = "INSERT INTO sampling(sample_code,design_id,design_code,name,category,assign_to,assign_by,budget,target_days) values (?,?,?,?,?,?,?,?,?)";
        $stmt1 = $con->prepare($sample_insert_query);
        $stmt1->bind_param("sissiiidi",$sample_no,$id,$design_code,$design_name,$style,$assign_to,$uid,$budget,$target_days);
        $stmt1->execute();
    }

    $con->commit();
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
} catch (Exception $e) {
    $con->rollback();
    echo json_encode(['success' => false, 'message' => 'Something went wrong']);
}