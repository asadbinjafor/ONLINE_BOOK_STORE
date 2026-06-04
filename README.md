# BookHub — Online Book Store

## Project Scenario Summary

**BookHub** is a web-based **Online Book Store** where customers can browse books by category, search with filters, add items to a cart, and complete purchases. Administrators manage the full book inventory, registered users, and order processing from a dedicated admin panel.

The system supports **two registered roles** and a **public (guest) browsing** experience:

| Role | Description |
|------|-------------|
| **Admin** | Main controller of the store: manages books (CRUD), removes customers, views all registered users (admin + customer), processes orders (pending → confirmed → shipped → delivered), and views complete purchase history with filters. |
| **Customer** | Registered shopper: browses and searches books, views book details, manages cart (add / update / remove via AJAX), checks out with payment method selection, and views purchase history in profile and on the My Orders page. |
| **Guest (not logged in)** | Can browse the home page, categories, featured books, and search books. Must **register or log in as a customer** to add items to the cart or place orders. |

**Typical workflow:** Customer browses books → adds to cart → confirms address on checkout → selects payment → order is created as **pending** → admin updates status (confirmed / shipped / delivered) → customer tracks the order in **My Orders** and profile purchase history.

This project was built for **Web Technologies — Project 06**, using the shared database schema and a **PHP MVC** architecture (`control/`, `model/`, `view/`) with security, validation, session-based auth, and AJAX/JSON endpoints as required by the assignment.

---

## Technologies & Topics Used

The project combines front-end presentation, server-side PHP logic, MySQL data storage, and common web security practices.

### Front-End

| Topic | How it is used in this project |
|-------|-------------------------------|
| **HTML5** | Page structure, navigation, forms, tables, book cards, admin panels, checkout and invoice layouts |
| **CSS3** | Layout (Flexbox, CSS Grid), responsive design (`@media`), component styling (cards, badges, cart table, admin tables, book detail page) across `task1_style.css`–`task4_style.css` |
| **JavaScript** | Client-side validation (registration, profile, book form, checkout); AJAX (`XMLHttpRequest`) for book search, cart operations, order search, admin order status, and customer delete |

### Back-End

| Topic | How it is used in this project |
|-------|-------------------------------|
| **PHP** | Server-side logic, routing via `index.php` and view includes, session management |
| **MVC pattern** | Separation into `control/` (controllers), `model/` (data access), `view/` (presentation) |
| **MySQLi** | Database connection with **prepared statements** on every query (SQL injection prevention) |
| **Sessions & cookies** | Login state (`$_SESSION['user_id']`, `role`, `name`); **Remember Me** cookie (7 days, HMAC-signed) |
| **File upload** | Profile pictures → `uploads/profile/`; book cover images → `uploads/books/` (JPEG/PNG, max 2 MB, MIME check) |
| **Password security** | `password_hash()` on register; `password_verify()` on login |

### Database

| Topic | How it is used in this project |
|-------|-------------------------------|
| **MySQL** | Relational database: **`bookstore`** |
| **Tables** | `users`, `categories`, `books`, `cart`, `orders`, `order_items`, `payments` |
| **Keys & integrity** | Foreign keys between books/categories, cart, orders, and order items; unique email on users |

### Other Web Topics

| Topic | How it is used in this project |
|-------|-------------------------------|
| **AJAX / JSON** | APIs return `Content-Type: application/json` for search, cart, orders, and admin actions |
| **XSS prevention** | `htmlspecialchars()` / helper `e()` when outputting user and database content |
| **Role-based access** | `admin_gate.php` and `customer_gate.php` restrict pages and API endpoints |
| **Responsive UI** | Mobile-friendly navigation, grids, and tables |
| **Apache (XAMPP)** | Local hosting; entry point `index.php` redirects to `view/Home.php` |

---

## Default User Credentials

After importing **`database_task1.sql`** (or `database.sql`), you can log in with this seeded admin account:

| Role | Display Name | Email | Password |
|------|--------------|-------|----------|
| **Admin** | Store Admin | `admin@bookstore.com` | `Admin@12345` |

**Note:** Only the admin account is seeded by default. **Customer accounts** are created through **Register** (`view/Registration.php`) or by an admin under **All Users** (`view/admin_users.php`). Registration allows choosing role **Admin** or **Customer** (as per assignment Task 1).

---

## How to Run the Project

