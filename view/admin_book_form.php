<?php include '../control/admin_book_form_process.php'; include_once '../control/app.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Book - Admin</title>
    <link rel="stylesheet" href="../css/task2_style.css">
</head>
<body>
<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="admin_dashboard.php">BookHub Admin</a>
        <div class="nav-links">
            <a href="admin_books.php">Back to Books</a>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="../control/logout_process.php">Logout</a>
        </div>
    </div>
</nav>
<div class="page-header">
    <h1><?php echo $isEdit ? 'Edit Book' : 'Add New Book'; ?></h1>
</div>
<div class="main-container">
    <div class="card" style="max-width:580px;">
    <form method="post" enctype="multipart/form-data" onsubmit="return validateBookForm()">
        <div class="form-group">
            <label>Title</label>
            <input type="text" id="title" name="title" value="<?php echo e($book["title"]); ?>">
            <span class="error"><?php echo $errors["title"] ?? ""; ?></span>
        </div>
        <div class="form-group">
            <label>Author</label>
            <input type="text" id="author" name="author" value="<?php echo e($book["author"]); ?>">
            <span class="error"><?php echo $errors["author"] ?? ""; ?></span>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description"><?php echo e($book["description"]); ?></textarea>
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="number" step="0.01" id="price" name="price" value="<?php echo e($book["price"]); ?>">
            <span class="error"><?php echo $errors["price"] ?? ""; ?></span>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select id="category_id" name="category_id">
                <option value="0">Select category</option>
                <?php while ($c = $categories->fetch_assoc()) { ?>
                <option value="<?php echo (int)$c["id"]; ?>" <?php if ((int)$book["category_id"] === (int)$c["id"]) echo 'selected'; ?>>
                    <?php echo e($c["name"]); ?>
                </option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label>Stock</label>
            <input type="number" id="stock" name="stock" value="<?php echo e($book["stock"]); ?>">
        </div>
        <div class="form-group">
            <label>Image (JPEG/PNG max 2MB)</label>
            <?php if (!empty($book["image_path"])) { ?>
                <img src="<?php echo e(BOOK_UPLOAD_WEB . $book["image_path"]); ?>" style="max-width:100px;display:block;">
            <?php } ?>
            <input type="file" name="image">
            <span class="error"><?php echo $errors["image"] ?? ""; ?></span>
        </div>
        <div class="form-group" style="display:flex; gap:10px;">
            <input type="submit" name="save_book" class="btn-primary" value="Save Book">
            <a class="btn-cancel" href="admin_books.php">Cancel</a>
        </div>
    </form>
    </div>
</div>
<script src="../js/task2_script.js"></script>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
