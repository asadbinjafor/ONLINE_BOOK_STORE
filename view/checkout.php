<?php
include '../control/checkout_process.php';
include_once '../control/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - BookHub</title>
    <link rel="stylesheet" type="text/css" href="../css/task3_style.css">
</head>
<body>

<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="Home.php">BookHub</a>
        <div class="nav-links">
            <a href="Home.php">Home</a>
            <a href="cart.php">Cart (<?php echo $cartCount; ?>)</a>
            <a href="customer_orders.php">My Orders</a>
            <a href="profile.php">Profile</a>
            <a href="../control/logout_process.php">Logout</a>
            <span class="nav-user-info">Hi, <?php echo htmlspecialchars($_SESSION["name"]); ?></span>
        </div>
    </div>
</nav>

<div class="page-header">
    <h1><?php echo $showInvoice ? "Order Invoice" : "Checkout"; ?></h1>
    <p><?php echo $showInvoice ? "Review your order before confirming" : "Enter your shipping address"; ?></p>
</div>

<div class="main-container">

    <?php if(!empty($errors["stock"])){ ?>
        <div class="msg-error"><?php echo htmlspecialchars($errors["stock"]); ?></div>
    <?php } ?>

    <?php if(!$showInvoice){ ?>

        <div class="card" style="max-width:580px; margin:0 auto;">
            <h2 class="section-title">Shipping Address</h2>

            <form action="" method="post" onsubmit="return validateCheckout()">
                <div class="form-group">
                    <label for="shipping_address">Delivery Address</label>
                    <textarea id="shipping_address" name="shipping_address" placeholder="Enter your full delivery address"><?php echo htmlspecialchars($shippingAddress); ?></textarea>
                    <span class="error"><?php echo $errors["shipping_address"] ?? ""; ?></span>
                </div>

                <div class="form-group" style="display:flex; gap:10px;">
                    <input type="submit" name="confirm_address" value="View Invoice">
                    <a class="btn-cancel" href="cart.php">Back to Cart</a>
                </div>
            </form>
        </div>

    <?php } else { ?>

        <div class="card invoice-card">
            <h2 class="section-title">Invoice</h2>

            <div class="invoice-info">
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($user["name"]); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user["email"]); ?></p>
                <p><strong>Shipping To:</strong> <?php echo htmlspecialchars($shippingAddress); ?></p>
            </div>

            <div class="cart-table-wrapper">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>book</th>
                            <th>Author</th>
                            <th>Qty</th>
                            <th>Unit Price (BDT)</th>
                            <th>Subtotal (BDT)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($itemsArray as $item){ ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item["title"]); ?></td>
                                <td><?php echo htmlspecialchars($item["author"]); ?></td>
                                <td><?php echo $item["quantity"]; ?></td>
                                <td><?php echo number_format($item["price"], 2); ?></td>
                                <td><?php echo number_format($item["subtotal"], 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="cart-total-label">Total Amount:</td>
                            <td class="cart-total-value">BDT <?php echo number_format($totalAmount, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="cart-actions">
                <a class="btn-cancel" href="cart.php">Cancel</a>
                <a class="btn-primary" href="payment.php">Confirm Purchase</a>
            </div>
        </div>

    <?php } ?>

</div>

<script src="../js/task3_script.js"></script>
</body>
</html>
<?php $mydb->closeConn($conn); ?>

