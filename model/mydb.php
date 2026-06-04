<?php
include_once __DIR__ . "/database.php";

class MyDB
{
    function createConn()
    {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Database connection failed");
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    }

    function closeConn($conn)
    {
        $conn->close();
    }

    function createUser($name, $email, $passwordHash, $role, $address, $phone, $profilePicture, $conn)
    {
        $sql = "INSERT INTO users (name, email, password_hash, role, address, phone, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $name, $email, $passwordHash, $role, $address, $phone, $profilePicture);
        return $stmt->execute();
    }

    function emailExists($email, $conn)
    {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    function emailExistsForOtherUser($email, $userId, $conn)
    {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id <> ?");
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getUserByEmail($email, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getUserById($id, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function updateProfile($id, $name, $email, $address, $phone, $profilePicture, $conn)
    {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, address = ?, phone = ?, profile_picture = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $email, $address, $phone, $profilePicture, $id);
        return $stmt->execute();
    }

    function updatePassword($id, $passwordHash, $conn)
    {
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->bind_param("si", $passwordHash, $id);
        return $stmt->execute();
    }

    function getCategories($conn)
    {
        return $conn->query("SELECT * FROM categories ORDER BY name");
    }

    function getAuthors($conn)
    {
        return $conn->query("SELECT DISTINCT author FROM books WHERE author <> '' ORDER BY author");
    }

    function getBooks($categoryId, $conn, $excludeIds = array())
    {
        $sql = "SELECT books.*, categories.name AS category_name
                FROM books
                LEFT JOIN categories ON books.category_id = categories.id
                WHERE (? = 0 OR books.category_id = ?)";
        $params = array($categoryId, $categoryId);
        $types = "ii";

        if (count($excludeIds) > 0) {
            $placeholders = implode(",", array_fill(0, count($excludeIds), "?"));
            $sql .= " AND books.id NOT IN ($placeholders)";
            foreach ($excludeIds as $id) {
                $params[] = (int)$id;
                $types .= "i";
            }
        }

        $sql .= " ORDER BY books.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getFeaturedBooks($limit, $conn)
    {
        $sql = "SELECT books.*, categories.name AS category_name
                FROM books
                LEFT JOIN categories ON books.category_id = categories.id
                ORDER BY books.created_at DESC
                LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    function searchBooks($q, $author, $genre, $filter, $conn)
    {
        $search = "%" . $q . "%";
        $sql = "SELECT books.id, books.title, books.author, books.price, books.description,
                       books.image_path, books.stock, categories.name AS category_name
                FROM books
                LEFT JOIN categories ON books.category_id = categories.id
                WHERE 1=1";
        $params = array();
        $types = "";

        if ($filter === "author" && $author !== "") {
            $sql .= " AND books.author = ?";
            $params[] = $author;
            $types .= "s";
        } elseif ($filter === "genre" && $genre !== "") {
            $sql .= " AND categories.name = ?";
            $params[] = $genre;
            $types .= "s";
        } elseif ($filter === "title" || $filter === "" || $filter === "name") {
            if ($q !== "") {
                $sql .= " AND books.title LIKE ?";
                $params[] = $search;
                $types .= "s";
            }
        } else {
            if ($q !== "") {
                $sql .= " AND (books.title LIKE ? OR books.author LIKE ? OR categories.name LIKE ?)";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
                $types .= "sss";
            }
            if ($author !== "") {
                $sql .= " AND books.author = ?";
                $params[] = $author;
                $types .= "s";
            }
            if ($genre !== "") {
                $sql .= " AND categories.name = ?";
                $params[] = $genre;
                $types .= "s";
            }
        }

        $sql .= " ORDER BY books.title";
        $stmt = $conn->prepare($sql);
        if ($types !== "") {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    function getBookById($id, $conn)
    {
        $stmt = $conn->prepare(
            "SELECT books.*, categories.name AS category_name
             FROM books LEFT JOIN categories ON books.category_id = categories.id
             WHERE books.id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getAllBooks($conn)
    {
        return $conn->query(
            "SELECT books.*, categories.name AS category_name
             FROM books LEFT JOIN categories ON books.category_id = categories.id
             ORDER BY books.title"
        );
    }

    function createBook($title, $author, $description, $price, $categoryId, $stock, $imagePath, $conn)
    {
        $stmt = $conn->prepare(
            "INSERT INTO books (title, author, description, price, category_id, stock, image_path)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssdiss", $title, $author, $description, $price, $categoryId, $stock, $imagePath);
        return $stmt->execute();
    }

    function updateBook($id, $title, $author, $description, $price, $categoryId, $stock, $imagePath, $conn)
    {
        $stmt = $conn->prepare(
            "UPDATE books SET title = ?, author = ?, description = ?, price = ?,
             category_id = ?, stock = ?, image_path = ? WHERE id = ?"
        );
        $stmt->bind_param("sssdissi", $title, $author, $description, $price, $categoryId, $stock, $imagePath, $id);
        return $stmt->execute();
    }

    function deleteBook($id, $conn)
    {
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    function bookInPendingOrder($bookId, $conn)
    {
        $stmt = $conn->prepare(
            "SELECT oi.id FROM order_items oi
             JOIN orders o ON oi.order_id = o.id
             WHERE oi.book_id = ? AND o.status = 'pending' LIMIT 1"
        );
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    function getDashboardCounts($conn)
    {
        $counts = array();
        $counts["books"] = (int)$conn->query("SELECT COUNT(*) AS cnt FROM books")->fetch_assoc()["cnt"];
        $counts["customers"] = (int)$conn->query("SELECT COUNT(*) AS cnt FROM users WHERE role = 'customer'")->fetch_assoc()["cnt"];
        $counts["orders"] = (int)$conn->query("SELECT COUNT(*) AS cnt FROM orders")->fetch_assoc()["cnt"];
        $row = $conn->query("SELECT COALESCE(SUM(total_amount),0) AS revenue FROM orders WHERE status IN ('confirmed','shipped','delivered')")->fetch_assoc();
        $counts["revenue"] = (float)$row["revenue"];
        return $counts;
    }

    function getAllCustomers($conn)
    {
        return $conn->query(
            "SELECT id, name, email, phone, address, created_at FROM users
             WHERE role = 'customer' ORDER BY created_at DESC"
        );
    }

    function getAllUsers($conn)
    {
        return $conn->query(
            "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC"
        );
    }

    function deleteUserCart($userId, $conn)
    {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    function deleteUserOrderItems($userId, $conn)
    {
        $stmt = $conn->prepare(
            "DELETE oi FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    function deleteUserPayments($userId, $conn)
    {
        $stmt = $conn->prepare(
            "DELETE p FROM payments p JOIN orders o ON p.order_id = o.id WHERE o.user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    function deleteUserOrders($userId, $conn)
    {
        $stmt = $conn->prepare("DELETE FROM orders WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    function deleteUser($userId, $conn)
    {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'customer'");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    function getAllOrders($statusFilter, $from, $to, $conn)
    {
        $sql = "SELECT orders.*, users.name AS customer_name, users.email AS customer_email,
                       users.address AS shipping_address, users.phone AS customer_phone
                FROM orders JOIN users ON orders.user_id = users.id WHERE 1=1";
        $params = array();
        $types = "";

        if ($statusFilter !== "" && in_array($statusFilter, array("pending", "confirmed", "shipped", "delivered"))) {
            $sql .= " AND orders.status = ?";
            $params[] = $statusFilter;
            $types .= "s";
        }
        if ($from !== "") {
            $sql .= " AND DATE(orders.order_date) >= ?";
            $params[] = $from;
            $types .= "s";
        }
        if ($to !== "") {
            $sql .= " AND DATE(orders.order_date) <= ?";
            $params[] = $to;
            $types .= "s";
        }
        $sql .= " ORDER BY orders.order_date DESC";

        $stmt = $conn->prepare($sql);
        if ($types !== "") {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    function getOrderItems($orderId, $conn)
    {
        $stmt = $conn->prepare(
            "SELECT order_items.*, books.title AS book_title, books.author
             FROM order_items JOIN books ON order_items.book_id = books.id
             WHERE order_items.order_id = ?"
        );
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function updateOrderStatus($orderId, $status, $conn)
    {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId);
        return $stmt->execute();
    }

    function addToCart($userId, $bookId, $quantity, $conn)
    {
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND book_id = ?");
        $stmt->bind_param("ii", $userId, $bookId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $newQty = $row["quantity"] + $quantity;
            $stmt2 = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt2->bind_param("ii", $newQty, $row["id"]);
            return $stmt2->execute();
        }

        $stmt2 = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, ?)");
        $stmt2->bind_param("iii", $userId, $bookId, $quantity);
        return $stmt2->execute();
    }

    function getCartItems($userId, $conn)
    {
        $stmt = $conn->prepare(
            "SELECT cart.id, cart.book_id, cart.quantity, books.title, books.author,
                    books.price, books.stock, books.image_path
             FROM cart JOIN books ON cart.book_id = books.id
             WHERE cart.user_id = ? ORDER BY cart.added_at DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getCartCount($userId, $conn)
    {
        $stmt = $conn->prepare("SELECT COALESCE(SUM(quantity), 0) AS total FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()["total"];
    }

    function updateCartQuantity($cartId, $userId, $quantity, $conn)
    {
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cartId, $userId);
        return $stmt->execute();
    }

    function removeCartItem($cartId, $userId, $conn)
    {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cartId, $userId);
        return $stmt->execute();
    }

    function clearCart($userId, $conn)
    {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    function getCartItemById($cartId, $userId, $conn)
    {
        $stmt = $conn->prepare(
            "SELECT cart.*, books.price, books.stock, books.title
             FROM cart JOIN books ON cart.book_id = books.id
             WHERE cart.id = ? AND cart.user_id = ?"
        );
        $stmt->bind_param("ii", $cartId, $userId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function createOrder($userId, $totalAmount, $paymentMethod, $conn)
    {
        $stmt = $conn->prepare(
            "INSERT INTO orders (user_id, total_amount, status, payment_method) VALUES (?, ?, 'pending', ?)"
        );
        $stmt->bind_param("ids", $userId, $totalAmount, $paymentMethod);
        if ($stmt->execute()) {
            return $conn->insert_id;
        }
        return false;
    }

    function createOrderItem($orderId, $bookId, $quantity, $unitPrice, $conn)
    {
        $stmt = $conn->prepare(
            "INSERT INTO order_items (order_id, book_id, quantity, unit_price) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("iiid", $orderId, $bookId, $quantity, $unitPrice);
        return $stmt->execute();
    }

    function createPayment($orderId, $amount, $paymentMethod, $transactionId, $conn)
    {
        $stmt = $conn->prepare(
            "INSERT INTO payments (order_id, amount, payment_method, transaction_id) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("idss", $orderId, $amount, $paymentMethod, $transactionId);
        return $stmt->execute();
    }

    function decreaseStock($bookId, $quantity, $conn)
    {
        $stmt = $conn->prepare(
            "UPDATE books SET stock = stock - ? WHERE id = ? AND stock >= ?"
        );
        $stmt->bind_param("iii", $quantity, $bookId, $quantity);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    function getCustomerOrders($userId, $conn)
    {
        $stmt = $conn->prepare(
            "SELECT id, total_amount, status, payment_method, order_date
             FROM orders WHERE user_id = ? ORDER BY order_date DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getOrderById($orderId, $conn)
    {
        $stmt = $conn->prepare(
            "SELECT orders.*, users.name AS customer_name, users.email AS customer_email
             FROM orders JOIN users ON orders.user_id = users.id WHERE orders.id = ?"
        );
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getOrderForUser($orderId, $userId, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $orderId, $userId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getOrderWithItems($orderId, $userId, $conn)
    {
        $stmt = $conn->prepare(
            "SELECT orders.*, users.name AS customer_name, users.email AS customer_email,
                    users.address AS shipping_address, users.phone AS customer_phone
             FROM orders
             JOIN users ON orders.user_id = users.id
             WHERE orders.id = ? AND orders.user_id = ?"
        );
        $stmt->bind_param("ii", $orderId, $userId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getPaymentByOrderId($orderId, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM payments WHERE order_id = ? LIMIT 1");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function searchCustomerOrders($userId, $status, $from, $to, $q, $conn)
    {
        $sql = "SELECT DISTINCT orders.id, orders.total_amount, orders.status,
                       orders.payment_method, orders.order_date
                FROM orders
                LEFT JOIN order_items oi ON orders.id = oi.order_id
                LEFT JOIN books b ON oi.book_id = b.id
                WHERE orders.user_id = ?";
        $params = array($userId);
        $types = "i";
        $allowed = array("pending", "confirmed", "shipped", "delivered");

        if ($status !== "" && in_array($status, $allowed, true)) {
            $sql .= " AND orders.status = ?";
            $params[] = $status;
            $types .= "s";
        }
        if ($from !== "") {
            $sql .= " AND DATE(orders.order_date) >= ?";
            $params[] = $from;
            $types .= "s";
        }
        if ($to !== "") {
            $sql .= " AND DATE(orders.order_date) <= ?";
            $params[] = $to;
            $types .= "s";
        }
        if ($q !== "") {
            $sql .= " AND b.title LIKE ?";
            $params[] = "%" . $q . "%";
            $types .= "s";
        }
        $sql .= " ORDER BY orders.order_date DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getCompletedOrders($conn)
    {
        return $conn->query(
            "SELECT orders.*, users.name AS customer_name, users.email AS customer_email,
                    users.phone AS customer_phone, users.address AS shipping_address
             FROM orders
             JOIN users ON orders.user_id = users.id
             WHERE orders.status IN ('confirmed','shipped','delivered')
             ORDER BY orders.order_date DESC"
        );
    }

    function increaseStock($bookId, $quantity, $conn)
    {
        $stmt = $conn->prepare("UPDATE books SET stock = stock + ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity, $bookId);
        return $stmt->execute();
    }

    function reorderItemsToCart($orderId, $userId, $conn)
    {
        $orderResult = $this->getOrderWithItems($orderId, $userId, $conn);
        if ($orderResult->num_rows === 0) {
            return array("success" => false, "message" => "Order not found");
        }
        $order = $orderResult->fetch_assoc();
        if (!in_array($order["status"], array("confirmed", "shipped", "delivered"), true)) {
            return array("success" => false, "message" => "Only processed orders can be reordered");
        }

        $warnings = array();
        $added = 0;
        $items = $this->getOrderItems($orderId, $conn);

        while ($item = $items->fetch_assoc()) {
            $bookResult = $this->getBookById($item["book_id"], $conn);
            if ($bookResult->num_rows === 0) {
                $warnings[] = $item["book_title"] . " is no longer available";
                continue;
            }
            $book = $bookResult->fetch_assoc();
            $qty = (int)$item["quantity"];
            $stock = (int)$book["stock"];

            if ($stock <= 0) {
                $warnings[] = $item["book_title"] . " is out of stock";
                continue;
            }
            if ($qty > $stock) {
                $warnings[] = $item["book_title"] . ": only " . $stock . " available";
                $qty = $stock;
            }
            if ($this->addToCart($userId, $item["book_id"], $qty, $conn)) {
                $added++;
            }
        }

        return array(
            "success" => $added > 0,
            "message" => $added > 0 ? "Added " . $added . " item(s) to cart" : "No items could be added",
            "warnings" => $warnings,
            "cart_count" => $this->getCartCount($userId, $conn)
        );
    }
}
