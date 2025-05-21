<?php
session_start();
require_once 'config/database.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle name, address, and phone update for the admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile'])) {
    // Note: Address and phone might not be relevant for an admin, adjust as needed
    $new_name = trim($_POST['name']);
    // $new_address = trim($_POST['address']); // Uncomment if needed
    // $new_phone = trim($_POST['phone']); // Uncomment if needed
    
    // Update only the username for admin account
    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->execute([$new_name, $_SESSION['user_id']]);

    // Refresh admin data in session (optional, but good practice)
    $_SESSION['username'] = $new_name;
    
    // Redirect back to the admin account page
    header('Location: admin_account.php');
    exit();
}

// Fetch admin user data from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // If admin user not found (shouldn't happen if logged in), redirect to login
    header('Location: login.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account - Margo Collection</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Include styles from your admin pages (like admin_contact.php) here */
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
            transition: all 0.3s ease;
        }

        .nav-icons a:hover {
            transform: translateY(-2px);
            color: #FF6B95;
        }

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

        .nav-icons .profile-icon:hover {
            background-color: #FF6B95;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 149, 0.3);
        }
         .container { /* Using a similar container class for the main content */
            max-width: 800px;
            margin: 40px auto;
            background: rgba(255,255,255,0.9);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.07);
        }

        /* Profile specific styles */
        .profile-container {
            max-width: 800px;
            margin: 50px auto;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 30px;
            padding: 50px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            text-align: center; /* Center profile content */
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
            margin-bottom: 30px; /* Added margin */
        }
         .profile-address {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }
        .profile-phone {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px; /* Added margin */
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
            text-align: center; /* Center text in input */
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
            align-items: center; /* Center buttons */
        }
         .profile-actions button {
            background-color: #FF6B95;
            color: white;
            padding: 12px 36px;
            border-radius: 22px;
            border: none;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
            cursor: pointer;
            width: 200px; /* Fixed width for consistency */
        }
        .profile-actions button:hover {
            background-color: #FF4777;
        }
        .logout-btn {
            background-color: #fff; /* White background */
            color: #FF6B95; /* Pink text */
            border: 2px solid #FF6B95; /* Pink border */
        }
        .logout-btn:hover {
            background: #FF6B95; /* Pink background on hover */
            color: #fff; /* White text on hover */
        }
        .hidden { display: none; }

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
                <a href="admin_contact.php">Contact Messages</a>
                </nav>
                <div class="nav-icons">
                    <a href="admin_account.php" class="profile-icon active">👤</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="profile-container">
            <div id="profile-display">
                <div class="profile-header">
                    <img src="images/log1.png" alt="Profile Picture" class="profile-pic">
                    <h2 class="profile-name-display"><?= htmlspecialchars($user['username']) ?></h2>
                    <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
                    <!-- Address and Phone might not be relevant for admin, uncomment if needed -->
                    <!-- <p class="profile-address">Address: <?= htmlspecialchars($user['address']) ?></p> -->
                    <!-- <p class="profile-phone">Phone: <?= htmlspecialchars($user['phone']) ?></p> -->
                </div>

                <div class="profile-actions">
                    <button id="edit-profile-btn">Edit Profile</button>
                    <button class="logout-btn" id="admin-logout-btn">Log Out</button>
                </div>
            </div>

            <div id="profile-edit" class="hidden">
                <div class="profile-header">
                     <img src="images/log1.png" alt="Profile Picture" class="profile-pic">
                    <h2 style="color: #FF6B95;">Edit Profile</h2>
                </div>
                <form class="edit-form" method="post">
                     <input type="hidden" name="edit_profile" value="1">
                    <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($user['username']) ?>" required>
                    <!-- Address and Phone might not be relevant for admin, uncomment if needed -->
                    <!-- <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($user['address']) ?>"> -->
                    <!-- <input type="text" name="phone" placeholder="Phone Number" value="<?= htmlspecialchars($user['phone']) ?>"> -->
                    <div>
                        <button type="submit">Save</button>
                        <button type="button" id="cancel-edit" class="edit-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p style="text-align: center;">&copy; 2025 Margo Collection. All rights reserved.</p>
    </footer>

    <script>
        const profileDisplay = document.getElementById('profile-display');
        const profileEdit = document.getElementById('profile-edit');
        const editProfileBtn = document.getElementById('edit-profile-btn');
        const cancelEditBtn = document.getElementById('cancel-edit');

        editProfileBtn.addEventListener('click', () => {
            profileDisplay.classList.add('hidden');
            profileEdit.classList.remove('hidden');
        });

        cancelEditBtn.addEventListener('click', () => {
            profileEdit.classList.add('hidden');
            profileDisplay.classList.remove('hidden');
        });

         // Basic logout - redirects to logout.php
         // The actual session destruction should happen in logout.php

        // Add confirmation for admin logout
        const adminLogoutBtn = document.getElementById('admin-logout-btn');
        adminLogoutBtn.addEventListener('click', () => {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'logout.php';
            }
        });

    </script>

</body>
</html> 