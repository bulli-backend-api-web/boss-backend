<?php

include("../config/database.php");
include("../config/auth_check.php");


$id = $_POST['id'];
$batch_id = $_POST['batch_id'];

$stmt = $con->prepare("
    UPDATE stock_inward_qr
    SET print_status = 1
    WHERE id = ?
");

$stmt->bind_param("i", $id);

$stmt->execute();


$stmt1 = $con->prepare("
    UPDATE stock_inward_batch
    SET status = 'stock_inward_batch'
    WHERE id = ?
");

$stmt1->bind_param("i", $batch_id);

$stmt1->execute();

echo json_encode([
    'status' => true
]);