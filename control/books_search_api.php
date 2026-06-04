<?php
header("Content-Type: application/json");
include_once '../model/mydb.php';
include_once 'app.php';
include_once 'auth.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$q = trim($_GET["q"] ?? "");
$author = trim($_GET["author"] ?? "");
$genre = trim($_GET["genre"] ?? "");
$filter = trim($_GET["filter"] ?? "");

if ($q !== "" && strlen($q) > 100) {
    echo json_encode(array("success" => false, "message" => "Search text too long", "books" => array()));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$result = $mydb->searchBooks($q, $author, $genre, $filter, $conn);

$books = array();
while ($row = $result->fetch_assoc()) {
    $img = $row["image_path"] ? BOOK_UPLOAD_WEB . $row["image_path"] : "";
    $books[] = array(
        "id" => (int)$row["id"],
        "title" => htmlspecialchars($row["title"]),
        "author" => htmlspecialchars($row["author"]),
        "price" => (float)$row["price"],
        "description" => htmlspecialchars($row["description"] ?? ""),
        "stock" => (int)$row["stock"],
        "category_name" => htmlspecialchars($row["category_name"] ?? ""),
        "image_url" => $img
    );
}

$mydb->closeConn($conn);
echo json_encode(array("success" => true, "books" => $books));
