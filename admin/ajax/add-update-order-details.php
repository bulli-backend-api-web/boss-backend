<?php
include("../config/database.php");
include("../config/auth_check.php");

error_reporting(E_ALL);
ini_set('display_errors',1);

$action = !empty($_POST['action']) ? $_POST['action'] : "";
if($action == 'add-order'){
    $channel = $_POST['channel'];
    $fullname = $_POST['fullname'];
    $cmobile = $_POST['cmobile'];
    $product_id = $_POST['product_id'];
    $qty = $_POST['qty'];
    $size = $_POST['size'];
    $delivery_date = !empty($_POST['delivery_date']) ? $_POST['delivery_date'] : "";
    $payment_method = $_POST['payment_method'];
    $shipping_address = $_POST['shipping_address'];
    
    $remarks = $_POST['remarks'];
    $status =1;
    $date = date('Y-m-d');
    $datee = date('Y-m-d H:i:s');
    $invoice_no = get_order_invoice_no();
    
    $net_total = $_POST['net_total'];
    $cod_charge = $_POST['cod_charge'];
    $discount = $_POST['discount'];
    $amount = $_POST['grand_total'];
    $pincode = $_POST['pincode'];
    $country_id = $_POST['country_id'];
    $state_id = $_POST['state_id'];
    $city = $_POST['city'];
    $product_id = $_POST['product_id'];
    
    $store_id = !empty($_POST['store_id']) ? $_POST['store_id'] : "";
    $whole_saler_id = !empty($_POST['whole_saler_id']) ? $_POST['whole_saler_id'] : "";
    
    $insert_order = "INSERT INTO orderr(uid,fullname,address_1,cmobile,grandtotal,status,date,datee,invoice_no,payment_method,remark,order_from,zipcode,city,state,country,net_total,discount,scharge_cod,store_id,wholesaler_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($insert_order);
    $stmt->bind_param('isssdisssissssssdddii',$uid,$fullname,$shipping_address,$cmobile,$amount,$status,$date,$datee,$invoice_no,$payment_method,$remarks,$channel,$pincode,$city,$state_id,$country_id,$net_total,$discount,$cod_charge,$store_id,$whole_saler_id);
    if($stmt->execute()){
        $last_id = $stmt->insert_id;
        if($product_id){
            for ($ckoutt = 0; $ckoutt < count($_POST['product_id']); $ckoutt++) {
                $product_id = $_POST['product_id'][$ckoutt];
                $product_unique_price = $_POST['amount'][$ckoutt];
                $product_qty = $_POST['qty'][$ckoutt];
                $size = $_POST['size'][$ckoutt];
                $product_price = $product_unique_price * $product_qty;

                $product_price_sub = (int) $product_price;
                $productIdArray[]  = $product_id;
                $order_product = "INSERT INTO orderr_product(order_id,product_id,size,qty,product_unique_price,price) VALUES (?,?,?,?,?,?)";
                $order_stmt = $con->prepare($order_product);
                $order_stmt->bind_param('iisiid',$last_id,$product_id,$size,$product_qty,$product_unique_price,$product_price);
            }
        }
        
        if($order_stmt->execute()){
            echo json_encode(["status" => "success", "message" => "Order Created Successfully."]);
        }else{
            echo json_encode(["status" => "error", "message" => "Error while creating order"]);
        }

    }else{
        echo json_encode(["status" => "error", "message" => "Error while creating order"]);
    }
}else if($action == 'update-order'){
    $order_id = $_POST['hidden_order_id'];
    $status = $_POST['status'];
    $verify_date = date('Y-m-d H:i:s');
    $verify_by = $uid;
    $reject_remarks = isset($_POST['reject_remarks']) ? $_POST['reject_remarks'] : "";
    $extra_order_id = isset($_POST['extra_order_id']) ? $_POST['extra_order_id'] : "";
    $update_order = "UPDATE shopify_order SET status = ?, verify_date = ?, verify_by = ?,order_status_extra_id = ?, reject_remarks = ? where order_id = ?";
    $stmt = $con->prepare($update_order);
    $stmt->bind_param('isisis',$status,$verify_date,$verify_by,$order_id,$extra_order_id,$reject_remarks);
    
    if($stmt->execute()){
        echo json_encode(["status" => "success", "message" => "Order Confirm Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while update order status"]);
    }
}
