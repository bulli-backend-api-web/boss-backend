<?php

include("../config/database.php");
include("../config/auth_check.php");

error_reporting(E_ALL);
ini_set('display_errors',1);

$action = isset($_POST['action']) ? $_POST['action'] : "";
if ($action == 'add-rule') {
    $rule_name = isset($_POST['rule_name']) ? $_POST['rule_name'] : "";
    $rule_type = isset($_POST['rule_type']) ? $_POST['rule_type'] : "";
    $scope_type = isset($_POST['scope_type']) ? $_POST['scope_type'] : "";
    $department_id = isset($_POST['department_id']) ? $_POST['department_id'] : "";
    $staff_id = isset($_POST['staff_id']) ? $_POST['staff_id'] : "";
    $metric_code = isset($_POST['metric_code']) ? $_POST['metric_code'] : "";
    $operator = isset($_POST['operator']) ? $_POST['operator'] : "";
    $threshold_value = isset($_POST['threshold_value']) ? $_POST['threshold_value'] : "";
    $severity = isset($_POST['severity']) ? $_POST['severity'] : "";
    $notify_to = isset($_POST['notify_to']) ? $_POST['notify_to'] : "";
    $escalation = isset($_POST['escalation']) ? $_POST['escalation'] : "";
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : "";
    $unit = isset($_POST['unit']) ? $_POST['unit'] : "";

    $insert_query = "INSERT INTO rule_master(rule_name,rule_type,scope_type,department_id,staff_id,metric_code,operator,threshold_value,severity,notify_to,escalation_after,escalation_days,remarks,unit) values "
            . "(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param("sssiissdssiiss", $rule_name, $rule_type, $scope_type, $department_id, $staff_id, $metric_code, $operator, $threshold_value, $severity, $notify_to, $escalation, $escalation, $remarks, $unit);
    if ($stmt->execute()) {
        echo json_encode(["status" => "1", "message" => "Rule Saved Successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "something went wrong while create rule"]);
    }
}else if($action == 'update-rule'){
    $rule_id = isset($_POST['hidden_id']) ? $_POST['hidden_id'] : "";
    $rule_name = isset($_POST['rule_name']) ? $_POST['rule_name'] : "";
    $rule_type = isset($_POST['rule_type']) ? $_POST['rule_type'] : "";
    $scope_type = isset($_POST['scope_type']) ? $_POST['scope_type'] : "";
    $department_id = isset($_POST['department_id']) ? $_POST['department_id'] : "";
    $staff_id = isset($_POST['staff_id']) ? $_POST['staff_id'] : "";
    $metric_code = isset($_POST['metric_code']) ? $_POST['metric_code'] : "";
    $operator = isset($_POST['operator']) ? $_POST['operator'] : "";
    $threshold_value = isset($_POST['threshold_value']) ? $_POST['threshold_value'] : "";
    $severity = isset($_POST['severity']) ? $_POST['severity'] : "";
    $notify_to = isset($_POST['notify_to']) ? $_POST['notify_to'] : "";
    $escalation = isset($_POST['escalation']) ? $_POST['escalation'] : "";
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : "";
    $unit = isset($_POST['unit']) ? $_POST['unit'] : "";
    
    $update_qry = "UPDATE rule_master SET rule_name = ?, rule_type = ?, scope_type = ?, department_id = ?, staff_id= ?, metric_code = ?, operator = ?, threshold_value = ?, unit = ?,severity = ?,notify_to = ?, escalation_after = ?, remarks = ?  where id = ?";
    $upt = $con->prepare($update_qry);
    $upt->bind_param('sssisssdsssisi',$rule_name,$rule_type,$scope_type,$department_id,$staff_id,$metric_code,$operator,$threshold_value,$unit,$severity,$notify_to,$escalation,$remarks,$rule_id);
    if ($upt->execute()) {
        echo json_encode(["status" => "1", "message" => "Rule Updated Successfully"]);
    }else{
        echo json_encode(["status" => "error", "message" => "something went wrong while update rule"]);
    }
    
}else{
    echo json_encode(["status" => "0", "message" => "Invalid request"]);
}
