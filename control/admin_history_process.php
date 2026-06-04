<?php
include_once '../model/mydb.php';
include_once 'admin_gate.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
requireAdmin();

$statusFilter = $_GET["status"] ?? "";
$from = $_GET["from"] ?? "";
$to = $_GET["to"] ?? "";

$mydb = new MyDB();
$conn = $mydb->createConn();

if ($statusFilter !== "" || $from !== "" || $to !== "") {
    $ordersResult = $mydb->getAllOrders($statusFilter, $from, $to, $conn);
} else {
    $ordersResult = $mydb->getCompletedOrders($conn);
}

$orderData = array();
while ($order = $ordersResult->fetch_assoc()) {
    $items = $mydb->getOrderItems($order["id"], $conn);
    $order["items"] = array();
    while ($item = $items->fetch_assoc()) {
        $order["items"][] = $item;
    }
    $orderData[] = $order;
}

$mydb->closeConn($conn);
