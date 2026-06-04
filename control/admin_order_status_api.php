<?php
include_once '../model/mydb.php';
include_once 'admin_gate.php';
requireAdminApi();

$orderId = (int)($_POST["order_id"] ?? 0);
$status = trim($_POST["status"] ?? "");
$allowed = array("confirmed", "shipped", "delivered");

if ($orderId <= 0 || !in_array($status, $allowed, true)) {
    echo json_encode(array("success" => false, "message" => "Invalid request"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$result = $mydb->getOrderById($orderId, $conn);

if ($result->num_rows === 0) {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Order not found"));
    exit();
}

$order = $result->fetch_assoc();
$current = $order["status"];

if ($status === "confirmed" && $current !== "pending") {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Only pending orders can be confirmed"));
    exit();
}
if ($status === "shipped" && $current !== "confirmed") {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Only confirmed orders can be shipped"));
    exit();
}
if ($status === "delivered" && $current !== "shipped") {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Only shipped orders can be delivered"));
    exit();
}

if ($mydb->updateOrderStatus($orderId, $status, $conn)) {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => true, "message" => "Order " . $status, "status" => $status));
} else {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Update failed"));
}
