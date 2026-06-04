<?php
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'admin_gate.php';
include_once 'upload.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
requireAdmin();

$mydb = new MyDB();
$conn = $mydb->createConn();
$successMsg = "";
$errorMsg = "";

if (isset($_GET["delete"])) {
    $bookId = (int)$_GET["delete"];
    if ($bookId > 0) {
        if ($mydb->bookInPendingOrder($bookId, $conn)) {
            $errorMsg = "Cannot delete: book is in a pending order";
        } else {
            $bookResult = $mydb->getBookById($bookId, $conn);
            if ($bookResult->num_rows > 0) {
                $book = $bookResult->fetch_assoc();
                if ($mydb->deleteBook($bookId, $conn)) {
                    deleteBookImageFile($book["image_path"]);
                    $successMsg = "Book deleted";
                }
            }
        }
    }
}

$books = $mydb->getAllBooks($conn);
$categories = $mydb->getCategories($conn);
