<?php

require_once '../config/database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$mobile = $_GET['mobile'];

$stmt = $con->prepare("
        SELECT
            o.order_id,
            o.order_id,
            oi.product_name,
            oi.product_qty,
            oi.product_unique_price,
            oi.product_price,
            DATE_FORMAT(o.order_date, '%d %b %Y')   AS order_date,
            o.status
        FROM shopify_order o
        INNER JOIN shopify_order_product oi ON oi.orderr_id = o.order_id
        WHERE
            o.cmobile  = ?
            AND o.status = '1'
            AND o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ORDER BY o.order_date DESC
        LIMIT 20
    ");
$stmt->bind_param('s', $mobile);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];

while ($row = $result->fetch_assoc()) {

    $orders[] = [
        'id' => $row['order_id'],
        'item' => $row['product_name'],
        'date' => $row['order_date'],
        'qty' => $row['product_qty'],
        'price' => '₹' . number_format($row['product_price']),
        'status' => 'Delivered'
    ];
}

echo json_encode([
    'success' => true,
    'orders' => $orders
]);
