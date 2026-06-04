<?php
header("Content-Type: application/json");
include_once '../model/mydb.php';
include_once 'customer_gate.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
requireCustomerApi();

$address = trim($_POST["address"] ?? "");
$paymentMethod = trim($_POST["payment_method"] ?? "");
$allowedPayments = array("Credit Card", "bKash", "Nagad", "Bank Transfer", "Cash on Delivery");

if ($address === "") {
    echo json_encode(array("success" => false, "message" => "Delivery address is required"));
    exit();
}
if (!in_array($paymentMethod, $allowedPayments, true)) {
    echo json_encode(array("success" => false, "message" => "Select a valid payment method"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$conn->begin_transaction();

try {
    $items = $mydb->getCartItems($_SESSION["user_id"], $conn);
    if ($items->num_rows === 0) {
        throw new Exception("Cart is empty");
    }

    $total = 0;
    $cartData = array();
    while ($row = $items->fetch_assoc()) {
        if ($row["quantity"] > $row["stock"]) {
            throw new Exception($row["title"] . " has insufficient stock");
        }
        $total += $row["price"] * $row["quantity"];
        $cartData[] = $row;
    }

    $orderId = $mydb->createOrder($_SESSION["user_id"], $total, $paymentMethod, $conn);
    if (!$orderId) {
        throw new Exception("Could not create order");
    }

    foreach ($cartData as $item) {
        if (!$mydb->createOrderItem($orderId, $item["book_id"], $item["quantity"], $item["price"], $conn)) {
            throw new Exception("Order item failed");
        }
        if (!$mydb->decreaseStock($item["book_id"], $item["quantity"], $conn)) {
            throw new Exception("Stock update failed for " . $item["title"]);
        }
    }

    $txnId = "TXN" . time() . rand(100, 999);
    if (!$mydb->createPayment($orderId, $total, $paymentMethod, $txnId, $conn)) {
        throw new Exception("Payment record failed");
    }

    $userResult = $mydb->getUserById($_SESSION["user_id"], $conn);
    $u = $userResult->fetch_assoc();
    $mydb->updateProfile($_SESSION["user_id"], $u["name"], $u["email"], $address, $u["phone"], $u["profile_picture"], $conn);

    $mydb->clearCart($_SESSION["user_id"], $conn);
    $conn->commit();
    $mydb->closeConn($conn);

    echo json_encode(array(
        "success" => true,
        "message" => "Order placed successfully",
        "order_id" => $orderId,
        "redirect" => "../view/order_success.php?order_id=" . $orderId
    ));
} catch (Exception $ex) {
    $conn->rollback();
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => $ex->getMessage()));
}
