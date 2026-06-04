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

$errors = array();

if (!isset($_SESSION["shipping_address"]) || $_SESSION["shipping_address"] === "") {
    header("Location: ../view/checkout.php");
    exit();
}

$cartItems = $mydb->getCartItems($_SESSION["user_id"], $conn);
$cartCount = $mydb->getCartCount($_SESSION["user_id"], $conn);

if ($cartCount === 0) {
    header("Location: ../view/cart.php");
    exit();
}

$totalAmount = 0;
$itemsArray = array();
while ($item = $cartItems->fetch_assoc()) {
    $item["subtotal"] = $item["quantity"] * $item["price"];
    $totalAmount += $item["subtotal"];
    $itemsArray[] = $item;
}

if (isset($_POST["confirm_payment"])) {
    $paymentMethod = trim($_POST["payment_method"] ?? "");
    $validMethods = array("Credit Card", "bKash", "Nagad", "Bank Transfer", "Cash on Delivery");

    if (!in_array($paymentMethod, $validMethods, true)) {
        $errors["payment_method"] = "Please select a valid payment method";
    }

    foreach ($itemsArray as $item) {
        if ($item["quantity"] > $item["stock"]) {
            $errors["stock"] = $item["title"] . " has only " . $item["stock"] . " units in stock";
            break;
        }
    }

    if (count($errors) === 0) {
        $conn->begin_transaction();
        try {
            $orderId = $mydb->createOrder($_SESSION["user_id"], $totalAmount, $paymentMethod, $conn);
            if (!$orderId) {
                throw new Exception("Failed to create order");
            }

            foreach ($itemsArray as $item) {
                if (!$mydb->createOrderItem($orderId, $item["book_id"], $item["quantity"], $item["price"], $conn)) {
                    throw new Exception("Order item failed");
                }
                if (!$mydb->decreaseStock($item["book_id"], $item["quantity"], $conn)) {
                    throw new Exception("Stock update failed");
                }
            }

            $txnId = "TXN" . time() . rand(1000, 9999);
            $mydb->createPayment($orderId, $totalAmount, $paymentMethod, $txnId, $conn);
            $mydb->clearCart($_SESSION["user_id"], $conn);
            unset($_SESSION["shipping_address"]);
            $conn->commit();
            header("Location: ../view/order_success.php?order_id=" . $orderId);
            exit();
        } catch (Exception $ex) {
            $conn->rollback();
            $errors["database"] = $ex->getMessage();
        }
    }
}
