CREATE DATABASE IF NOT EXISTS bookstore;
USE bookstore;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    profile_picture VARCHAR(255),
    address TEXT NOT NULL,
    phone VARCHAR(30) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    image_path VARCHAR(255),
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id)
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','shipped','delivered') NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(50),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id)
);

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password_hash, role, address, phone, profile_picture)
SELECT 'Store Admin', 'admin@bookstore.com',
       '$2y$12$QMx7yjQrGfxwl2B.5Mt79OeAjEfxwmbLATaE7ePrT9Fz5kGp.WkwS',
       'admin', 'Dhaka, Bangladesh', '01700000000', ''
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@bookstore.com');

INSERT INTO categories (name)
SELECT 'Novel' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Novel');
INSERT INTO categories (name)
SELECT 'Literature' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Literature');
INSERT INTO categories (name)
SELECT 'Sci-Fi' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Sci-Fi');
INSERT INTO categories (name)
SELECT 'History' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'History');
INSERT INTO categories (name)
SELECT 'Children' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Children');

INSERT INTO books (title, author, description, price, category_id, stock)
SELECT 'The Great Gatsby', 'F. Scott Fitzgerald',
       'A classic American novel set in the Jazz Age.', 450.00, id, 25
FROM categories WHERE name = 'Literature'
AND NOT EXISTS (SELECT 1 FROM books WHERE title = 'The Great Gatsby');

INSERT INTO books (title, author, description, price, category_id, stock)
SELECT 'Dune', 'Frank Herbert',
       'Epic science fiction saga on the desert planet Arrakis.', 890.00, id, 18
FROM categories WHERE name = 'Sci-Fi'
AND NOT EXISTS (SELECT 1 FROM books WHERE title = 'Dune');

INSERT INTO books (title, author, description, price, category_id, stock)
SELECT 'To Kill a Mockingbird', 'Harper Lee',
       'A gripping tale of racial injustice and childhood innocence.', 520.00, id, 30
FROM categories WHERE name = 'Novel'
AND NOT EXISTS (SELECT 1 FROM books WHERE title = 'To Kill a Mockingbird');

INSERT INTO books (title, author, description, price, category_id, stock)
SELECT 'Sapiens', 'Yuval Noah Harari',
       'A brief history of humankind.', 750.00, id, 20
FROM categories WHERE name = 'History'
AND NOT EXISTS (SELECT 1 FROM books WHERE title = 'Sapiens');

INSERT INTO books (title, author, description, price, category_id, stock)
SELECT 'Charlotte''s Web', 'E.B. White',
       'A beloved children''s story of friendship and courage.', 320.00, id, 40
FROM categories WHERE name = 'Children'
AND NOT EXISTS (SELECT 1 FROM books WHERE title = 'Charlotte''s Web');
