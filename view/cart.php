<?php include '../control/cart_process.php'; include_once '../control/app.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - BookHub</title>
    <link rel="stylesheet" href="../css/task3_style.css">
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
            <span class="nav-user-info">Hi, <?php echo e($_SESSION["name"]); ?></span>
        </div>
    </div>
</nav>

<div class="page-header">
    <h1>Shopping Cart</h1>
    <p>Review your books before checkout</p>
</div>

<div class="main-container">

    <div id="cartMsg"></div>

    <?php if (count($cartRows) === 0) { ?>
        <div class="empty-cart">
            <h2>Your cart is empty</h2>
            <p>Browse books and add items to your cart</p>
            <a class="btn-primary" href="Home.php">Browse Books</a>
        </div>
    <?php } else { ?>
        <div class="cart-table-wrapper">
            <table class="cart-table" id="cartTable">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Author</th>
                        <th>Price (BDT)</th>
                        <th>Quantity</th>
                        <th>Subtotal (BDT)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartRows as $item) { ?>
                    <tr id="cart-row-<?php echo (int)$item["id"]; ?>">
                        <td><strong><?php echo e($item["title"]); ?></strong></td>
                        <td><?php echo e($item["author"]); ?></td>
                        <td><?php echo number_format((float)$item["price"], 2); ?></td>
                        <td>
                            <div class="qty-controls">
                                <button type="button" class="qty-btn"
                                        onclick="updateCart(<?php echo (int)$item['id']; ?>, <?php echo (int)$item['quantity'] - 1; ?>, <?php echo (int)$item['stock']; ?>)">−</button>
                                <span class="qty-value" id="qty-<?php echo (int)$item['id']; ?>"><?php echo (int)$item['quantity']; ?></span>
                                <button type="button" class="qty-btn"
                                        onclick="updateCart(<?php echo (int)$item['id']; ?>, <?php echo (int)$item['quantity'] + 1; ?>, <?php echo (int)$item['stock']; ?>)">+</button>
                            </div>
                            <small class="stock-info">Stock: <?php echo (int)$item["stock"]; ?></small>
                        </td>
                        <td id="subtotal-<?php echo (int)$item['id']; ?>"><?php echo number_format($item["subtotal"], 2); ?></td>
                        <td>
                            <button type="button" class="btn-remove" onclick="removeFromCart(<?php echo (int)$item['id']; ?>)">Remove</button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="cart-total-label">Total:</td>
                        <td colspan="2" class="cart-total-value" id="cartTotal">BDT <?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="cart-actions">
            <a class="btn-secondary" href="Home.php">Continue Shopping</a>
            <a class="btn-primary" href="checkout.php">Proceed to Checkout</a>
        </div>
    <?php } ?>

</div>

<script src="../js/task3_script.js"></script>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
