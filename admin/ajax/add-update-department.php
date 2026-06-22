<?php
include("../config/database.php");
include("../config/auth_check.php");

$action = !empty($_POST['action']) ? $_POST['action'] : '';
if($action == 'add-dept'){
    $department_name = $_POST['department_name'];
    $checkSql = "SELECT COUNT(*) as cnt FROM departments WHERE department_name = ?";
    $stmt = mysqli_prepare($con, $checkSql);
    mysqli_stmt_bind_param($stmt, "s", $department_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['cnt'] > 0) {
        echo json_encode(["status" => "error", "message" => "department Name already exists in system"]);
        exit;
    }
    
    $insert_query = "INSERT INTO departments(department_name) VALUES (?)";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param('s',$department_name);
    if($stmt->execute()){
        echo json_encode(["status" => "success", "message" => "Dept Name Added Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while added data"]);
    }
}else if($action == 'update-dept'){
    $department_name = $_POST['department_name'];
    $dept_id = $_POST['id'];
    
    $update_Sql = "UPDATE departments SET department_name = ? where id = ?";
    $stmt1 = $con->prepare($update_Sql);
    $stmt1->bind_param('si',$department_name,$dept_id);
    if($stmt1->execute()){
        echo json_encode(["status" => "success", "message" => "Dept Name Updated Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while updated channel"]);
    }
}else{
    echo json_encode(["status" => "error", "message" => "Invalid Request"]);
}
