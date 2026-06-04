<?php
include_once 'auth.php';
session_start();
$_SESSION = array();
session_destroy();
clearRememberCookie();
header("Location: ../view/login.php");
exit();
