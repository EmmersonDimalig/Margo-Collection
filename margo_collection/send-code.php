<?php
session_start();
require 'includes/db.php';

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['email'])) {
        $_SESSION['error'] = "No email session found; Please try again.";
        header('Location: forgot-password.php');
        exit();
    }

    $enteredCode = $_POST['code'];
    $email = $_SESSION['email'];

    $stmt = $pdo->prepare("SELECT reset_token, reset_expires FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $expires = new DateTime($user['reset_expires'], new DateTimeZone('Asia/Manila'));
        if ($enteredCode == $user['reset_token'] && $expires > $now) {
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code_verified'] = true;
            header('Location: new-password.php');
            exit();
        } else {
            $_SESSION['error'] = "Invalid or expired code. Please try again.";
        }
    } else {
        $_SESSION['error'] = "No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Verification Code</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/styless.css" />
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background-color: #FFE1E9;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1440 320\' preserveAspectRatio=\'none\'><path fill=\'none\' stroke=\'%23FFC0CB\' stroke-width=\'2\' d=\'M0,160 C320,200 420,100 640,160 C860,220 960,120 1280,160 L1440,160 L1440,320 L0,320 Z\'></path></svg>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            width: 400px;
            background-color: #F8A7A7;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .envelope {
            font-size: 2.5rem;
            color: #fff;
            background: #E75480;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            margin-top: 10px;
        }
        .forgot-title {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 24px;
            line-height: 1.5;
            text-align: center;
        }
        .forgot-form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .forgot-form input[type="text"] {
            background-color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 18px;
            width: 80%;
            font-size: 1rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .forgot-form button {
            background-color: #E75480;
            border: none;
            border-radius: 8px;
            padding: 10px;
            width: 50%;
            font-weight: 500;
            color: #fff;
            font-size: 1rem;
            margin-top: 10px;
            transition: background-color 0.3s;
            cursor: pointer;
        }
        .forgot-form button:hover {
            background-color: #D64771;
        }
        .success-message, .error-message {
            background: #fff3f6;
            color: #d75c6b;
            border-radius: 8px;
            padding: 8px 0;
            margin-bottom: 12px;
            font-size: 0.95rem;
            width: 80%;
            text-align: center;
        }
        @media (max-width: 500px) {
            .card {
                width: 95vw;
                padding: 18px 4vw 16px 4vw;
            }
        }
    </style>
</head>
<body>
    <div style="position:relative;width:100vw;height:100vh;display:flex;align-items:center;justify-content:center;">
        <div class="card">
            <div class="envelope">&#9993;</div>
            <div class="forgot-title">
                Enter the verification code sent to your email.
            </div>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <form class="forgot-form" method="POST" action="">
                <input type="text" name="code" placeholder="Enter verification code" required>
                <button type="submit">Submit</button>
            </form>
            <div style="margin-top:12px;text-align:center;width:100%;">
                <a href="forgot-password.php" style="color:#333;text-decoration:none;">&larr; Back to Forgot Password</a>
            </div>
        </div>
    </div>
</body>
</html>