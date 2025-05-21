<?php
session_start();
require_once 'config/database.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch all messages
$stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Contact - Margo Collection</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-image: url('images/jjj.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* Header and Navigation */
        .header {
            background-color: transparent;
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

        .nav-menu a:hover,
        .nav-menu a.active {
            color: #FF6B95;
        }

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

        .nav-icons a:hover {
            color: #FF6B95;
        }

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

        .messages-container {
            max-width: 800px;
            margin: 40px auto;
            background: rgba(255,255,255,0.9);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.07);
        }
        .message-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
         .message-info {
            display: flex;
            gap: 20px;
        }
        .message-info span {
            color: #666;
            font-size: 0.9em;
        }
        .message-content {
            margin: 15px 0;
            line-height: 1.6;
        }
        /* Remove reply specific styles if any needed */

    </style>
</head>
<body>
    <header class="header">
        <div class="search-container">
            <div class="search-bar">
                <input type="text" placeholder="Search...">
            </div>
        </div>
        <div class="nav-container">
            <div class="nav-content">
                <nav class="nav-menu">
                <a href="admin_products.php">Products</a>
                <a href="admin_customize.php">Customize</a>
                <a href="admin_order.php">Orders</a>
                <a href="admin_history.php">Order History</a>
                <a href="admin_contact.php"  class="active">Contact Messages</a>
                </nav>
                <div class="nav-icons">
                    <!-- Keep icons or modify as needed for admin -->
                    
                    <a href="admin_account.php" class="profile-icon">👤</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="messages-container">
            <h2 style="color: #FF6B95; margin-bottom: 20px; text-align: center;">Contact Messages</h2>
            <?php foreach ($messages as $message): ?>
                <div class="message-card">
                    <div class="message-header">
                         <div class="message-info">
                            <span><strong>From:</strong> <?php echo htmlspecialchars($message['name']); ?></span>
                            <span><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></span>
                            <span><strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></span>
                        </div>
                    </div>

                    <div class="message-content">
                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Margo Collection. All rights reserved.</p>
    </footer>

    <!-- Include any necessary scripts here -->

</body>
</html>