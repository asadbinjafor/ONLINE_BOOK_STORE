<?php
include '../control/home_process.php';
include_once '../control/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - BookHub</title>
    <link rel="stylesheet" href="../css/task1_style.css">
    <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "customer") { ?>
    <link rel="stylesheet" href="../css/task3_style.css">
    <link rel="stylesheet" href="../css/task4_style.css">
    <?php } ?>
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
    <h1>Online Book Store</h1>
    <p>Browse books by category, author and title</p>
</div>

<div class="main-container">
    <div class="filters-bar">
        <div class="filter-item">
            <label for="searchText">Search</label>
            <input type="text" id="searchText" placeholder="Book title..." onkeyup="searchBooks()">
        </div>
        <div class="filter-item">
            <label for="authorFilter">Author</label>
            <select id="authorFilter" onchange="searchBooks()">
                <option value="">All Authors</option>
                <?php while ($a = $authors->fetch_assoc()) { ?>
                <option value="<?php echo e($a["author"]); ?>"><?php echo e($a["author"]); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="filter-item">
            <label for="genreFilter">Genre</label>
            <select id="genreFilter" onchange="searchBooks()">
                <option value="">All Genres</option>
                <?php
                $catsForFilter = $mydb->getCategories($conn);
                while ($cat = $catsForFilter->fetch_assoc()) {
                ?>
                <option value="<?php echo e($cat["name"]); ?>"><?php echo e($cat["name"]); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="filter-item">
            <label for="filterType">Filter By</label>
            <select id="filterType" onchange="searchBooks()">
                <option value="">All Fields</option>
                <option value="title">Title</option>
                <option value="author">Author</option>
                <option value="genre">Genre</option>
            </select>
        </div>
    </div>

    <h2 class="section-title">Categories</h2>
    <div class="category-list">
        <a class="category-link <?php if ($categoryId === 0) echo 'active'; ?>" href="Home.php">All</a>
        <?php while ($category = $categories->fetch_assoc()) { ?>
        <a class="category-link <?php if ($categoryId === (int)$category["id"]) echo 'active'; ?>"
           href="Home.php?category_id=<?php echo (int)$category["id"]; ?>">
            <?php echo e($category["name"]); ?>
        </a>
        <?php } ?>
    </div>

    <?php if ($categoryId === 0 && count($featuredBooks) > 0) { ?>
    <h2 class="section-title">Featured Books</h2>
    <div class="book-grid featured-grid">
        <?php foreach ($featuredBooks as $fb) { ?>
        <div class="book-card book-card-featured">
            <h3><a href="book_detail.php?id=<?php echo (int)$fb["id"]; ?>"><?php echo e($fb["title"]); ?></a></h3>
            <p><?php echo e($fb["author"]); ?></p>
            <p class="book-price">BDT <?php echo e($fb["price"]); ?></p>
            <a class="btn-link" href="book_detail.php?id=<?php echo (int)$fb["id"]; ?>">View Details</a>
        </div>
        <?php } ?>
    </div>
    <?php } ?>

    <?php if ($bookListCount > 0 || $categoryId > 0) { ?>
    <h2 class="section-title">Books</h2>
    <?php } ?>
    <div id="bookList" class="book-grid">
        <?php if ($bookListCount === 0 && $categoryId > 0) { ?>
            <div class="no-results">No books found in this category.</div>
        <?php } ?>
        <?php while ($book = $books->fetch_assoc()) { ?>
        <div class="book-card" data-book-id="<?php echo (int)$book["id"]; ?>">
            <?php if (!empty($book["image_path"])) { ?>
            <img class="book-thumb" src="<?php echo e(BOOK_UPLOAD_WEB . $book["image_path"]); ?>" alt="">
            <?php } ?>
            <h3><a href="book_detail.php?id=<?php echo (int)$book["id"]; ?>"><?php echo e($book["title"]); ?></a></h3>
            <p>Author: <?php echo e($book["author"]); ?></p>
            <p>Genre: <?php echo e($book["category_name"] ?? ""); ?></p>
            <p>Stock: <?php echo (int)$book["stock"]; ?></p>
            <p class="book-price">BDT <?php echo e($book["price"]); ?></p>
            <a class="btn-link" href="book_detail.php?id=<?php echo (int)$book["id"]; ?>">View Details</a>
            <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "customer" && (int)$book["stock"] > 0) { ?>
            <div class="add-cart-form">
                <input type="number" class="add-cart-qty" id="qty-input-<?php echo (int)$book["id"]; ?>"
                       value="1" min="1" max="<?php echo (int)$book["stock"]; ?>"
                       data-stock="<?php echo (int)$book["stock"]; ?>">
                <button type="button" class="btn-add-cart" onclick="addToCart(<?php echo (int)$book["id"]; ?>)">Add to Cart</button>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
    <?php if ($categoryId === 0 && $bookListCount === 0 && count($featuredBooks) > 0) { ?>
    <p class="browse-hint">Use search or pick a category to browse all books. Featured titles are shown above.</p>
    <?php } ?>
</div>

<script src="../js/task1_script.js"></script>
<?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "customer") { ?>
<script src="../js/task3_script.js"></script>
<?php } ?>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
