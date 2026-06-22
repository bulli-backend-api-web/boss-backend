<?php

include("../config/database.php");
include("../config/auth_check.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$batch_id = (int) $_POST['batch_id'];
$user_id = (int) $_POST['user_id'];
$remarks = mysqli_real_escape_string($con, $_POST['remarks']);

mysqli_query($con, "UPDATE stock_inward_batch SET assigned_user_id = '$user_id', assign_date = NOW(),status = 'LABEL_ATTACHMENT_PENDING' WHERE id = '$batch_id'");

mysqli_query($con, "INSERT INTO stock_inward_assignments(batch_id,user_id,assigned_by,remarks,assigned_at)VALUES('$batch_id','$user_id','" . $uid . "','$remarks',NOW())");

$task_no = "TASK-" . date("YmdHis");
$title = "Attach Barcode Labels";
$description = "Attach barcode labels for Batch No: " . $batch_id;

$stmt = $con->prepare("INSERT INTO task_master(task_no,module,reference_id,title,description,assigned_to,assigned_by,priority,status,created_at)
                VALUES(?,'STOCK_INWARD',?,?,?,?,?,'HIGH','PENDING',NOW())");

$stmt->bind_param("sissii",$task_no,$batch_id,$title,$description,$user_id,$uid);

$stmt->execute();
$stmt->close();

echo json_encode([
    'status' => true
]);
