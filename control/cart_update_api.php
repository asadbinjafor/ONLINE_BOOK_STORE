<?php
header("Content-Type: application/json");
include_once '../model/mydb.php';
include_once 'customer_gate.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
requireCustomerApi();

$cartId = (int)($_POST["cart_id"] ?? 0);
$quantity = (int)($_POST["quantity"] ?? 0);

if ($cartId <= 0 || $quantity <= 0) {
    echo json_encode(array("success" => false, "message" => "Invalid request"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$itemResult = $mydb->getCartItemById($cartId, $_SESSION["user_id"], $conn);

if ($itemResult->num_rows === 0) {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Cart item not found"));
    exit();
}

$item = $itemResult->fetch_assoc();
if ($quantity > (int)$item["stock"]) {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Only " . $item["stock"] . " units available"));
    exit();
}

if ($mydb->updateCartQuantity($cartId, $_SESSION["user_id"], $quantity, $conn)) {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => true, "message" => "Cart updated"));
} else {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Update failed"));
}
