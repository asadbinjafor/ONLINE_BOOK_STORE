<?php
include_once '../model/mydb.php';
include_once 'admin_gate.php';
requireAdminApi();

$userId = (int)($_POST["user_id"] ?? 0);
if ($userId <= 0) {
    echo json_encode(array("success" => false, "message" => "Invalid user"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$conn->begin_transaction();

try {
    $mydb->deleteUserCart($userId, $conn);
    $mydb->deleteUserPayments($userId, $conn);
    $mydb->deleteUserOrderItems($userId, $conn);
    $mydb->deleteUserOrders($userId, $conn);
    if (!$mydb->deleteUser($userId, $conn)) {
        throw new Exception("Delete failed or user is not a customer");
    }
    $conn->commit();
    $mydb->closeConn($conn);
    echo json_encode(array("success" => true, "message" => "Customer removed"));
} catch (Exception $ex) {
    $conn->rollback();
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => $ex->getMessage()));
}
