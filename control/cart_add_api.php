<?php
header("Content-Type: application/json");
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'customer_gate.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
requireCustomerApi();

$bookId = (int)($_POST["book_id"] ?? 0);
$quantity = (int)($_POST["quantity"] ?? 0);

if ($bookId <= 0) {
    echo json_encode(array("success" => false, "message" => "Invalid book"));
    exit();
}
if ($quantity <= 0) {
    echo json_encode(array("success" => false, "message" => "Quantity must be at least 1"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$bookResult = $mydb->getBookById($bookId, $conn);

if ($bookResult->num_rows === 0) {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Book not found"));
    exit();
}

$book = $bookResult->fetch_assoc();
if ($quantity > (int)$book["stock"]) {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Only " . $book["stock"] . " units available"));
    exit();
}

if ($mydb->addToCart($_SESSION["user_id"], $bookId, $quantity, $conn)) {
    $cartCount = $mydb->getCartCount($_SESSION["user_id"], $conn);
    $mydb->closeConn($conn);
    echo json_encode(array("success" => true, "message" => "Added to cart", "cart_count" => $cartCount));
} else {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Failed to add to cart"));
}
