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

$orders = $mydb->getCustomerOrders($_SESSION["user_id"], $conn);
$cartCount = $mydb->getCartCount($_SESSION["user_id"], $conn);
