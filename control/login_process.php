<?php
include_once '../model/mydb.php';
include_once 'auth.php';
session_start();

$errorMsg = "";
$email = "";

if (isset($_COOKIE["remember_me"]) && !isset($_SESSION["user_id"])) {
    $mydb = new MyDB();
    $conn = $mydb->createConn();
    tryRememberLogin($mydb, $conn);
    $mydb->closeConn($conn);
    if (isset($_SESSION["user_id"])) {
        header("Location: ../view/Home.php");
        exit();
    }
}

if (isset($_POST["login"])) {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "") {
        $errorMsg = "Email is required";
    } elseif ($password === "") {
        $errorMsg = "Password is required";
    } else {
        $mydb = new MyDB();
        $conn = $mydb->createConn();
        $result = $mydb->getUserByEmail($email, $conn);
        $loginOk = false;

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user["password_hash"])) {
                setLoginSession($user);
                if (isset($_POST["remember"])) {
                    setRememberCookie($user["id"]);
                }
                $loginOk = true;
            }
        }
        $mydb->closeConn($conn);

        if ($loginOk) {
            header("Location: ../view/Home.php");
            exit();
        }
        $errorMsg = "Invalid email or password";
    }
}
