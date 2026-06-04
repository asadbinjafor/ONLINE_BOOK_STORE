<?php
include '../control/order_success_process.php';
include_once '../control/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - BookHub</title>
    <link rel="stylesheet" type="text/css" href="../css/task3_style.css">
</head>
<body>

<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="Home.php">BookHub</a>
        <div class="nav-links">
            <a href="Home.php">Home</a>
            <a href="cart.php">Cart (<?php echo $cartCount; ?>)</a>
            <a href="customer_orders.php">My Orders</a>
            <a href="profile.php">Profile</a>
            <a href="../control/logout_process.php">Logout</a>
            <span class="nav-user-info">Hi, <?php echo htmlspecialchars($_SESSION["name"]); ?></span>
        </div>
    </div>
</nav>

<div class="page-header success-header">
    <h1>Order Placed Successfully!</h1>
    <p>Your order is pending admin approval</p>
</div>

<div class="main-container">

    <div class="card order-success-card">
        <div class="success-icon">&#10003;</div>
        <h2>Order #<?php echo $order["id"]; ?></h2>
        <span class="order-status-badge status-pending">Pending Admin Approval</span>

        <div class="order-details">
            <p><strong>Customer:</strong> <?php echo htmlspecialchars($order["customer_name"]); ?></p>
            <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order["shipping_address"]); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order["payment_method"]); ?></p>
            <p><strong>Order Date:</strong> <?php echo date("d M Y, h:i A", strtotime($order["order_date"])); ?></p>
        </div>

        <div class="cart-table-wrapper">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Author</th>
                        <th>Qty</th>
                        <th>Unit Price (BDT)</th>
                        <th>Subtotal (BDT)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $orderItems->fetch_assoc()){ ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item["book_title"]); ?></td>
                            <td><?php echo htmlspecialchars($item["author"]); ?></td>
                            <td><?php echo $item["quantity"]; ?></td>
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

        <div class="cart-actions" style="justify-content:center;">
            <a class="btn-primary" href="customer_order_detail.php?order_id=<?php echo $order["id"]; ?>">View Order</a>
            <a class="btn-secondary" href="order_invoice.php?order_id=<?php echo $order["id"]; ?>" target="_blank">Print Invoice</a>
            <a class="btn-primary" href="Home.php">Continue Shopping</a>
        </div>
    </div>

</div>

</body>
</html>
<?php $mydb->closeConn($conn); ?>

