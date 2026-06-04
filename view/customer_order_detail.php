<?php
include '../control/customer_order_detail_process.php';
include_once '../control/app.php';

$statusMessages = array(
    "pending" => "Your order is waiting for admin confirmation.",
    "confirmed" => "Your order has been confirmed and is being processed.",
    "shipped" => "Your order has been shipped.",
    "delivered" => "Your order has been delivered."
);
$currentMsg = $statusMessages[$order["status"]] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo (int)$order["id"]; ?> - BookHub</title>
    <link rel="stylesheet" href="../css/task3_style.css">
    <link rel="stylesheet" href="../css/task4_style.css">
</head>
<body>
<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="Home.php">BookHub</a>
        <div class="nav-links">
            <a href="Home.php">Home</a>
            <a href="cart.php">Cart (<span id="navCartCount"><?php echo $cartCount; ?></span>)</a>
            <a href="customer_orders.php">My Orders</a>
            <a href="profile.php">Profile</a>
            <a href="../control/logout_process.php">Logout</a>
        </div>
    </div>
</nav>

<div class="page-header">
    <h1>Order #<?php echo (int)$order["id"]; ?></h1>
    <p>Order details and tracking</p>
</div>

<div class="main-container">
    <div id="orderDetailMsg"></div>
    <div class="card order-detail-card">
        <div class="order-detail-header">
            <span class="order-status-badge status-<?php echo e($order["status"]); ?>"><?php echo ucfirst(e($order["status"])); ?></span>
            <div class="order-detail-actions">
                <a class="btn-secondary" href="order_invoice.php?order_id=<?php echo (int)$order["id"]; ?>" target="_blank">Print Invoice</a>
                <a class="btn-secondary" href="customer_orders.php">Back to Orders</a>
            </div>
        </div>

        <div class="status-message-box">
            <strong>Status:</strong> <?php echo e($currentMsg); ?>
        </div>

        <div class="order-details">
            <p><strong>Order Date:</strong> <?php echo date("d M Y, h:i A", strtotime($order["order_date"])); ?></p>
            <p><strong>Shipping Address:</strong> <?php echo e($order["shipping_address"]); ?></p>
            <p><strong>Payment Method:</strong> <?php echo e($order["payment_method"] ?? "—"); ?></p>
            <?php if ($payment) { ?>
                <p><strong>Transaction ID:</strong> <?php echo e($payment["transaction_id"] ?? "—"); ?></p>
                <p><strong>Payment Date:</strong> <?php echo date("d M Y, h:i A", strtotime($payment["payment_date"])); ?></p>
            <?php } ?>
        </div>

        <div class="cart-table-wrapper">
            <table class="cart-table">
                <thead>
                    <tr><th>Book</th><th>Author</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                    <?php while ($item = $orderItems->fetch_assoc()) { ?>
                    <tr>
                        <td><a href="book_detail.php?id=<?php echo (int)$item["book_id"]; ?>"><?php echo e($item["book_title"]); ?></a></td>
                        <td><?php echo e($item["author"]); ?></td>
                        <td><?php echo (int)$item["quantity"]; ?></td>
                        <td><?php echo number_format($item["unit_price"], 2); ?></td>
                        <td><?php echo number_format($item["quantity"] * $item["unit_price"], 2); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="cart-total-label">Total:</td>
                        <td class="cart-total-value">BDT <?php echo number_format($order["total_amount"], 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="order-detail-footer">
            <?php if (in_array($order["status"], array("confirmed", "shipped", "delivered"), true)) { ?>
                <button type="button" class="btn-primary" onclick="reorderPurchase(<?php echo (int)$order['id']; ?>)">Reorder</button>
            <?php } ?>
        </div>
    </div>
</div>
<script src="../js/task4_script.js"></script>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
