<?php
include_once '../model/mydb.php';
include_once 'customer_gate.php';
requireCustomerApi();

$status = trim($_GET["status"] ?? "");
$from = trim($_GET["from"] ?? "");
$to = trim($_GET["to"] ?? "");
$q = trim($_GET["q"] ?? "");

if ($status !== "" && !in_array($status, array("pending", "confirmed", "shipped", "delivered"), true)) {
    echo json_encode(array("success" => false, "message" => "Invalid status filter"));
    exit();
}
if (strlen($q) > 100) {
    echo json_encode(array("success" => false, "message" => "Search text is too long"));
    exit();
}
if ($from !== "" && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) {
    echo json_encode(array("success" => false, "message" => "Invalid from date"));
    exit();
}
if ($to !== "" && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
    echo json_encode(array("success" => false, "message" => "Invalid to date"));
    exit();
}
if ($from !== "" && $to !== "" && $from > $to) {
    echo json_encode(array("success" => false, "message" => "From date cannot be after to date"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$result = $mydb->searchCustomerOrders($_SESSION["user_id"], $status, $from, $to, $q, $conn);

$orders = array();
while ($row = $result->fetch_assoc()) {
    $orders[] = array(
        "id" => (int)$row["id"],
        "total_amount" => $row["total_amount"],
        "payment_method" => htmlspecialchars($row["payment_method"] ?? ""),
        "status" => htmlspecialchars($row["status"]),
        "order_date" => date("d M Y, h:i A", strtotime($row["order_date"]))
    );
}

$mydb->closeConn($conn);
echo json_encode(array("success" => true, "orders" => $orders));
