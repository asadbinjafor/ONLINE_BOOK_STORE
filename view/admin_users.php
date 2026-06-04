<?php include '../control/admin_users_process.php'; include_once '../control/app.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users - BookHub Admin</title>
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
    <h1>All Registered Users</h1>
    <p>View users and add new admin or customer accounts</p>
</div>

<div class="main-container">

    <?php if ($successMsg !== "") { ?>
        <div class="msg-success"><?php echo e($successMsg); ?></div>
    <?php } ?>
    <?php if (isset($errors["database"])) { ?>
        <div class="msg-error"><?php echo e($errors["database"]); ?></div>
    <?php } ?>

    <div class="card" style="max-width:560px; margin-bottom:28px;">
        <h2 class="section-title">Add New User</h2>
        <form method="post" enctype="multipart/form-data" onsubmit="return validateAdminUserForm()">
            <div class="form-group">
                <label for="user_name">Full Name</label>
                <input type="text" id="user_name" name="name" value="<?php echo e($old["name"]); ?>">
                <span class="error"><?php echo $errors["name"] ?? ""; ?></span>
            </div>
            <div class="form-group">
                <label for="user_email">Email</label>
                <input type="email" id="user_email" name="email" value="<?php echo e($old["email"]); ?>">
                <span class="error"><?php echo $errors["email"] ?? ""; ?></span>
            </div>
            <div class="form-group">
                <label for="user_password">Password (min 8 characters)</label>
                <input type="password" id="user_password" name="password">
                <span class="error"><?php echo $errors["password"] ?? ""; ?></span>
            </div>
            <div class="form-group">
                <label for="user_role">Role</label>
                <select id="user_role" name="role">
                    <option value="customer" <?php if ($old["role"] === "customer") echo "selected"; ?>>Customer</option>
                    <option value="admin" <?php if ($old["role"] === "admin") echo "selected"; ?>>Admin</option>
                </select>
                <span class="error"><?php echo $errors["role"] ?? ""; ?></span>
            </div>
            <div class="form-group">
                <label for="user_address">Address</label>
                <textarea id="user_address" name="address"><?php echo e($old["address"]); ?></textarea>
                <span class="error"><?php echo $errors["address"] ?? ""; ?></span>
            </div>
            <div class="form-group">
                <label for="user_phone">Phone</label>
                <input type="text" id="user_phone" name="phone" value="<?php echo e($old["phone"]); ?>"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                <span class="error"><?php echo $errors["phone"] ?? ""; ?></span>
            </div>
            <div class="form-group">
                <label for="user_picture">Profile Picture (optional, JPEG/PNG, max 2MB)</label>
                <input type="file" id="user_picture" name="profile_picture">
                <span class="error"><?php echo $errors["profile_picture"] ?? ""; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" name="add_user" class="btn-primary" value="Add User">
            </div>
        </form>
    </div>

    <h2 class="section-title">User List</h2>
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users->num_rows === 0) { ?>
                <tr>
                    <td colspan="5" class="text-center">No users found.</td>
                </tr>
                <?php } else { ?>
                <?php while ($u = $users->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo (int)$u["id"]; ?></td>
                    <td><?php echo e($u["name"]); ?></td>
                    <td><?php echo e($u["email"]); ?></td>
                    <td><span class="role-badge role-<?php echo e($u["role"]); ?>"><?php echo ucfirst(e($u["role"])); ?></span></td>
                    <td><?php echo date("d M Y, h:i A", strtotime($u["created_at"])); ?></td>
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
