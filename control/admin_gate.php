<?php
function requireAdmin()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
        header("Location: ../view/login.php");
        exit();
    }
}

function requireAdminApi()
{
    header("Content-Type: application/json");
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
        echo json_encode(array("success" => false, "message" => "Admin access required"));
        exit();
    }
}
