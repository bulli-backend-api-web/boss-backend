<?php
include("../config/database.php");
include("../config/auth_check.php");

$action = !empty($_POST['action']) ? $_POST['action'] : '';
if($action == 'add-sample'){
    $design_id = $_POST['design_id'];
    $sample_name = $_POST['sample_name'];
    $category = $_POST['category'];
    $assign_to = $_POST['assign_to'];
    $budget = $_POST['budget'];
    $target_days = $_POST['target_days'];
    $fabric = isset($_POST['fabric']) ? $_POST['fabric'] : "";
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : "";
    $sample_code = generate_sample_no();
    $insert_query = "INSERT INTO sampling(sample_code,design_code,name,category,assign_to,assign_by,budget,target_days,fabric,remark) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param('ssssiidiss',$sample_code,$design_id,$sample_name,$category,$assign_to,$uid,$budget,$target_days,$fabric,$remarks);
    if($stmt->execute()){
        echo json_encode(["status" => "success", "message" => "Dept Name Added Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while added data"]);
    }
}