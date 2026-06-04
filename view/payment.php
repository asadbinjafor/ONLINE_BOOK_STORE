<?php
include '../control/payment_process.php';
include_once '../control/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - BookHub</title>
    <link rel="stylesheet" type="text/css" href="../css/task3_style.css">
</head>
<body>

<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="Home.php">BookHub</a>
        <div class="nav-links">
            <a href="Home.php">Home</a>
            <a href="cart.php">Cart</a>
            <a href="customer_orders.php">My Orders</a>
            <a href="profile.php">Profile</a>
            <a href="../control/logout_process.php">Logout</a>
            <span class="nav-user-info">Hi, <?php echo htmlspecialchars($_SESSION["name"]); ?></span>
        </div>
    </div>
</nav>

<div class="page-header">
    <h1>Select Payment Method</h1>
    <p>Choose how you would like to pay</p>
</div>

<div class="main-container">

    <?php if(!empty($errors["stock"])){ ?>
        <div class="msg-error"><?php echo htmlspecialchars($errors["stock"]); ?></div>
    <?php } ?>
    <?php if(!empty($errors["database"])){ ?>
        <div class="msg-error"><?php echo htmlspecialchars($errors["database"]); ?></div>
    <?php } ?>

    <div class="card" style="max-width:520px; margin:0 auto;">

        <div class="payment-summary">
            <h3>Order Summary</h3>
            <p><strong>Items:</strong> <?php echo count($itemsArray); ?> book(s)</p>
            <p><strong>Total Amount:</strong> BDT <?php echo number_format($totalAmount, 2); ?></p>
            <p><strong>Shipping To:</strong> <?php echo htmlspecialchars($_SESSION["shipping_address"]); ?></p>
        </div>

        <h2 class="section-title">Payment Method</h2>

        <form action="" method="post" onsubmit="return validatePayment()">
            <div class="payment-options">
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="Credit Card">
                    <span>Credit Card</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="bKash">
                    <span>bKash</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="Nagad">
                    <span>Nagad</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="Bank Transfer">
                    <span>Bank Transfer</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="Cash on Delivery">
                    <span>Cash on Delivery</span>
                </label>
            </div>
            <span class="error"><?php echo $errors["payment_method"] ?? ""; ?></span>

            <div class="form-group" style="display:flex; gap:10px; margin-top:20px;">
                <input type="submit" name="confirm_payment" value="Place Order">
                <a class="btn-cancel" href="checkout.php">Back</a>
            </div>
        </form>
    </div>

</div>

<script src="../js/task3_script.js"></script>
</body>
</html>
<?php $mydb->closeConn($conn); ?>

