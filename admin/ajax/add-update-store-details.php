<?php
include("../config/database.php");
include("../config/auth_check.php");

$action = !empty($_POST['action']) ? $_POST['action'] : "";
if($action == 'add-store-details'){
    $redirect_page = $_POST['redirect_page'];
    $store_name = $_POST['store_name'];
    $store_code = $_POST['store_code'];
    $ownership_model = $_POST['ownership_model'];
    $store_type = $_POST['store_type'];
    $city = $_POST['city'];
    $state_id = $_POST['state_id'];
    $address = $_POST['address'];
    $operation_head = $_POST['operation_head'];
    $operation_head_number = $_POST['operation_head_number'];
    $store_manager_name = $_POST['store_manager_name'];
    $store_manager_contact = $_POST['store_manager_contact'];
    $sale_person = $_POST['sale_person'];
    $applicable_rate = $_POST['applicable_rate'];
    $billing_cycle = $_POST['billing_cycle'];
    $opening_date = $_POST['opening_date'];
    $agreement_expire = $_POST['agreement_expire'];
    $remarks = $_POST['remarks'];
    $insert_query = "INSERT INTO store (
            store_name,
            store_code,
            ownership_model,
            store_type,
            city,
            state,
            address,
            ops_head_name,
            ops_contact_number,
            applicable_rate,
            billing_cycle,
            opening_date,
            agreement_expire,
            remarks
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $con->prepare($insert_query);
    $stmt->bind_param('sssssissssssss',$store_name,$store_code,$ownership_model,$store_type,$city,$state_id,$address,$operation_head,$operation_head_number,$applicable_rate,$billing_cycle,$opening_date,$agreement_expire,$remarks);
    if($stmt->execute()){
        $last_id = $stmt->insert_id;
        
        if($sale_person){
            for ($ckoutt = 0; $ckoutt < count($_POST['sale_person']); $ckoutt++) {
                $sales_person_name = $_POST['sale_person'][$ckoutt];
                $sales_person_mobile = $_POST['sale_person'][$ckoutt];
                $sales_person_sql = "INSERT INTO store_sales_person(store_id,name,mobile_number) VALUES (?,?,?)";
                $order_stmt = $con->prepare($sales_person_sql);
                $order_stmt->bind_param('iss',$last_id,$sales_person_name,$sales_person_mobile);
            }
        }
        if($order_stmt->execute()){
            echo json_encode(["status" => "success", "message" => "Store Created Successfully."]);
        }else{
            echo json_encode(["status" => "error", "message" => "Error while creating Store"]);
        }
    }else{
         echo json_encode(["status" => "error", "message" => "Error while creating Store"]);
    }
}else{
     echo json_encode(["status" => "error", "message" => "Invalid Request"]);
}