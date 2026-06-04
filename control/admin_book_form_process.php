<?php
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'admin_gate.php';
include_once 'upload.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
requireAdmin();

$bookId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$isEdit = $bookId > 0;
$errors = array();
$book = array(
    "title" => "", "author" => "", "description" => "",
    "price" => "", "category_id" => "", "stock" => "", "image_path" => ""
);

$mydb = new MyDB();
$conn = $mydb->createConn();
$categories = $mydb->getCategories($conn);

if ($isEdit) {
    $result = $mydb->getBookById($bookId, $conn);
    if ($result->num_rows === 0) {
        header("Location: ../view/admin_books.php");
        exit();
    }
    $book = $result->fetch_assoc();
}

if (isset($_POST["save_book"])) {
    $book["title"] = trim($_POST["title"] ?? "");
    $book["author"] = trim($_POST["author"] ?? "");
    $book["description"] = trim($_POST["description"] ?? "");
    $book["price"] = $_POST["price"] ?? "";
    $book["category_id"] = (int)($_POST["category_id"] ?? 0);
    $book["stock"] = (int)($_POST["stock"] ?? 0);

    if ($book["title"] === "") {
        $errors["title"] = "Title is required";
    }
    if ($book["author"] === "") {
        $errors["author"] = "Author is required";
    }
    if (!is_numeric($book["price"]) || (float)$book["price"] <= 0) {
        $errors["price"] = "Price must be greater than 0";
    }
    if ($book["category_id"] <= 0) {
        $errors["category_id"] = "Select a category";
    }
    if ($book["stock"] < 0) {
        $errors["stock"] = "Stock cannot be negative";
    }

    $imagePath = $book["image_path"] ?? "";
    if (count($errors) === 0) {
        $imagePath = uploadBookImage("image", $imagePath, $errors);
    }

    if (count($errors) === 0) {
        $price = (float)$book["price"];
        if ($isEdit) {
            $ok = $mydb->updateBook($bookId, $book["title"], $book["author"], $book["description"], $price, $book["category_id"], $book["stock"], $imagePath, $conn);
        } else {
            $ok = $mydb->createBook($book["title"], $book["author"], $book["description"], $price, $book["category_id"], $book["stock"], $imagePath, $conn);
        }
        if ($ok) {
            header("Location: ../view/admin_books.php?saved=1");
            exit();
        }
        $errors["database"] = "Save failed";
    }
}
