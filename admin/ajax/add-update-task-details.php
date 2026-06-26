<?php

include("../config/database.php");
include("../config/auth_check.php");

error_reporting(E_ALL);
ini_set('display_errors',1);

$action = !empty($_POST['action']) ? $_POST['action'] : 'add-design-details';
if ($action == 'add-design-details') {
    $task_title = $_POST['task_title'];
    $task_type = $_POST['task_type'];
    $department = $_POST['department'];
    $recurrence = $_POST['recurrence'];
    $proof_required = $_POST['proof_required'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $due_date = isset($_POST['due_date']) ? $_POST['due_date'] : "";
    $assign_to = '';

    if (isset($_POST['assign_to']) && !empty($_POST['assign_to'])) {
        $assign_to = implode(',', $_POST['assign_to']);
    }

    $task_no = generate_task_no();
    $stmt = $con->prepare("INSERT INTO task_master(task_no,title,description,assigned_to,assigned_by,priority,task_type,department_id,recurrence_type,deadline_time)VALUES(?,?,?,?,?,?,?,?,?,?)");

    $stmt->bind_param("ssssississ",$task_no,$task_title,$description,$assign_to,$uid,$priority,$task_type,$department,$recurrence,$due_date);

    if($stmt->execute()){
        $task_id = $con->insert_id;
        if (!empty($_POST['assign_to'])) {
            foreach ($_POST['assign_to'] as $staff_id) {
                $staff_id = (int) $staff_id;
                mysqli_query($con, "INSERT INTO task_staff_mapping(task_id,staff_id) VALUES('$task_id','$staff_id')");
            }
        }
        echo json_encode(["status" => "success", "message" => "Task Create successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while create task."]);
    }
}else if($action == 'rework-task'){
    $task_id = $_POST['task_id'];
    $remarks = $_POST['remarks'];
    $status = $_POST['status'];
    
    $updstmt = $con->prepare("UPDATE task_master set status = ?, remarks = ? where id = ?");
    $updstmt->bind_param("ssi",$status,$remarks,$task_id);
    if($updstmt->execute()){
        echo json_encode(["status" => "success", "message" => "Task Status Updated."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while updating task"]);
    }
    
}else if($action == 'complete-task'){
    $task_id = $_POST['task_id'];
    $remarks = $_POST['remarks'];
    $status = $_POST['status'];
    
    $updstmt = $con->prepare("UPDATE task_master set status = ?, remarks = ? where id = ?");
    $updstmt->bind_param("ssi",$status,$remarks,$task_id);
    if($updstmt->execute()){
        echo json_encode(["status" => "success", "message" => "Task Status Updated."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while updating task"]);
    }
    
}