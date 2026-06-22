<?php
include("../config/database.php");
include("../config/auth_check.php");

$action = !empty($_POST['action']) ? $_POST['action'] : "";

if($action == 'add-whole-seller-details'){
    $business_name = $_POST['business_name'];
    $gst_number = $_POST['gst_number'];
    $business_type = $_POST['business_type'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $primary_contact_person = $_POST['primary_contact_person'];
    $mobile = !empty($_POST['mobile']) ? $_POST['mobile']: "";
    $email = !empty($_POST['email']) ? $_POST['email']: "";
    $whatsapp_number = !empty($_POST['whatsapp_number']) ? $_POST['whatsapp_number']: "";
    $rep_name = !empty($_POST['rep_name']) ? $_POST['rep_name']: "";
    $rep_number = !empty($_POST['rep_number']) ? $_POST['rep_number']: "";
    $agent_name = !empty($_POST['agent_name']) ? $_POST['agent_name']: "";
    $agent_commission = !empty($_POST['agent_commission']) ? $_POST['agent_commission']: "";
    $price_tier = !empty($_POST['price_tier']) ? $_POST['price_tier']: "";
    $base_discount = !empty($_POST['base_discount']) ? $_POST['base_discount']: "";
    $credit_days = !empty($_POST['credit_days']) ? $_POST['credit_days']: "";
    $credit_limit = !empty($_POST['credit_limit']) ? $_POST['credit_limit']: "";
    $payment_terms = !empty($_POST['payment_terms']) ? $_POST['payment_terms']: "";
    $territory_region = !empty($_POST['territory_region']) ? $_POST['territory_region']: "";
    $exclusivity = !empty($_POST['exclusivity']) ? $_POST['exclusivity']: "";
    $agreement_note = !empty($_POST['agreement_note']) ? $_POST['agreement_note']: "";
    
    $insert_query = "INSERT INTO wholesaler (
            business_name,
            gst_number,
            business_type,
            city,
            address,
            contact_person,
            contact_person_mobile,
            contact_person_email,
            contact_person_whatsapp,
            assign_rep_name,
            rep_phone,
            agent_name,
            agent_commision,
            price_tier,
            base_discount,
            credit_days,
            credit_limit,
            payment_terms,
            territory_region,
            exclusivity,
            agreement_notes
            
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $con->prepare($insert_query);
    $stmt->bind_param('ssssssssssssisiiissss',$business_name,$gst_number,$business_type,$city,$address,$primary_contact_person,$mobile,$email,$whatsapp_number,$rep_name,$rep_number,$agent_name,$agent_commission,$price_tier,$base_discount,$credit_days,$credit_limit,$payment_terms,$territory_region,$exclusivity,$agreement_note);
    if($stmt->execute()){
        echo json_encode(["status" => "success", "message" => "Wholeseler Registered Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while wholesaller"]);
    }
}else{
    echo json_encode(["status" => "error", "message" => "Invalid Request."]);
}