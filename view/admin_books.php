<?php include '../control/admin_books_process.php'; include_once '../control/app.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management - BookHub Admin</title>
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
            <span class="nav-user-info">Admin: <?php echo e($_SESSION["name"]); ?></span>
        </div>
    </div>
</nav>

<div class="page-header">
    <h1>Book Management</h1>
    <p>Add, edit and remove books from the store</p>
</div>

<div class="main-container">

    <?php if ($successMsg !== "") { ?>
        <div class="msg-success"><?php echo e($successMsg); ?></div>
    <?php } ?>
    <?php if ($errorMsg !== "") { ?>
        <div class="msg-error"><?php echo e($errorMsg); ?></div>
    <?php } ?>
    <?php if (isset($_GET["saved"])) { ?>
        <div class="msg-success">Book saved successfully.</div>
    <?php } ?>

    <div style="margin-bottom:20px;">
        <a class="btn-primary" href="admin_book_form.php">+ Add New Book</a>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Price (BDT)</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($books->num_rows === 0) { ?>
                <tr>
                    <td colspan="6" class="text-center">No books found. Add your first book.</td>
                </tr>
                <?php } else { ?>
                <?php while ($b = $books->fetch_assoc()) { ?>
                <tr>
                    <td><strong><?php echo e($b["title"]); ?></strong></td>
                    <td><?php echo e($b["author"]); ?></td>
                    <td><?php echo e($b["category_name"] ?? "—"); ?></td>
                    <td><?php echo number_format((float)$b["price"], 2); ?></td>
                    <td><?php echo (int)$b["stock"]; ?></td>
                    <td class="order-actions">
                        <a class="btn-action btn-edit-sm" href="admin_book_form.php?id=<?php echo (int)$b["id"]; ?>">Edit</a>
                        <a class="btn-action btn-reject-sm" href="admin_books.php?delete=<?php echo (int)$b["id"]; ?>"
                           onclick="return confirmDelete('book')">Delete</a>
                    </td>
                </tr>
                <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../js/task2_script.js"></script>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
