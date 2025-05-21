<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Margo Collection - Login</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #FFE1E9;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none"><path fill="none" stroke="%23FFC0CB" stroke-width="2" d="M0,160 C320,200 420,100 640,160 C860,220 960,120 1280,160 L1440,160 L1440,320 L0,320 Z"></path></svg>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }

        .card {
            width: 400px;
            background-color: #F8A7A7;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .logo-text {
            text-align: center;
            font-family: serif;
            margin-bottom: 20px;
        }

        .logo-text h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 5px;
        }

        .logo-text p {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
        }

        .logo-img {
            width: 180px;
            height: 10px;
            
            display: block;
        }

        .form-control {
            background-color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
            width: 70%;
            margin-left: auto;
            margin-right: auto;
            display: block;
        }

        .btn-primary {
            background-color: #E75480;
            border: none;
            border-radius: 8px;
            padding: 10px;
            width: 50%;
            font-weight: 500;
            margin-top: 10px;
            transition: background-color 0.3s;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-primary:hover {
            background-color: #D64771;
        }

        .links {
            text-align: center;
            margin-top: 15px;
            font-size: 0.9rem;
        }

        .links a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s;
        }

        .links a:hover {
            color: #E75480;
        }

        .custom-error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="card">
        <div style="margin-bottom: 10px;">
            <a href="index.php" style="text-decoration:none;font-size:1.5rem;color:#000;">←</a>
        </div>
        <img src="images/lugo.png" alt="Margo Collection Logo" style="width: 180px; height: 180px; margin: 0 auto 20px; display: block;">
        
        <?php if (isset($_SESSION['error'])): ?>
            <p class="custom-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </p>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" role="alert">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="login_validate.php" method="POST">
            <input required class="form-control" type="text" placeholder="Username" name="username">
            <input required class="form-control" type="password" placeholder="Password" name="password">
            <div class="g-recaptcha" data-sitekey="6LdZRD8rAAAAADx8Rt8koCu-JNv7TthZTjE5gl_V" style="margin: 15px auto; display: block; width: 304px;"></div>
            <button class="btn btn-primary" type="submit">Log in</button>
        </form>

        <div class="links">
            <p><a href="signup.php">Don't have an account? </a></p>
            <p><a href="forgot-password.php">Forgot Password?</a></p>
        </div>
    </div>
</body>
</html>
