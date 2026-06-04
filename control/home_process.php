<?php
include_once '../model/mydb.php';
include_once 'auth.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
tryRememberLogin($mydb, $conn);

$categoryId = isset($_GET["category_id"]) ? (int)$_GET["category_id"] : 0;

$categories = $mydb->getCategories($conn);
$authors = $mydb->getAuthors($conn);

$featuredBooks = array();
$excludeFeaturedIds = array();
if ($categoryId === 0) {
    $featuredResult = $mydb->getFeaturedBooks(3, $conn);
    while ($row = $featuredResult->fetch_assoc()) {
        $featuredBooks[] = $row;
        $excludeFeaturedIds[] = (int)$row["id"];
    }
}

$books = $mydb->getBooks($categoryId, $conn, $excludeFeaturedIds);
$bookListCount = $books->num_rows;

$cartCount = 0;
if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "customer") {
    $cartCount = $mydb->getCartCount($_SESSION["user_id"], $conn);
}
