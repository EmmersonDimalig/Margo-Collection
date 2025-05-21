<?php
session_start();

require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Basic admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch orders from the database
// Joining with users table to get customer information and order_items for product details
try {
    $stmt = $pdo->query("SELECT 
                            o.id AS order_id,
                            o.created_at,
                            o.total_amount,
                            u.username AS customer_username,
                            o.payment_method,
                            o.status
                        FROM orders o
                        JOIN users u ON o.user_id = u.id
                        ORDER BY o.created_at DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch order items for each order
    foreach ($orders as &$order) {
        $stmt_items = $pdo->prepare("SELECT 
                                        oi.product_name,
                                        oi.quantity,
                                        oi.product_price
                                    FROM order_items oi
                                    WHERE oi.order_id = ?");
        $stmt_items->execute([$order['order_id']]);
        $order['items'] = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($order); // Break the reference with the last element

} catch (Exception $e) {
    // Log or display error appropriately
    echo "Error fetching orders: " . $e->getMessage();
    $orders = []; // Ensure $orders is an empty array on error
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Order History - Margo Collection</title>
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

        /* Container for the main content */
        .container {
            padding: 30px;
            border-radius: 15px;
            margin: 40px auto; /* Center the container and add vertical space */
            max-width: 900px; /* Match the max-width of the orders table for consistency */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
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
            border-collapse: collapse; /* Use collapsed borders for grid */
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden; /* Ensures rounded corners apply to children */
        }

        .orders-table th,
        .orders-table td {
            padding: 12px 15px; /* More padding */
            text-align: left;
            border: 1px solid rgba(0, 0, 0, 0.2); /* Lighter border for dark theme */
        }

        .orders-table th {
            background-color: rgba(91, 34, 73, 2.0); 
            color: white;
            font-weight: 600;
            text-transform: uppercase;  
            font-size: 0.9em;
        }

        .orders-table tbody tr {
            /* Background set by alternating rows */
            transition: background-color 0.3s ease;
        }

        .orders-table tbody tr:hover {
            background-color: rgba(90, 80, 100, 0.9); /* Darker hover effect */
        }

        .orders-table tbody tr:nth-child(even) {
            background-color: rgba(132, 62, 110, 0.9); /* Alternating row background */
        }

        .orders-table tbody tr:nth-child(odd) {
            background-color: rgba(132, 62, 110, 0.9); /* Alternating row background */
        }

        .orders-table td {
            color: white; /* White text for table data */
            /* Border handled by the general td/th rule */
        }

        .orders-table tbody tr:last-child td {
            /* Border handled by the general td/th rule */
        }

        .orders-table th:first-child {
            /* Border radius handled by the table overflow hidden and border-collapse */
        }

        .orders-table th:last-child {
            /* Border radius handled by the table overflow hidden and border-collapse */
        }

        .order-items-cell {
            border: 1px solid rgba(255, 255, 255, 0.2); /* Border for order items cell */
        }

        /* Right align the Total Amount column */
        .orders-table th:nth-child(4),
        .orders-table td:nth-child(4) {
            text-align: right;
        }

        /* Style for the order items list */
        .orders-table .order-items-cell ul {
            list-style: none;
            padding: 0;
            margin: 5px 0 0 0;
        }

        .orders-table .order-items-cell li {
            margin-bottom: 5px;
            color: rgba(255, 255, 255, 0.8); /* Slightly muted white for list items */
        }

        .orders-table .order-items-cell strong {
            color: white; /* Ensure strong text is white */
        }

        .orders-table td .status-completed {
            color:rgb(48, 226, 90); /* Green color for completed status */
            font-weight: 600;
        }

        .orders-table td .status-pending {
            color: #ffc107; /* Yellow color for pending status */
            font-weight: 600;
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
                <a href="admin_products.php">Products</a>
                <a href="admin_customize.php">Customize</a>
                <a href="admin_order.php">Orders</a>
                <a href="admin_history.php" class="active">Order History</a>
                <a href="admin_contact.php">Contact Messages</a>
                </nav>
                <div class="nav-icons">
                    <a href="admin_account.php" class="profile-icon">👤</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <h2>Admin Order History</h2>
            
            <?php if (empty($orders)): ?>
                <p>No order history found.</p>
            <?php else: ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['order_id']) ?></td>
                                <td><?= htmlspecialchars($order['customer_username']) ?></td>
                                <td><?= htmlspecialchars($order['created_at']) ?></td>
                                <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                                <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                <td>
                                    <?php
                                        $status = htmlspecialchars($order['status']);
                                        $statusClass = '';
                                        if ($status === 'completed') {
                                            $statusClass = 'status-completed';
                                        } elseif ($status === 'pending') {
                                            $statusClass = 'status-pending';
                                        }
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= $status ?></span>
                                </td>
                            </tr>
                            <?php if (!empty($order['items'])): ?>
                                <tr>
                                    <td colspan="6" class="order-items-cell">
                                        <strong>Items:</strong>
                                        <ul>
                                            <?php foreach ($order['items'] as $item): ?>
                                                <li style="margin-bottom: 5px;">
                                                    <?= htmlspecialchars($item['product_name']) ?> (Qty: <?= htmlspecialchars($item['quantity']) ?>) - ₱<?= number_format($item['product_price'] * $item['quantity'], 2) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </main>

    <!-- Include your JavaScript files here -->
    
</body>
</html> 