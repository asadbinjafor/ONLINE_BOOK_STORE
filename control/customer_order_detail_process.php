<?php
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'customer_gate.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
tryRememberLogin($mydb, $conn);
requireCustomer();

$orderId = (int)($_GET["order_id"] ?? 0);
if ($orderId <= 0) {
    header("Location: ../view/customer_orders.php");
    exit();
}

$orderResult = $mydb->getOrderWithItems($orderId, $_SESSION["user_id"], $conn);
if ($orderResult->num_rows === 0) {
    header("Location: ../view/customer_orders.php");
    exit();
}

$order = $orderResult->fetch_assoc();
$orderItems = $mydb->getOrderItems($orderId, $conn);
$paymentResult = $mydb->getPaymentByOrderId($orderId, $conn);
$payment = $paymentResult->num_rows > 0 ? $paymentResult->fetch_assoc() : null;
$cartCount = $mydb->getCartCount($_SESSION["user_id"], $conn);
