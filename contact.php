<?php
require_once 'config/database.php';
$submitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;
    // Store the message in the database
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Margo Collection</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body {
            background-image: url('images/jjj.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .nav-container {
            background-color: white;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }
        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            position: relative;
        }
        .nav-menu {
            display: flex;
            gap: 50px;
            align-items: center;
            margin: 0 auto;
            padding: 0 20px;
        }
        .nav-menu a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
            font-size: 14px;
        }
        .nav-menu a:hover, .nav-menu a.active { color: #FF6B95; }
        .nav-icons {
            position: absolute;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .nav-icons a {
            text-decoration: none;
            color: #333;
            font-size: 20px;
            display: flex;
            align-items: center;
            transition: color 0.3s;
        }
        .nav-icons a:hover { color: #FF6B95; }
        .nav-icons .cart-icon,
        .nav-icons .profile-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #FFE1E9;
            transition: background-color 0.3s;
        }
        .nav-icons .cart-icon:hover,
        .nav-icons .profile-icon:hover {
            background-color: #FF6B95;
            color: white;
        }
        .contact-container {
            max-width: 500px;
            margin: 80px auto 0 auto;
            background: rgba(255,255,255,0.7);
            border-radius: 20px;
            padding: 40px 30px 30px 30px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.07);
            text-align: center;
        }
        .contact-title {
            font-size: 2rem;
            font-weight: 600;
            color: #FF6B95;
            margin-bottom: 30px;
        }
        .contact-form input, .contact-form textarea {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 15px;
            background: #fff;
            resize: none;
        }
        .contact-form button {
            background-color: #FF6B95;
            color: white;
            padding: 12px 36px;
            border-radius: 22px;
            border: none;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
            cursor: pointer;
        }
        .contact-form button:hover {
            background-color: #FF4777;
        }
        .thankyou-box {
            background: #f9b6c6;
            border-radius: 18px;
            padding: 40px 20px 30px 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .thankyou-box .icon {
            font-size: 3rem;
            color: #fff;
            background: #FF6B95;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
        }
        .thankyou-box .message {
            font-size: 1.2rem;
            color: #fff;
            margin-bottom: 10px;
        }
        .back-btn {
            background-color: #fff;
            color: #FF6B95;
            border: 2px solid #FF6B95;
            border-radius: 22px;
            padding: 12px 36px;
            margin-top: 10px;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s, color 0.3s;
        }
        .back-btn:hover {
            background: #FF6B95;
            color: #fff;
        }
        .search-container {
            padding: 20px;
            display: flex;
            justify-content: flex-start;
            max-width: 1200px;
            margin: 0 auto;
        }
        .search-bar {
            position: relative;
            width: 300px;
        }
        .search-bar input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 25px;
            background-color: white;
            outline: none;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>
    <div class="search-container">
        <div class="search-bar">
            <input type="text" placeholder="Search...">
        </div>
    </div>
    <div class="nav-container">
        <div class="nav-content">
            <nav class="nav-menu">
                <a href="products.php">Products</a>
                <a href="customize.php">Customize</a>
                <a href="user_pending_orders.php">Pending Orders</a>
                <a href="contact.php" class="active">Contact</a>
                <a href="about.php">About</a>
            </nav>
            <div class="nav-icons">
                <a href="cart.php" class="cart-icon">🛒</a>
                <a href="account.php" class="profile-icon">👤</a>
            </div>
        </div>
    </div>
    <div class="contact-container">
        <?php if ($submitted): ?>
            <div class="thankyou-box">
                <div class="icon">✉️</div>
                <div class="message">Thank you for contacting us! ✔️</div>
            </div>
            <a href="products.php" class="back-btn">Back</a>
        <?php else: ?>
            <div class="contact-title">Contact Us</div>
            <form class="contact-form" method="post">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
                <button type="submit">Send Message</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html> 