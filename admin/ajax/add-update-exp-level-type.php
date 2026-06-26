<?php
include("../config/database.php");
include("../config/auth_check.php");

$action = !empty($_POST['action']) ? $_POST['action'] : '';
if($action == 'add-exp-level-type'){
    $name = $_POST['name'];
    $checkSql = "SELECT COUNT(*) as cnt FROM staff_exp_level WHERE name = ?";
    $stmt = mysqli_prepare($con, $checkSql);
    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['cnt'] > 0) {
        echo json_encode(["status" => "error", "message" => "Employment Type already exists in system"]);
        exit;
    }
    
    $insert_query = "INSERT INTO staff_exp_level(name) VALUES (?)";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param('s',$name);
    if($stmt->execute()){
        $last_id = $con->insert_id;
        echo json_encode(["status" => "success", "message" => "Record Added Successfully.",'id'=>$last_id]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while added data"]);
    }
}else{
    echo json_encode(["status" => "error", "message" => "Invalid Request"]);
}
