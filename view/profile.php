<?php
include '../control/profile_process.php';
include_once '../control/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - BookHub</title>
    <link rel="stylesheet" href="../css/task1_style.css">
    <?php if ($role === "customer") { ?>
    <link rel="stylesheet" href="../css/task3_style.css">
    <?php } ?>
    <link rel="stylesheet" href="../css/task4_style.css">
</head>
<body>
<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="Home.php">BookHub</a>
        <div class="nav-links">
            <a href="Home.php">Home</a>
            <?php if ($role === "customer") { ?>
                <a href="cart.php">Cart</a>
                <a href="customer_orders.php">My Orders</a>
            <?php } ?>
            <a href="profile.php">Profile</a>
            <?php if ($role === "admin") { ?>
                <a href="admin_dashboard.php">Admin Dashboard</a>
            <?php } ?>
            <a href="../control/logout_process.php">Logout</a>
            <span class="nav-user-info">Hi, <?php echo e($_SESSION["name"]); ?></span>
        </div>
    </div>
</nav>

<div class="page-header">
    <h1>My Profile</h1>
    <p>View and manage your account details</p>
</div>

<div class="main-container">
    <?php if (isset($_GET["updated"])) { ?>
        <div class="msg-success">Profile updated successfully.</div>
    <?php } ?>

    <div class="card">
        <div class="profile-layout">
            <?php if ($profilePicture !== "") { ?>
                <img class="profile-avatar" src="<?php echo e(PROFILE_UPLOAD_WEB . $profilePicture); ?>" alt="Profile">
            <?php } else { ?>
                <div class="profile-avatar-placeholder">&#9786;</div>
            <?php } ?>
            <div class="profile-details">
                <h2><?php echo e($name); ?></h2>
                <span class="role-badge role-<?php echo e($role); ?>"><?php echo ucfirst(e($role)); ?></span>
                <p>Email: <?php echo e($email); ?></p>
                <p>Phone: <?php echo e($phone); ?></p>
                <p>Address: <?php echo e($address); ?></p>
                <a class="btn-edit" href="editprofile.php">Edit Profile</a>
            </div>
        </div>
    </div>

    <?php if ($role === "customer" && $orders) { ?>
    <h2 class="section-title" style="margin-top:28px;">Purchase History</h2>
    <div class="cart-table-wrapper">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Total (BDT)</th>
                    <th>Status</th>
                    <th>Books</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $orderCount = 0;
                while ($ord = $orders->fetch_assoc()) {
                    $orderCount++;
                    $items = $mydb->getOrderItems($ord["id"], $conn);
                    $titles = array();
                    while ($it = $items->fetch_assoc()) {
                        $titles[] = $it["book_title"] . " x" . $it["quantity"];
                    }
                ?>
                <tr>
                    <td><strong>#<?php echo (int)$ord["id"]; ?></strong></td>
                    <td><?php echo date("d M Y", strtotime($ord["order_date"])); ?></td>
                    <td><?php echo number_format((float)$ord["total_amount"], 2); ?></td>
                    <td>
                        <span class="order-status-badge status-<?php echo e($ord["status"]); ?>">
                            <?php echo ucfirst(e($ord["status"])); ?>
                        </span>
                    </td>
                    <td><?php echo e(implode(", ", $titles)); ?></td>
                    <td class="order-actions">
                        <a class="btn-link" href="customer_order_detail.php?order_id=<?php echo (int)$ord["id"]; ?>">View</a>
                        <a class="btn-link" href="order_invoice.php?order_id=<?php echo (int)$ord["id"]; ?>" target="_blank">Invoice</a>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($orderCount === 0) { ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:16px;color:#6b7280;">No orders yet.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <p class="browse-hint" style="margin-top:14px;">
        <a href="customer_orders.php">View all orders with search &rarr;</a>
    </p>
    <?php } ?>
</div>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
