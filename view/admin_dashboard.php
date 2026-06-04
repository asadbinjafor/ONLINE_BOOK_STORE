<?php include '../control/admin_dashboard_process.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BookHub</title>
    <link rel="stylesheet" href="../css/task2_style.css">
</head>
<body>
<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="admin_dashboard.php">BookHub Admin</a>
        <div class="nav-links">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_books.php">Books</a>
            <a href="admin_customers.php">Customers</a>
            <a href="admin_users.php">All Users</a>
            <a href="admin_orders.php">Orders</a>
            <a href="admin_history.php">History</a>
            <a href="Home.php">Store</a>
            <a href="../control/logout_process.php">Logout</a>
        </div>
    </div>
</nav>
<div class="page-header"><h1>Admin Dashboard</h1><p>Manage books, customers and orders</p></div>
<div class="main-container">
    <div class="dashboard-stats">
        <a class="stat-card" href="admin_books.php"><div class="stat-number"><?php echo (int)$counts["books"]; ?></div><div class="stat-label">Total Books</div></a>
        <a class="stat-card" href="admin_customers.php"><div class="stat-number"><?php echo (int)$counts["customers"]; ?></div><div class="stat-label">Customers</div></a>
        <a class="stat-card" href="admin_orders.php"><div class="stat-number"><?php echo (int)$counts["orders"]; ?></div><div class="stat-label">Total Orders</div></a>
        <a class="stat-card"><div class="stat-number"><?php echo number_format($counts["revenue"], 0); ?></div><div class="stat-label">Revenue (BDT)</div></a>
    </div>
    <div class="quick-links">
        <a class="quick-link-btn" href="admin_book_form.php">Add New Book</a>
        <a class="quick-link-btn" href="admin_orders.php">Process Orders</a>
        <a class="quick-link-btn" href="admin_history.php">Purchase History</a>
    </div>
</div>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
