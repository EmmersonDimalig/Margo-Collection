<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include 'includes/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if email exists in database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a 6-digit code
        date_default_timezone_set('Asia/Manila');
        $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $now->modify('+1 hour');
        $expires = $now->format('Y-m-d H:i:s');
        $verification_code = sprintf("%06d", mt_rand(1, 999999));

        // Store code and expiry in DB
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $stmt->execute([$verification_code, $expires, $email]);

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ranesjan@gmail.com'; // <-- your Gmail
            $mail->Password = 'djlxedawzsxcznke';    // <-- your App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('YOUR_GMAIL@gmail.com', 'Your App Name');
            $mail->addAddress($email, $user['username'] ?? '');

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification Code';
            $mail->Body = "Your verification code is: <strong>$verification_code</strong><br>This code will expire in 1 hour.";
            $mail->AltBody = "Your verification code is: $verification_code. This code will expire in 1 hour.";

            $mail->send();
            $_SESSION['success'] = "Verification code has been sent to your email.";
            $_SESSION['email'] = $email; // Store email for send-code.php
            header('Location: send-code.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['success'] = "If this email exists, a verification code has been sent.";
        header('Location: forgot-password.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
        .back-arrow {
            position: absolute;
            top: 24px;
            left: 24px;
            font-size: 1.5rem;
            color: #333;
            background: #fff;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.07);
            border: 1px solid #eee;
            transition: background 0.2s;
        }
        .back-arrow:hover {
            background: #FFE1E9;
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
        .forgot-form input[type="email"] {
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
        .success-message {
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
            .back-arrow {
                top: 10px;
                left: 10px;
            }
        }
    </style>
</head>
<body>
    <div style="position:relative;width:100vw;height:100vh;display:flex;align-items:center;justify-content:center;">
        <a href="login.php" class="back-arrow" title="Back">&#8592;</a>
        <div class="card">
            <div class="envelope">&#9993;</div>
            <div class="forgot-title">
                Enter your email to send a verification code to enter new password.
            </div>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <form class="forgot-form" method="POST" action="">
                <input type="email" name="email" placeholder="Enter your email:" required>
                <button type="submit">Send Code</button>
            </form>
        </div>
    </div>
</body>
</html>