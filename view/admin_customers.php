<?php include '../control/admin_customers_process.php'; include_once '../control/app.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers - Admin</title>
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
            <a href="../control/logout_process.php">Logout</a>
        </div>
    </div>
</nav>
<div class="page-header"><h1>Customers</h1></div>
<div class="main-container">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php if ($customers->num_rows === 0) { ?>
                <tr><td colspan="5" class="text-center">No customers found.</td></tr>
                <?php } ?>
                <?php while ($c = $customers->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo e($c["name"]); ?></td>
                    <td><?php echo e($c["email"]); ?></td>
                    <td><?php echo e($c["phone"]); ?></td>
                    <td><?php echo date("d M Y, h:i A", strtotime($c["created_at"])); ?></td>
                    <td><button type="button" class="btn-remove" onclick="deleteCustomer(<?php echo (int)$c['id']; ?>, '<?php echo e($c['name']); ?>')">Delete</button></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script src="../js/task2_script.js"></script>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
