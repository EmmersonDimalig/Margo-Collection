<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
$status = 'pending';

// Fetch user info
$stmt = $pdo->prepare("SELECT address, phone FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$shipping_address = $user ? $user['address'] : '';
$phone = $user ? $user['phone'] : '';

// Get cart items
$stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$order_id = null;
$order_success = false;
$total_amount = 0;

// Calculate total amount
foreach ($cart_items as $item) {
    $total_amount += $item['product_price'] * $item['quantity'];
}

if ($payment_method && $shipping_address && $phone && !empty($cart_items)) {
    // Insert order, including total_amount
    $insert = $pdo->prepare("INSERT INTO orders (user_id, payment_method, shipping_address, phone, status, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
    if ($insert->execute([$user_id, $payment_method, $shipping_address, $phone, $status, $total_amount])) {
        $order_id = $pdo->lastInsertId();
        // Insert order items
        $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, product_price, quantity, image_url) VALUES (?, ?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $item_stmt->execute([
                $order_id,
                $item['product_name'],
                $item['product_price'],
                $item['quantity'],
                $item['image_url']
            ]);
        }
        // Clear cart
        $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?")->execute([$user_id]);
        $order_success = true;
    }
}

$method_text = '';
$show_qr = false;
switch ($payment_method) {
    case 'cod':
        $method_text = 'Cash on Delivery';
        break;
    case 'gcash':
        $method_text = 'Gcash QR Payment';
        $show_qr = true;
        break;
    default:
        $method_text = 'Unknown';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #ffe1e9;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .confirmation-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            padding: 48px 40px;
            max-width: 400px;
            width: 90vw;
            text-align: center;
        }
        .confirmation-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #E75480;
            margin-bottom: 24px;
        }
        .confirmation-detail {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 32px;
        }
        .qr-section {
            margin-bottom: 24px;
        }
        .qr-section img {
            width: 180px;
            height: 180px;
            object-fit: contain;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 10px;
        }
        .back-btn {
            background: #E75480;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        .back-btn:hover {
            background: #D64771;
        }
    </style>
</head>
<body>
    <div class="confirmation-card">
        <div class="confirmation-title">Thank you for your order!</div>
        <div class="confirmation-detail">
            <?php if ($order_success): ?>
                Your order has been placed.<br>
                Order ID: <strong><?= htmlspecialchars($order_id) ?></strong><br>
                Payment method: <strong><?= htmlspecialchars($payment_method) ?></strong>
                <?php if ($show_qr): ?>
                    <div class="qr-section">
                        <img src="images/qr.jpg" alt="Gcash QR Code">
                        <div style="color:#E75480;font-weight:500;">Scan this QR code to pay with Gcash</div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <span style="color:red;">There was a problem placing your order. Please try again.</span>
            <?php endif; ?>
        </div>
        <button class="back-btn" onclick="window.location.href='products.php'">Back to Home</button>
    </div>
</body>
</html> 