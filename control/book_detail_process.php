<?php
include_once '../model/mydb.php';
include_once 'auth.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$bookId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($bookId <= 0) {
    header("Location: ../view/Home.php");
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
tryRememberLogin($mydb, $conn);

$bookResult = $mydb->getBookById($bookId, $conn);
if ($bookResult->num_rows === 0) {
    $mydb->closeConn($conn);
    header("Location: ../view/Home.php");
    exit();
}
$book = $bookResult->fetch_assoc();

$cartCount = 0;
if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "customer") {
    $cartCount = $mydb->getCartCount($_SESSION["user_id"], $conn);
}
