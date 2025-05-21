<?php
session_start();
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch orders for the logged-in user
// This assumes an 'orders' table with a 'user_id' column.
try {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $user_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Optional: print_r($user_orders);
    
} catch (Exception $e) {
    // Log or display error appropriately
    echo "Error fetching orders: " . $e->getMessage();
    $user_orders = []; // Ensure $user_orders is an empty array on error
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Orders - Margo Collection</title>
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

        .login-btn {
            background-color: #FF6B95;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #FF4777;
        }

        /* Products Grid */
        .products-container {
            max-width: 900px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 50px;
            margin-top: 40px;
        }

        .product-card {
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            max-width: 280px;
            margin: 0 auto;
            padding-bottom: 15px;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: auto;
            display: block;
        }

        .product-info {
            padding: 15px;
            text-align: center;
        }

        .product-title {
            font-size: 1rem;
            color: #333;
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 1rem;
            color: #FF6B95;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .checkout-btn {
            background-color: #FF6B95;
            color: white;
            padding: 6px 20px;
            border-radius: 15px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            display: inline-block;
            margin-top: 8px;
        }

        .checkout-btn:hover {
            background-color: #FF4777;
        }

        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(1, 1fr);
            }
            
            .nav-menu {
                display: none;
            }
        }

        /* Cart Overlay Styles */
        .cart-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .cart-content {
            background-color: white;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 800px;
            position: relative;
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .cart-header h2 {
            color: #333;
            margin: 0;
        }

        .cart-items {
            margin-bottom: 20px;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 100px 2fr 1fr 1fr 1fr;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }

        .cart-total {
            background-color: #FFE1E9;
            padding: 20px;
            border-radius: 15px;
            text-align: right;
            margin-top: 20px;
        }

        .cart-total p {
            margin: 5px 0;
            font-size: 0.9rem;
            color: #666;
        }

        .cart-total .total {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        .cart-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }

        .cart-btn {
            padding: 10px 20px;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .proceed-btn {
            background-color: #FF6B95;
            color: white;
        }

        .edit-btn {
            background-color: #eee;
            color: #333;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #666;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #FF6B95;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateX(200%);
            transition: transform 0.3s ease-in-out;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification .check-icon {
            font-size: 20px;
        }

        .notification .message {
            font-size: 14px;
            font-weight: 500;
        }

        .orders-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px; /* Space between rows */
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden; /* Ensures rounded corners apply to children */
        }

        .orders-table th,
        .orders-table td {
            padding: 12px 15px; /* More padding */
            text-align: left;
            border: none; /* Remove default borders */
        }

        .orders-table th {
            background-color: #FF6B95; /* Pink header background */
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
        }

        .orders-table tbody tr {
            background-color: #fff;
            margin-bottom: 10px; /* Space between rows */
            border-radius: 8px; /* Rounded corners for rows */
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .orders-table tbody tr:hover {
            background-color: #ffe1e9; /* Light pink hover effect */
        }

        /* Style for the first and last cells in a row to maintain rounded corners */
        .orders-table tbody tr td:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .orders-table tbody tr td:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .orders-table td {
            color: #333;
            border-bottom: 1px solid #eee; /* Subtle separator */
        }

        .orders-table tbody tr:last-child td {
            border-bottom: none; /* No border on the last row */
        }

        .orders-table th:first-child {
            border-top-left-radius: 8px;
        }

        .orders-table th:last-child {
            border-top-right-radius: 8px;
        }

        /* Container for the main content */
        .container {
            background-color: rgba(255, 255, 255, 0.9); /* White background with slight transparency */
            padding: 30px;
            border-radius: 15px;
            margin: 40px auto; /* Center the container and add vertical space */
            max-width: 900px; /* Match the max-width of the orders table for consistency */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
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
                    <a href="products.php">Products</a>
                    <a href="customize.php">Customize</a>
                    <a href="user_pending_orders.php" class="active">Pending Orders</a>
                    <a href="contact.php">Contact</a>
                    <a href="about.php">About</a>
                </nav>
                <div class="nav-icons">
                    <a href="cart.php" class="cart-icon">🛒</a>
                    <a href="account.php" class="profile-icon">👤</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <h2>My Pending Orders</h2>
            
            <?php if (empty($user_orders)): ?>
                <p>You have no pending orders.</p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Order ID</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Date</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Total Amount</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Status</th>
                            <!-- Add more columns as needed (e.g., status) -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($user_orders as $order): ?>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><?= htmlspecialchars($order['id']) ?></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><?= htmlspecialchars($order['created_at']) ?></td>
                                <td style="border: 1px solid #ddd; padding: 8px;">₱<?= number_format($order['total_amount'], 2) ?></td>
                                <td style="border: 1px solid #ddd; padding: 8px;">
                                    <?php
                                        $status = htmlspecialchars($order['status']);
                                        $displayText = $status;
                                        $statusClass = '';

                                        if ($status === 'pending') {
                                            $displayText = 'Waiting for Approval';
                                            $statusClass = 'status-pending'; // Use the same class as admin for consistency
                                        } elseif ($status === 'accepted') {
                                            $displayText = 'Preparing order';
                                            // Add a class for styling Preparing order if needed
                                        } elseif ($status === 'ready_to_ship') {
                                            $displayText = 'Ready to be shipped';
                                            // Add a class for styling Ready to be shipped if needed
                                        } elseif ($status === 'shipping') {
                                            $displayText = 'Shipping';
                                            // Add a class for styling Shipping if needed
                                        } elseif ($status === 'completed') {
                                            $displayText = 'Completed';
                                            $statusClass = 'status-completed'; // Use the same class as admin for consistency
                                        }
                                        // Add more conditions for other statuses if needed
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= $displayText ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </main>

    <!-- Cart Overlay -->
    <div class="cart-overlay">
        <div class="cart-content">
            <button class="close-btn">&times;</button>
            <div class="cart-header">
                <h2>Shopping Cart</h2>
            </div>
            <div class="cart-items">
                <div class="cart-item">
                    <div>Product</div>
                    <div>Price</div>
                    <div>Qty</div>
                    <div>Total</div>
                </div>
            </div>
            <div class="cart-total">
                <p>Cart Total</p>
                <p class="shipping">Shipping fee included</p>
                <p class="total">₱0.00</p>
            </div>
            <div class="cart-buttons">
                <button class="cart-btn edit-btn">Edit order</button>
                <button class="cart-btn proceed-btn">Proceed to Payment</button>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div class="notification" id="notification">
        <span class="check-icon">✓</span>
        <span class="message">Item added to cart!</span>
    </div>

    <script>
        // Get all add to cart buttons
        const addToCartButtons = document.querySelectorAll('.checkout-btn');
        const cartOverlay = document.querySelector('.cart-overlay');
        const closeButton = document.querySelector('.close-btn');
        const cartItems = document.querySelector('.cart-items');
        const notification = document.getElementById('notification');

        // Function to show notification
        function showNotification(message = 'Item added to cart!') {
            notification.querySelector('.message').textContent = message;
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Add click event to all add to cart buttons
        addToCartButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const productName = button.getAttribute('data-name');
                const productPrice = button.getAttribute('data-price');
                const productImage = button.getAttribute('data-image');

                // Add to cart using AJAX
                fetch('cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `action=add&product_id=${encodeURIComponent(productName)}&price=${productPrice}&name=${encodeURIComponent(productName)}&image=${encodeURIComponent(productImage)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification();
                    } else if (data.error) {
                        showNotification('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error adding item to cart');
                });
            });
        });

        // Close overlay when clicking close button
        closeButton.addEventListener('click', () => {
            cartOverlay.style.display = 'none';
        });

        // Close overlay when clicking outside
        cartOverlay.addEventListener('click', (e) => {
            if (e.target === cartOverlay) {
                cartOverlay.style.display = 'none';
            }
        });
    </script>
</body>
</html> 