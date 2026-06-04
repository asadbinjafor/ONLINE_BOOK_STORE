<?php
define("ROOT_DIR", dirname(__DIR__));
define("PROFILE_UPLOAD_DIR", ROOT_DIR . "/uploads/profile/");
define("PROFILE_UPLOAD_WEB", "../uploads/profile/");
define("BOOK_UPLOAD_DIR", ROOT_DIR . "/uploads/books/");
define("BOOK_UPLOAD_WEB", "../uploads/books/");
define("REMEMBER_SECRET", "project06_bookstore_remember_me");

function ensureUploadDir($dir)
{
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
