<?php
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'admin_gate.php';
include_once 'upload.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
requireAdmin();

$mydb = new MyDB();
$conn = $mydb->createConn();

$errors = array();
$successMsg = "";
$old = array("name" => "", "email" => "", "role" => "customer", "address" => "", "phone" => "");

if (isset($_GET["added"])) {
    $successMsg = "User account created successfully.";
}

if (isset($_POST["add_user"])) {
    $old["name"] = trim($_POST["name"] ?? "");
    $old["email"] = trim($_POST["email"] ?? "");
    $old["role"] = $_POST["role"] ?? "customer";
    $old["address"] = trim($_POST["address"] ?? "");
    $old["phone"] = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($old["name"] === "") {
        $errors["name"] = "Name is required";
    }
    if ($old["email"] === "" || !filter_var($old["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Valid email is required";
    }
    if (strlen($password) < 8) {
        $errors["password"] = "Password must be at least 8 characters";
    }
    if ($old["role"] !== "admin" && $old["role"] !== "customer") {
        $errors["role"] = "Select admin or customer role";
    }
    if ($old["address"] === "") {
        $errors["address"] = "Address is required";
    }
    if ($old["phone"] === "") {
        $errors["phone"] = "Phone is required";
    } elseif (!ctype_digit($old["phone"])) {
        $errors["phone"] = "Phone must contain digits only";
    }

    if (count($errors) === 0 && $mydb->emailExists($old["email"], $conn)->num_rows > 0) {
        $errors["email"] = "Email already exists";
    }

    $profilePicture = "";
    if (count($errors) === 0) {
        $profilePicture = uploadProfilePicture("profile_picture", "", $errors);
    }

    if (count($errors) === 0) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($mydb->createUser($old["name"], $old["email"], $hash, $old["role"], $old["address"], $old["phone"], $profilePicture, $conn)) {
            header("Location: ../view/admin_users.php?added=1");
            exit();
        }
        $errors["database"] = "Could not create user. Please try again.";
    }
}

$users = $mydb->getAllUsers($conn);
