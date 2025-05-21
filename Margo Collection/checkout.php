<?php
session_start();
require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user data from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit();
}

$label = "Home";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Payment</title>
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
        .checkout-modal {
            display: flex;
            background: #fff;
            border-radius: 32px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            max-width: 900px;
            width: 90vw;
            min-height: 400px;
            overflow: hidden;
        }
        .checkout-left {
            flex: 2;
            padding: 48px 40px 48px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .checkout-title {
            font-size: 1.6rem;
            font-weight: 500;
            margin-bottom: 32px;
        }
        .payment-option {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            font-size: 1.1rem;
            cursor: pointer;
        }
        .payment-option input[type="radio"] {
            accent-color: #E75480;
            margin-right: 16px;
            width: 22px;
            height: 22px;
        }
        .checkout-right {
            flex: 1.2;
            background: #ffd6df;
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-top-right-radius: 32px;
            border-bottom-right-radius: 32px;
        }
        .profile-info {
            margin-bottom: 32px;
        }
        .profile-info p {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1rem;
        }
        .profile-info .label {
            font-weight: 500;
            color: #E75480;
        }
        .checkout-btn, .edit-btn {
            width: 100%;
            padding: 12px 0;
            border-radius: 12px;
            border: none;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 16px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .checkout-btn {
            background: #E75480;
            color: #fff;
        }
        .checkout-btn:hover {
            background: #D64771;
        }
        .edit-btn {
            background: #fff;
            color: #E75480;
            border: 2px solid #E75480;
        }
        .edit-btn:hover {
            background: #E75480;
            color: #fff;
        }
        @media (max-width: 900px) {
            .checkout-modal {
                flex-direction: column;
                border-radius: 24px;
            }
            .checkout-left, .checkout-right {
                border-radius: 0;
                padding: 32px 16px;
            }
            .checkout-right {
                border-top: 1px solid #f8a7a7;
            }
        }
    </style>
</head>
<body>
    <form class="checkout-modal" method="POST" action="process_payment.php">
        <div class="checkout-left">
            <div class="checkout-title">Payment Method</div>
            <label class="payment-option">
                <input type="radio" name="payment_method" value="cod" checked>
                Cash on Delivery
            </label>
            <label class="payment-option">
                <input type="radio" name="payment_method" value="gcash">
                Gcash Qr Payment
            </label>
        </div>
        <div class="checkout-right">
            <div class="profile-info">
                <div><span class="label">Name:</span> <?= htmlspecialchars($user['firstname']) ?></div>
                <div><span class="label">Phone Number:</span> <?= htmlspecialchars($user['phone'] ?? '09123456789') ?></div>
                <div><span class="label">Shipping Address:</span> <?= htmlspecialchars($user['address']) ?></div>
                <div><span class="label">Label:</span> <?= htmlspecialchars($label) ?></div>
            </div>
            <button type="submit" class="checkout-btn">Checkout</button>
            <button type="button" class="edit-btn" onclick="window.location.href='cart.php'">Edit order</button>
        </div>
    </form>
</body>
</html> 