<?php
include("../config/database.php");

$action = !empty($_POST['action']) ? $_POST['action'] : "";
if($action == 'update-product'){
    $product_id = $_POST['product_id'];
    $shopify_product_id = $_POST['shopify_product_id'];
    $shelf = $_POST['shelf'];
    $status = $_POST['status'];
    $total_stock = 0;
    $kt_ecommerce_add_product_options = $_POST['kt_ecommerce_add_product_options'];
    if($kt_ecommerce_add_product_options){
        foreach($kt_ecommerce_add_product_options as $variants){
            $inventory_stock = $variants['inventory_stock'];
            $total_stock += $inventory_stock;
            $variant_id = $variants['variant_id'];
            $update_variant = "UPDATE product_variants SET stock = ? where product_variant_id  = ?";
            $stmt1 = $con->prepare($update_variant);
            $stmt1->bind_param("ii", $inventory_stock,$variant_id);
            $stmt1->execute();
        }
    }
    
    $update_query = "UPDATE product SET status = ?, product_stock = ? where id = ?";
    $stmt = $con->prepare($update_query);
    $stmt->bind_param("iii", $status,$total_stock,$product_id);
    if($stmt->execute()){
        echo json_encode(["status" => "success", "message" => "Product Details Updated Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while update product details"]);
    }
}