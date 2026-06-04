<?php
include_once '../model/mydb.php';
include_once 'customer_gate.php';
requireCustomerApi();

$orderId = (int)($_POST["order_id"] ?? 0);
if ($orderId <= 0) {
    echo json_encode(array("success" => false, "message" => "Invalid order"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$result = $mydb->reorderItemsToCart($orderId, $_SESSION["user_id"], $conn);
$mydb->closeConn($conn);
echo json_encode($result);
