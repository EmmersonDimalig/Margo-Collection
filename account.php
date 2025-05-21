<?php
session_start();
require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle name, address, and phone update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile'])) {
    $new_name = trim($_POST['name']);
    $new_address = trim($_POST['address']);
    $new_phone = trim($_POST['phone']);
    $stmt = $pdo->prepare("UPDATE users SET firstname = ?, address = ?, phone = ? WHERE id = ?");
    $stmt->execute([$new_name, $new_address, $new_phone, $_SESSION['user_id']]);
    // Refresh user data
    header('Location: account.php');
    exit();
}

// Fetch user data from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // If user not found, redirect to login
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Margo Collection</title>
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
            padding: 12px 20px;
            border: 1px solid #ddd;
            border-radius: 30px;
            background-color: white;
            outline: none;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .search-bar input:focus {
            border-color: #FF6B95;
            box-shadow: 0 2px 12px rgba(255, 107, 149, 0.2);
        }
        .nav-container {
            background-color: white;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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
            position: relative;
            padding: 5px 0;
        }
        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #FF6B95;
            transition: width 0.3s ease;
        }
        .nav-menu a:hover::after {
            width: 100%;
        }
        .nav-menu a:hover, .nav-menu a.active { 
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
            transition: all 0.3s ease;
        }
        .nav-icons a:hover { 
            transform: translateY(-2px);
            color: #FF6B95; 
        }
        .nav-icons .cart-icon,
        .nav-icons .profile-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: #FFE1E9;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .nav-icons .cart-icon:hover,
        .nav-icons .profile-icon:hover {
            background-color: #FF6B95;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 149, 0.3);
        }
        .profile-container {
            max-width: 800px;
            margin: 50px auto;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 30px;
            padding: 50px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .profile-pic {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #f9b6c6;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(255, 107, 149, 0.2);
            transition: transform 0.3s ease;
        }
        .profile-pic:hover {
            transform: scale(1.05);
        }
        .profile-name-display {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        .profile-email {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }
        .profile-address {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }
        .profile-phone {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }
        .edit-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: center;
            margin-bottom: 20px;
        }
        .edit-form input[type="text"] {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
            width: 250px;
        }
        .edit-form button {
            background-color: #FF6B95;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        .edit-form button:hover {
            background-color: #FF4777;
        }
        .edit-cancel {
            background: transparent;
            color: #FF6B95;
            border: none;
            margin-left: 10px;
            cursor: pointer;
            font-size: 1rem;
        }
        .profile-actions {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 300px;
            margin: 0 auto;
        }
        .edit-btn, .logout-btn {
            padding: 14px 30px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
        }
        .edit-btn {
            background-color: #FF6B95;
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(255, 107, 149, 0.3);
        }
        .edit-btn:hover {
            background-color: #FF4777;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 149, 0.4);
        }
        .logout-btn {
            background-color: transparent;
            color: #FF6B95;
            border: 2px solid #FF6B95;
        }
        .logout-btn:hover {
            background-color: #FF6B95;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 149, 0.3);
        }
    </style>
    <script>
        function showEditForm() {
            document.getElementById('profile-display').style.display = 'none';
            document.getElementById('edit-form').style.display = 'flex';
        }
        function hideEditForm() {
            document.getElementById('profile-display').style.display = 'block';
            document.getElementById('edit-form').style.display = 'none';
        }
    </script>
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
                    <a href="contact.php">Contact</a>
                    <a href="about.php">About</a>
            </nav>
            <div class="nav-icons">
                <a href="cart.php" class="cart-icon">🛒</a>
                <a href="account.php" class="profile-icon">👤</a>
            </div>
        </div>
    </div>
    <div class="profile-container">
        <div class="profile-header">
            <img src="images/log1.png" alt="Profile Picture" class="profile-pic">
            <div id="profile-display">
                <h1 class="profile-name-display"><?= htmlspecialchars($user['firstname']) ?></h1>
                <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
                <div class="profile-address">
                    <?= htmlspecialchars($user['address']) ?>
                </div>
                <div class="profile-phone">
                    <?= htmlspecialchars($user['phone']) ?>
                </div>
            </div>
            <form id="edit-form" class="edit-form" method="POST" action="" style="display:none;">
                <input type="text" name="name" value="<?= htmlspecialchars($user['firstname']) ?>" required placeholder="Name">
                <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" required placeholder="Address">
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required placeholder="Phone Number">
                <div>
                    <button type="submit" name="edit_profile">Save</button>
                    <button type="button" class="edit-cancel" onclick="hideEditForm()">Cancel</button>
                </div>
            </form>
        </div>
        <div class="profile-actions">
            <button class="edit-btn" onclick="showEditForm()">Edit Profile</button>
            <a href="logout.php" class="logout-btn" onclick="return confirmLogout()">Log Out</a>
        </div>
    </div>
    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }
    </script>
</body>
</html> 