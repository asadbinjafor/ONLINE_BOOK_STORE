<?php
header("Content-Type: application/json");
include_once '../model/mydb.php';
include_once 'customer_gate.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
requireCustomerApi();

$cartId = (int)($_POST["cart_id"] ?? 0);
if ($cartId <= 0) {
    echo json_encode(array("success" => false, "message" => "Invalid cart item"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();

if ($mydb->removeCartItem($cartId, $_SESSION["user_id"], $conn)) {
    $cartCount = $mydb->getCartCount($_SESSION["user_id"], $conn);
    $mydb->closeConn($conn);
    echo json_encode(array("success" => true, "message" => "Removed from cart", "cart_count" => $cartCount));
} else {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Remove failed"));
}
