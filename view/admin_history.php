<?php
include '../control/admin_history_process.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History - BookHub</title>
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
    <h1>Customers' Purchase History</h1>
    <p>All processed orders with book details (filter by date/status)</p>
</div>

<div class="main-container">

    <form method="get" class="filters-bar" style="margin-bottom:20px;">
        <select name="status">
            <option value="">All Status</option>
            <?php foreach (array("pending","confirmed","shipped","delivered") as $st) { ?>
            <option value="<?php echo $st; ?>" <?php if (($statusFilter ?? "") === $st) echo 'selected'; ?>><?php echo ucfirst($st); ?></option>
            <?php } ?>
        </select>
        <input type="date" name="from" value="<?php echo htmlspecialchars($from ?? ""); ?>">
        <input type="date" name="to" value="<?php echo htmlspecialchars($to ?? ""); ?>">
        <button type="submit">Filter</button>
        <a href="admin_history.php">Reset</a>
    </form>

    <?php if(empty($orderData)){ ?>
        <div class="no-results">No orders found.</div>
    <?php } else { ?>

        <?php foreach($orderData as $order){ ?>
            <div class="card history-order-card">
                <div class="history-order-header">
                    <div>
                        <strong>Order #<?php echo $order["id"]; ?></strong>
                        &nbsp;|&nbsp;
                        <?php echo date("d M Y, h:i A", strtotime($order["order_date"])); ?>
                        &nbsp;|&nbsp;
                        <span class="order-status-badge status-<?php echo htmlspecialchars($order["status"]); ?>"><?php echo ucfirst(htmlspecialchars($order["status"])); ?></span>
                    </div>
                    <div class="history-customer-info">
                        <strong><?php echo htmlspecialchars($order["customer_name"]); ?></strong>
                        &nbsp;(<?php echo htmlspecialchars($order["customer_email"]); ?>)
                        <?php if(!empty($order["customer_phone"])){ ?>
                            &nbsp;&bull;&nbsp;<?php echo htmlspecialchars($order["customer_phone"]); ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="history-order-meta">
                    <span><strong>Shipping:</strong> <?php echo htmlspecialchars($order["shipping_address"]); ?></span>
                    <span><strong>Payment:</strong> <?php echo htmlspecialchars($order["payment_method"] ?? "â€”"); ?></span>
                    <span><strong>Total:</strong> BDT <?php echo number_format($order["total_amount"], 2); ?></span>
                </div>

                <?php if(!empty($order["items"])){ ?>
                    <div class="admin-table-wrapper" style="margin-top:12px;">
                        <table class="admin-table history-items-table">
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
                                <?php foreach($order["items"] as $item){ ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item["book_title"]); ?></td>
                                        <td><?php echo htmlspecialchars($item["author"]); ?></td>
                                        <td><?php echo $item["quantity"]; ?></td>
                                        <td><?php echo number_format($item["unit_price"], 2); ?></td>
                                        <td><?php echo number_format($item["quantity"] * $item["unit_price"], 2); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

    <?php } ?>

</div>

</body>
</html>

