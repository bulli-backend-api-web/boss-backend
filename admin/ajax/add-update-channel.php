<?php
include("../config/database.php");
include("../config/auth_check.php");

$action = !empty($_POST['action']) ? $_POST['action'] : '';
if($action == 'add-channel'){
    $channel_name = $_POST['channel_name'];
    $checkSql = "SELECT COUNT(*) as cnt FROM channel WHERE name = ?";
    $stmt = mysqli_prepare($con, $checkSql);
    mysqli_stmt_bind_param($stmt, "s", $channel_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['cnt'] > 0) {
        echo json_encode(["status" => "error", "message" => "Channel Name already exists in system"]);
        exit;
    }
    
    $insert_query = "INSERT INTO channel(name) VALUES (?)";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param('s',$channel_name);
    if($stmt->execute()){
        echo json_encode(["status" => "success", "message" => "Channel Name Added Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while added channel"]);
    }
}else if($action == 'update-channel'){
    $channel_name = $_POST['channel_name'];
    $channel_id = $_POST['id'];
    
    $update_Sql = "UPDATE channel SET name = ? where id = ?";
    $stmt1 = $con->prepare($update_Sql);
    $stmt1->bind_param('si',$channel_name,$channel_id);
    if($stmt1->execute()){
        echo json_encode(["status" => "success", "message" => "Channel Name Updated Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while updated channel"]);
    }
}else{
    echo json_encode(["status" => "error", "message" => "Invalid Request"]);
}
