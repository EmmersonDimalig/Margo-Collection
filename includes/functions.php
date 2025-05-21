<?php
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
    return $_SESSION['user_id'];
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function debug_log($message) {
    error_log(print_r($message, true));
}
?> 