1. Install **XAMPP** and start **Apache** and **MySQL**.
2. Copy the project folder to `htdocs`, e.g. `C:\xampp\htdocs\project6`.
3. Import **`database_task1.sql`** in phpMyAdmin (creates database `bookstore`, tables, sample categories, books, and admin user).
4. Confirm database settings in `model/database.php` if needed:
   - Host: `localhost`
   - User: `root`
   - Password: *(empty by default on XAMPP)*
   - Database: `bookstore`
5. Open: **http://localhost/project6/** or **http://localhost/project6/view/Home.php**
6. Log in as admin with the credentials above, or register a new customer account.

Ensure folders `uploads/profile/` and `uploads/books/` are writable for image uploads.

---

## Main Modules (Assignment Tasks)

| Task | Module | Main features |
|------|--------|----------------|
| **Task 1** | Auth & home | Register (admin/customer), login, remember me, profile view/edit, home with categories, featured books, category browsing |
| **Task 2** | Admin management | Book CRUD with image upload, remove customers (AJAX), all users list, add user (admin/customer), purchase history, order processing dashboard |
| **Task 3** | Customer browse & cart | AJAX book search (title/author/genre), book detail page, add/update/remove cart (JSON APIs), cart page |
| **Task 4** | Checkout & orders | Checkout (address + invoice), payment methods, order + payment records, order success, My Orders with AJAX filter, profile purchase history, admin order status updates |

---

## Project Folder Overview

```
project6/
├── index.php              → Entry point (redirects to Home)
├── database_task1.sql     → Database schema, seed data, default admin
├── database.sql           → Copy of schema (optional import)
├── control/               → Controllers (request handling, APIs, validation)
├── model/                 → Models (MyDB class, database config)
├── view/                  → Views (HTML/PHP pages)
├── css/                   → task1_style.css … task4_style.css
├── js/                    → task1_script.js … task4_script.js
└── uploads/
    ├── profile/           → User profile pictures
    └── books/             → Book cover images
```

### Key `control/` files (examples)

| Area | Files |
|------|--------|
| Auth | `login_process.php`, `registration_process.php`, `logout_process.php`, `auth.php` |
| Profile | `profile_process.php`, `editprofile_process.php` |
| Storefront | `home_process.php`, `books_search_api.php`, `book_detail_process.php` |
| Cart | `cart_process.php`, `cart_add_api.php`, `cart_update_api.php`, `cart_remove_api.php` |
| Checkout | `checkout_process.php`, `payment_process.php`, `order_success_process.php` |
| Customer orders | `customer_orders_process.php`, `orders_search_api.php`, `customer_order_reorder_api.php` |
| Admin | `admin_dashboard_process.php`, `admin_books_process.php`, `admin_users_process.php`, `admin_order_status_api.php`, … |

### Key `view/` pages (examples)

| Page | Purpose |
|------|---------|
| `Home.php` | Categories, featured books, search, book listing |
| `book_detail.php` | Single book details and add to cart |
| `cart.php`, `checkout.php`, `payment.php` | Shopping and checkout flow |
| `customer_orders.php`, `profile.php` | Purchase history |
| `admin_dashboard.php`, `admin_books.php`, `admin_orders.php` | Admin panels |

---

## Payment Methods (Customer Checkout)

- Credit Card  
- bKash  
- Nagad  
- Bank Transfer  
- Cash on Delivery  

---

## Order Status Flow

| Status | Meaning |
|--------|---------|
| `pending` | Order placed; waiting for admin |
| `confirmed` | Admin confirmed the order |
| `shipped` | Order shipped |
| `delivered` | Order delivered |

---

## Security Features (Summary)

- Prepared statements for all database queries  
- Passwords hashed with `password_hash()` — never stored as plain text  
- Output escaped with `htmlspecialchars()` to reduce XSS risk  
- Role-based access control for admin and customer areas  
- Server-side validation on every form before database writes  
- Client-side validation on key forms  
- File upload validation (MIME type and size)  
- Remember-me cookie signed with HMAC  

---

## Git Workflow (Assignment Requirement)

- Use a **`main`** branch and feature branches: `feature/taskX-studentID`  
- At least **3 meaningful commits per student** per task  
- Merge tasks into `main` via pull request with full history  

---

This README describes the **project scenario**, **technologies used**, **default login**, **folder structure**, and **how to run** the application for reviewers, instructors, and GitHub visitors.
