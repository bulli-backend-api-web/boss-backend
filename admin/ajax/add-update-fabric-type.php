<?php
include("../config/database.php");
include("../config/auth_check.php");

$action = !empty($_POST['action']) ? $_POST['action'] : '';
if($action == 'add-fabric-type'){
    $fabric_type = $_POST['fabric_type'];
    $checkSql = "SELECT COUNT(*) as cnt FROM fabric_type WHERE name = ?";
    $stmt = mysqli_prepare($con, $checkSql);
    mysqli_stmt_bind_param($stmt, "s", $fabric_type);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['cnt'] > 0) {
        echo json_encode(["status" => "error", "message" => "Name already exists in system"]);
        exit;
    }
    
    $insert_query = "INSERT INTO fabric_type(name) VALUES (?)";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param('s',$fabric_type);
    if($stmt->execute()){
        $last_insert_id = $con->insert_id;
        echo json_encode(["status" => "success", "message" => "Type Added Successfully.",'id'=>$last_insert_id,'name'=>$fabric_type]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while added data"]);
    }
}else if($action == 'update-fabric-type'){
    $fabric_type = $_POST['fabric_type'];
    $fabric_id = $_POST['id'];
    
    $update_Sql = "UPDATE fabric_type SET name = ? where id = ?";
    $stmt1 = $con->prepare($update_Sql);
    $stmt1->bind_param('si',$fabric_type,$fabric_id);
    
    if($stmt1->execute()){
        echo json_encode(["status" => "success", "message" => "Type Updated Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while updated channel"]);
    }
}else{
    echo json_encode(["status" => "error", "message" => "Invalid Request"]);
}
