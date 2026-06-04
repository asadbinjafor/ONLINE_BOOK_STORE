<?php
include_once '../model/mydb.php';
include_once 'admin_gate.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
requireAdmin();

$mydb = new MyDB();
$conn = $mydb->createConn();
$orders = $mydb->getAllOrders("", "", "", $conn);
