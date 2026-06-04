<?php
function requireCustomer()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
        header("Location: ../view/login.php");
        exit();
    }
}

function requireCustomerApi()
{
    header("Content-Type: application/json");
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
        echo json_encode(array("success" => false, "message" => "Login required"));
        exit();
    }
}

function requireLoginApi()
{
    header("Content-Type: application/json");
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["user_id"])) {
        echo json_encode(array("success" => false, "message" => "Login required"));
        exit();
    }
}
