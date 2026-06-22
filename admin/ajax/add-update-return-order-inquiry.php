<?php

include("../config/database.php");
include("../config/auth_check.php");

$reason_list = getResonList();

$action = !empty($_POST['action']) ? $_POST['action'] : "";
if ($action == 'update-return-order-inquiry') {
    $inquiry_id = !empty($_POST['inquiry_id']) ? $_POST['inquiry_id'] : "";
    $request_from = !empty($_POST['request_from']) ? $_POST['request_from'] : "";
    $redirect_page = !empty($_POST['redirect_page']) ? $_POST['redirect_page'] : "";
    $name = !empty($_POST['name']) ? $_POST['name'] : "";
    $mobile = !empty($_POST['mobile']) ? $_POST['mobile'] : "";
    $order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : "";
    $ticket_id = !empty($_POST['ticket_id']) ? $_POST['ticket_id'] : "";
    $total_qty = !empty($_POST['total_qty']) ? $_POST['total_qty'] : "";
    $description = !empty($_POST['description']) ? $_POST['description'] : "";
    $dto_type = !empty($_POST['dto_type']) ? $_POST['dto_type'] : "";
    $return_status_id = !empty($_POST['return_status_id']) ? $_POST['return_status_id'] : "";
    $dto_way = !empty($_POST['dto_way']) ? $_POST['dto_way'] : "";
    $courier_name = !empty($_POST['courier_name']) ? $_POST['courier_name'] : "";
    $awb_number = !empty($_POST['awb_number']) ? $_POST['awb_number'] : "";
    $remark = !empty($_POST['remark']) ? $_POST['remark'] : "";
    $payment_method = !empty($_POST['payment_method']) ? $_POST['payment_method'] : "";
    $upi_id = !empty($_POST['upi_id']) ? $_POST['upi_id'] : "";
    $account_name = !empty($_POST['account_name']) ? $_POST['account_name'] : "";
    $account_number = !empty($_POST['account_number']) ? $_POST['account_number'] : "";
    $ifsc_code = !empty($_POST['ifsc_code']) ? $_POST['ifsc_code'] : "";
    $bank_name = !empty($_POST['bank_name']) ? $_POST['bank_name'] : "";
    $total_amount = !empty($_POST['total_amount']) ? $_POST['total_amount'] : 0;
    $deduction = !empty($_POST['deduction']) ? $_POST['deduction'] : 0;
    $deduction1 = !empty($_POST['deduction1']) ? $_POST['deduction1'] : 0;
    $deduction2 = !empty($_POST['deduction2']) ? $_POST['deduction2'] : 0;
    $total_refund = !empty($_POST['total_refund']) ? $_POST['total_refund'] : 0;
    $reject_reason = !empty($_POST['$reject_reason']) ? $_POST['$reject_reason'] : "";
    $customer_inquiry_status = !empty($_POST['customer_inquiry_status']) ? $_POST['customer_inquiry_status'] : "";
    $reason = !empty($reason_list[$return_status_id]) ? $reason_list[$return_status_id] : "";
    $stmt = $con->prepare("UPDATE return_order_inquiry SET customer_inquiry_status = ?,reject_reason = ? where id = ?");
    $stmt->bind_param("isi", $customer_inquiry_status, $reject_reason, $inquiry_id);
    if ($stmt->execute()) {
        if ($customer_inquiry_status == 2) {
            $existing_data = "SELECT image_proof,unboxing_video from return_order_inquiry where id = $inquiry_id";
            $existing_res = $con->query($existing_data);
            $existing_row = $existing_res->fetch_assoc();
            $image_proof = $existing_row['image_proof'];
            $unboxing_video = $existing_row['unboxing_video'];
            $rev_pickup_status = 'Pending';
            $rev_pickup_status_date = date('Y-m-d H:i:s');
            $qury1 = "insert into dto_orders(dto_type,order_from,dto_way,ticket_id,order_id,customer_name,mobile_number,courier,tracking_number,qty,amount,deduction,deduction1,deduction2,final_amount,reason,agent_id,payment_method,upi_id,account_name,account_number,ifsc_code,bank_name,client_damage_image,unboxing_video,gpay_number,rev_pickup_status,rev_pickup_status_date) 
              values
              (
              '$dto_type','$request_from','$dto_way','$ticket_id','$order_id','$name','$mobile','$courier_name','$awb_number',$total_qty,'$total_amount',$deduction,$deduction1,$deduction2,'$total_refund','$reason',$uid,$payment_method,'$upi_id','$account_name','$account_number','$ifsc_code','$bank_name','$image_proof','$unboxing_video','$upi_id','$rev_pickup_status','$rev_pickup_status_date')";
            $sq1 = $con->query($qury1);
        }

        echo json_encode(["status" => "success", "message" => "Inquiry Details Updated Successfully.", 'redirect_page' => $redirect_page]);
    } else {
        echo json_encode(["status" => "error", "message" => "Something went wrong"]);
    }
}else{
    echo json_encode(["status" => "error", "message" => "Invalid Request"]);
}
