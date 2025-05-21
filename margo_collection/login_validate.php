<?php

session_start();

require_once 'includes/db.php';

// Google reCAPTCHA Secret Key (replace with your actual Secret Key)
define('RECAPTCHA_SECRET_KEY', '6LdZRD8rAAAAAK4mih_iXJdzSW5lkuHRD5ZtrVh1');

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];

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
        header('Location: login.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user["password"])){
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // If there are items in the session cart, move them to the database
        if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach($_SESSION['cart'] as $product_id => $item) {
                $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_name, product_price, quantity, image_url) 
                                     VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $user['id'],
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $item['image']
                ]);
            }
            // Clear the session cart after moving items to database
            unset($_SESSION['cart']);
        }

        // Redirect based on user role
        if ($user['role'] === 'admin') {
            header('Location: admin_products.php');
        } else {
            header('Location: products.php');
        }
        exit();
    } else {
        $_SESSION['error'] = "Invalid username or password";
        header('Location: login.php');
        exit();
    }
}