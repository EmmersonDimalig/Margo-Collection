<?php
session_start();

require 'includes/db.php';

if(!isset($_SESSION['email']) || !isset($_SESSION['reset_code_verified']) || !$_SESSION['reset_code_verified']){
    header('Location: enter_code.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if($newPassword === $confirmPassword){
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $_SESSION['reset_email']]);

        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_code_verified']);

        $_SESSION['success'] = 'Your password has been reset succesfully.';
        header('Location: login.php');
        exit();
    } else {
        $_SESSION['error'] = 'Passwords do not match. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Margo Collection</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="signup-page">
    <div class="signup-card">
        <div class="signup-back-button">
            <a href="login.php">←</a>
        </div>
        <h1 class="signup-title">Reset Password</h1>
        <p class="signup-subtitle">Create your new password</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="new-password.php" method="POST">
            <div class="signup-form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="signup-form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="signup-button">Reset Password</button>
            
            <p class="signup-login-link">Remember your password? <a href="login.php">Log in</a></p>
        </form>
    </div>
</body>
</html>