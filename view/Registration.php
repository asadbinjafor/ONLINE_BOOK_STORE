<?php include '../control/registration_process.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BookHub</title>
    <link rel="stylesheet" href="../css/task1_style.css">
</head>
<body>
<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="Home.php">BookHub</a>
        <div class="nav-links">
            <a href="login.php">Login</a>
            <a href="Registration.php">Register</a>
        </div>
    </div>
</nav>
<div class="auth-wrapper">
    <div class="auth-box" style="max-width:520px;">
        <h2>Create Account</h2>
        <?php if (isset($errors["database"])) { ?>
            <div class="msg-error"><?php echo htmlspecialchars($errors["database"]); ?></div>
        <?php } ?>
        <form method="post" enctype="multipart/form-data" onsubmit="return validateRegistration()">
            <div class="form-group">
                <label>Name</label>
                <input type="text" id="reg_name" name="name" value="<?php echo htmlspecialchars($old["name"]); ?>">
                <span class="error"><?php echo $errors["name"] ?? ""; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" id="reg_email" name="email" value="<?php echo htmlspecialchars($old["email"]); ?>">
                <span class="error"><?php echo $errors["email"] ?? ""; ?></span>
            </div>
            <div class="form-group">
                <label>Password (min 8)</label>
                <input type="password" id="reg_password" name="password">
                <span class="error"><?php echo $errors["password"] ?? ""; ?></span>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="customer" <?php if ($old["role"] === "customer") echo "selected"; ?>>Customer</option>
                    <option value="admin" <?php if ($old["role"] === "admin") echo "selected"; ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address"><?php echo htmlspecialchars($old["address"]); ?></textarea>
                <span class="error"><?php echo $errors["address"] ?? ""; ?></span>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($old["phone"]); ?>">
                <span class="error"><?php echo $errors["phone"] ?? ""; ?></span>
            </div>
            <div class="form-group">
                <label>Profile Picture (JPEG/PNG, max 2MB)</label>
                <input type="file" name="profile_picture">
            </div>
            <div class="form-group">
                <input type="submit" name="register" value="Register">
            </div>
        </form>
    </div>
</div>
<script src="../js/task1_script.js"></script>
</body>
</html>
