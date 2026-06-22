<?php

include("../config/database.php");

$from_date = $_POST['from_date'] ?? '2025-01-01';
$to_date = $_POST['to_date'] ?? date('Y-m-d');
$payment_type = !empty($_POST['payment_type']) ? $_POST['payment_type'] : "";
$order_type = !empty($_POST['order_from']) ? $_POST['order_from'] : "";
$order_typ = !empty($_POST['order_type']) ? $_POST['order_type'] : "";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=orders.csv');

$output = fopen('php://output', 'w');

// CSV Header
fputcsv($output, ['Sr No.', 'Order ID', 'Order Date', 'Customer', 'Channel', 'Items', 'City', 'Pincode', 'Payment', 'Amount', 'Status']);

if($order_typ == 1){
    $sql = "SELECT * from shopify_order where status = 1";
}else{
    $sql = "SELECT * from shopify_order where 1=1";
}


if ($from_date && $to_date) {
    $sql .= " AND order_date between '$from_date' AND '$to_date' ";
}

if ($payment_type && $payment_type) {
    $sql .= " AND payment_method = $payment_type ";
}

if($order_type!='all' && $order_type){
    $sql .= " AND order_from = $order_type ";
}



$sql .= " ORDER BY order_date DESC";

$result = $con->query($sql);
$sr = 1;

if ($result) {
    while ($r = $result->fetch_assoc()) {
        
        $product_details_query = mysqli_query($con,"SELECT SUM(product_qty) AS allcount FROM shopify_order_product WHERE orderr_id = '{$r['invoice_no']}'");
        $totalItems = mysqli_fetch_assoc($product_details_query)['allcount'];
        fputcsv($output, [
            $sr++,
            $r['order_id'],
            $r['order_date'],
            $r['customer_name'],
            ($r['order_from'] == 1) ? 'Shopify' : 'U3K',
            $totalItems,
            $r['city'],
            $r['zipcode'] ?? "N/A",
            ($r['payment_method'] == 1) ? 'Prepaid' : 'COD',
            $r['amount'],
            $r['status']
            
        ]);
    }
}
