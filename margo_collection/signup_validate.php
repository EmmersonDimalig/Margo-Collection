<?php
session_start();
require 'includes/db.php';

// Google reCAPTCHA Secret Key (replace with your actual Secret Key)
define('RECAPTCHA_SECRET_KEY', '6LdZRD8rAAAAAK4mih_iXJdzSW5lkuHRD5ZtrVh1');

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $firstname = $_POST['firstname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Verify Google reCAPTCHA response
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $verify_data = [
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($verify_data)
        ]
    ];
    $context  = stream_context_create($options);
    $verify_result = file_get_contents($verify_url, false, $context);
    $captcha_success = json_decode($verify_result, true);

    if (!$captcha_success['success']) {
        // CAPTCHA verification failed
        $_SESSION['error'] = "Please complete the reCAPTCHA challenge.";
        header('Location: signup.php');
        exit();
    }

    if($password !== $confirm){
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: signup.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if($stmt->rowCount() > 0){
        $_SESSION['error'] = "Username already exists.";
        header("Location: signup.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (firstname, username, email, address, phone, password) VALUES (?,?,?,?,?,?)");

    if($stmt->execute([$firstname, $username, $email, $address, $phone, $hashedPassword])){
        $_SESSION['success'] = "Your account has been created. You can now login.";
        header("Location: login.php");
        exit();
    } else {
        echo ("There is an error");
        
        exit(); 
    }

}
