<?php
include '../control/book_detail_process.php';
include_once '../control/app.php';

$imageWeb = "";
if (!empty($book["image_path"])) {
    $imageWeb = BOOK_UPLOAD_WEB . $book["image_path"];
}
$isCustomer = isset($_SESSION["user_id"]) && $_SESSION["role"] === "customer";
$inStock = (int)$book["stock"] > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($book["title"]); ?> - BookHub</title>
    <link rel="stylesheet" href="../css/task1_style.css">
    <link rel="stylesheet" href="../css/task3_style.css">
    <link rel="stylesheet" href="../css/task4_style.css">
</head>
<body>
<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="Home.php">BookHub</a>
        <div class="nav-links">
            <a href="Home.php">Home</a>
            <?php if (isset($_SESSION["user_id"])) { ?>
                <?php if ($_SESSION["role"] === "customer") { ?>
                    <a href="cart.php">Cart (<span id="navCartCount"><?php echo $cartCount; ?></span>)</a>
                    <a href="customer_orders.php">My Orders</a>
                <?php } ?>
                <a href="profile.php">Profile</a>
                <?php if ($_SESSION["role"] === "admin") { ?>
                    <a href="admin_dashboard.php">Admin</a>
                <?php } ?>
                <a href="../control/logout_process.php">Logout</a>
                <span class="nav-user-info">Hi, <?php echo e($_SESSION["name"]); ?></span>
            <?php } else { ?>
                <a href="login.php">Login</a>
                <a href="Registration.php">Register</a>
            <?php } ?>
        </div>
    </div>
</nav>

<div class="page-header">
    <h1><?php echo e($book["title"]); ?></h1>
    <p>by <?php echo e($book["author"]); ?></p>
</div>

<div class="main-container">

    <div class="book-detail-card">
        <div class="book-detail-image">
            <?php if ($imageWeb !== "") { ?>
                <img src="<?php echo e($imageWeb); ?>" alt="<?php echo e($book["title"]); ?>">
            <?php } else { ?>
                <div class="book-no-image">
                    <span class="book-no-image-icon">BOOK</span>
                    <span>No cover image</span>
                </div>
            <?php } ?>
        </div>

        <div class="book-detail-info">
            <?php if (!empty($book["category_name"])) { ?>
                <span class="genre-badge"><?php echo e($book["category_name"]); ?></span>
            <?php } ?>

            <ul class="book-detail-meta">
                <li><span class="meta-label">Author</span><span class="meta-value"><?php echo e($book["author"]); ?></span></li>
                <li><span class="meta-label">Genre</span><span class="meta-value"><?php echo e($book["category_name"] ?? "—"); ?></span></li>
                <li><span class="meta-label">Price</span><span class="meta-value book-detail-price">BDT <?php echo number_format((float)$book["price"], 2); ?></span></li>
                <li><span class="meta-label">Stock</span>
                    <span class="meta-value <?php echo $inStock ? "stock-ok" : "text-out-of-stock"; ?>">
                        <?php echo $inStock ? (int)$book["stock"] . " copies available" : "Out of stock"; ?>
                    </span>
                </li>
            </ul>

            <div class="book-description-block">
                <h3>About this book</h3>
                <p class="book-description"><?php echo nl2br(e($book["description"] ?: "No description available.")); ?></p>
            </div>

            <?php if ($isCustomer && $inStock) { ?>
            <div class="add-cart-form book-detail-cart">
                <label for="detailQty">Quantity</label>
                <div class="book-detail-cart-row">
                    <input type="number" id="detailQty" class="add-cart-qty" value="1" min="1"
                           max="<?php echo (int)$book["stock"]; ?>"
                           data-stock="<?php echo (int)$book["stock"]; ?>">
                    <button type="button" class="btn-add-cart" onclick="addToCartFromDetail(<?php echo (int)$book["id"]; ?>)">Add to Cart</button>
                </div>
            </div>
            <?php } elseif ($isCustomer) { ?>
                <p class="text-out-of-stock">This book is currently out of stock.</p>
            <?php } elseif (!isset($_SESSION["user_id"])) { ?>
                <p class="login-hint"><a href="login.php">Login</a> as a customer to add this book to your cart.</p>
            <?php } ?>

            <div class="book-detail-back">
                <a class="btn-secondary" href="Home.php">&larr; Back to Home</a>
            </div>
        </div>
    </div>

</div>

<?php if ($isCustomer) { ?>
<script src="../js/task3_script.js"></script>
<?php } ?>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
