<?php
session_start();

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?> 