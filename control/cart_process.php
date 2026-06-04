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



$items = $mydb->getCartItems($_SESSION["user_id"], $conn);

$cartCount = $mydb->getCartCount($_SESSION["user_id"], $conn);



$total = 0;

$cartRows = array();

while ($row = $items->fetch_assoc()) {

    $subtotal = $row["price"] * $row["quantity"];

    $total += $subtotal;

    $row["subtotal"] = $subtotal;

    $cartRows[] = $row;

}

