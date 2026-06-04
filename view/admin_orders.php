<?php
include '../control/admin_orders_process.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Purchase Requests - BookHub</title>
    <link rel="stylesheet" type="text/css" href="../css/task2_style.css">
</head>
<body>

<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="admin_dashboard.php">BookHub Admin</a>
        <div class="nav-links">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_books.php">Books</a>
            <a href="admin_users.php">All Users</a>
            <a href="admin_customers.php">Customers</a>
            <a href="admin_orders.php">Orders</a>
            <a href="admin_history.php">History</a>
            <a href="Home.php">Home</a>
            <a href="../control/logout_process.php">Logout</a>
            <span class="nav-user-info">Admin: <?php echo htmlspecialchars($_SESSION["name"]); ?></span>
        </div>
    </div>
</nav>

<div class="page-header">
    <h1>All Purchase Requests</h1>
    <p>View and Confirm or Mark Delivered customer orders</p>
</div>

<div class="main-container">

    <div id="orderMsg"></div>

    <?php if($orders->num_rows == 0){ ?>
        <div class="no-results">No orders placed yet.</div>
    <?php } else { ?>
        <div class="admin-table-wrapper">
            <table class="admin-table" id="ordersTable">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total (BDT)</th>
                        <th>Shipping Address</th>
                        <th>Payment</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = $orders->fetch_assoc()){ ?>
                        <tr id="order-row-<?php echo $order["id"]; ?>">
                            <td>#<?php echo $order["id"]; ?></td>
                            <td>
                                <?php echo htmlspecialchars($order["customer_name"]); ?><br>
                                <small><?php echo htmlspecialchars($order["customer_email"]); ?></small>
                            </td>
                            <td><?php echo number_format($order["total_amount"], 2); ?></td>
                            <td><?php echo htmlspecialchars($order["shipping_address"]); ?></td>
                            <td><?php echo htmlspecialchars($order["payment_method"] ?? "â€”"); ?></td>
                            <td><?php echo date("d M Y, h:i A", strtotime($order["order_date"])); ?></td>
                            <td>
                                <span class="order-status-badge status-<?php echo htmlspecialchars($order["status"]); ?>"
                                      id="status-badge-<?php echo $order["id"]; ?>">
                                    <?php echo ucfirst(htmlspecialchars($order["status"])); ?>
                                </span>
                            </td>
                            <td id="action-cell-<?php echo $order["id"]; ?>">
                                <?php if ($order["status"] === "pending") { ?>
                                    <button class="btn-action btn-accept-sm"
                                            onclick="updateOrderStatus(<?php echo (int)$order['id']; ?>, 'confirmed')">Confirm</button>
                                <?php } elseif ($order["status"] === "confirmed") { ?>
                                    <button class="btn-action btn-accept-sm"
                                            onclick="updateOrderStatus(<?php echo (int)$order['id']; ?>, 'shipped')">Ship</button>
                                <?php } elseif ($order["status"] === "shipped") { ?>
                                    <button class="btn-action btn-accept-sm"
                                            onclick="updateOrderStatus(<?php echo (int)$order['id']; ?>, 'delivered')">Deliver</button>
                                <?php } else { ?>
                                    <span class="text-muted">—</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

</div>

<script src="../js/task2_script.js"></script>
</body>
</html>
<?php $mydb->closeConn($conn); ?>

