<?php
include '../control/customer_orders_process.php';
include_once '../control/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - BookHub</title>
    <link rel="stylesheet" type="text/css" href="../css/task3_style.css">
    <link rel="stylesheet" type="text/css" href="../css/task4_style.css">
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
            <span class="nav-user-info">Hi, <?php echo htmlspecialchars($_SESSION["name"]); ?></span>
        </div>
    </div>
</nav>

<div class="page-header">
    <h1>My Order History</h1>
    <p>View, track and manage your orders</p>
</div>

<div class="main-container">

    <div class="filters-bar order-filters">
        <div class="filter-item">
            <label for="orderSearch">Search book</label>
            <input type="text" id="orderSearch" placeholder="Book title..." maxlength="100">
        </div>
        <div class="filter-item">
            <label for="statusFilter">Status</label>
            <select id="statusFilter">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
            </select>
        </div>
        <div class="filter-item">
            <label for="fromDate">From Date</label>
            <input type="date" id="fromDate">
        </div>
        <div class="filter-item">
            <label for="toDate">To Date</label>
            <input type="date" id="toDate">
        </div>
        <div class="filter-item filter-actions">
            <button type="button" class="btn-primary" onclick="searchOrders()">Search</button>
            <button type="button" class="btn-secondary" onclick="resetOrderFilters()">Reset</button>
        </div>
    </div>

    <div id="orderMsg"></div>

    <div class="cart-table-wrapper">
        <table class="cart-table" id="ordersTable">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Total (BDT)</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="ordersTableBody">
                <?php if($orders->num_rows == 0){ ?>
                    <tr id="noOrdersRow">
                        <td colspan="6" class="text-center">No orders found.</td>
                    </tr>
                <?php } else { ?>
                    <?php while($order = $orders->fetch_assoc()){ ?>
                        <tr>
                            <td>#<?php echo $order["id"]; ?></td>
                            <td><?php echo date("d M Y, h:i A", strtotime($order["order_date"])); ?></td>
                            <td><?php echo number_format($order["total_amount"], 2); ?></td>
                            <td><?php echo htmlspecialchars($order["payment_method"] ?? "â€”"); ?></td>
                            <td>
                                <span class="order-status-badge status-<?php echo htmlspecialchars($order["status"]); ?>">
                                    <?php echo ucfirst(htmlspecialchars($order["status"])); ?>
                                </span>
                            </td>
                            <td class="order-actions">
                                <a class="btn-link" href="customer_order_detail.php?order_id=<?php echo $order["id"]; ?>">View</a>
                                <a class="btn-link" href="order_invoice.php?order_id=<?php echo $order["id"]; ?>" target="_blank">Invoice</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<script src="../js/task4_script.js"></script>
</body>
</html>
<?php $mydb->closeConn($conn); ?>

