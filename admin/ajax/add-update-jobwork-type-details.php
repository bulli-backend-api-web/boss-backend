<?php
include("../config/database.php");
include("../config/auth_check.php");
$action = isset($_POST['action']) ? $_POST['action'] : "";
if($action == 'add-jobwork'){
    $jobwork_name = $_POST['jobwork_name'];
    $amount = $_POST['amount'];
    $status = 1;
    $image_url = '';
    $stmt = $con->prepare("INSERT INTO jobwork_type (name,amount,created_by,status) VALUES (?,?,?,?)");
    $stmt->bind_param("sdii", $jobwork_name,$amount,$uid,$status);
    if($stmt->execute()){
        $details = "Added New Jobwork Type with name : ".$jobwork_name;
        logActivity($uid, "Added New Jobwork Type", $details);
        echo json_encode(["status" => "success", "message" => "Jobwork Added Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Party not added"]);
    }
    $stmt->close();
}else if($action == 'update-jobwork'){
    $id = $_POST['id'];
    $jobwork_name = $_POST['jobwork_name'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];
    $image_url = '';
    
    $stmt = $con->prepare("UPDATE jobwork_type SET name = ?,amount=?,status = ? where id = ?");
    $stmt->bind_param("siii", $jobwork_name,$amount,$status,$id);
    
    if($stmt->execute()){
        echo json_encode(["status" => "success", "message" => "Jobwork Details Updated Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Jobwork Details not updated"]);
    }
}else{
    echo json_encode(["status" => "error", "message" => "Invalid Request"]);
}