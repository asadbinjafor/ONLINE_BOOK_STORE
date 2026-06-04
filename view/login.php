<?php include '../control/login_process.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BookHub</title>
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
    <div class="auth-box">
        <h2>Login to BookHub</h2>
        <?php if (isset($_GET["registered"])) { ?>
            <div class="msg-success">Registration successful. Please login.</div>
        <?php } ?>
        <?php if ($errorMsg !== "") { ?>
            <div class="msg-error"><?php echo htmlspecialchars($errorMsg); ?></div>
        <?php } ?>
        <form method="post" onsubmit="return validateLogin()">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="remember" value="1"> Remember Me (7 days)</label>
            </div>
            <div class="form-group">
                <input type="submit" name="login" value="Login">
            </div>
        </form>
        <div class="auth-footer">No account? <a href="Registration.php">Register</a></div>
        <p style="margin-top:12px;font-size:13px;color:#666;">Demo admin: admin@bookstore.com / Admin@12345</p>
    </div>
</div>
<script>
function validateLogin() {
    if (document.getElementById("email").value.trim() === "") { alert("Email required"); return false; }
    if (document.getElementById("password").value === "") { alert("Password required"); return false; }
    return true;
}
</script>
</body>
</html>
