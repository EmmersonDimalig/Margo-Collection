<?php
session_start();
require_once 'config/database.php';

// Fetch products from the database
$stmt = $pdo->query("SELECT * FROM customize_products ORDER BY id ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customize Bouquet</title>
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
        .header {
            background-color: transparent;
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
        .customize-container {
            max-width: 1100px;
            margin: 70px auto 0 auto;
            padding: 0 20px 40px 20px;
        }
        .customize-title {
            text-align: center;
            margin-bottom: 50px;
            font-size: 2.2rem;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .customize-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 32px 32px;
            margin-bottom: 50px;
        }
        .product-card {
            background-color: white;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,0.10);
            width: 270px;
            min-width: 220px;
            margin: 0;
            padding-bottom: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
            border-top-left-radius: 22px;
            border-top-right-radius: 22px;
        }
        .product-info {
            padding: 20px 10px 0 10px;
            text-align: center;
            flex: 1;
        }
        .product-title {
            font-size: 1.1rem;
            color: #222;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .product-price {
            font-size: 1.1rem;
            color: #FF6B95;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .qty-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            margin-bottom: 10px;
        }
        .qty-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: #FFE1E9;
            color: #FF6B95;
            font-size: 1.3rem;
            font-weight: bold;
            transition: background 0.2s, color 0.2s;
        }
        .qty-btn:active {
            background: #FF6B95;
            color: #fff;
        }
        .add-cart-btn {
            background-color: #FF6B95;
            color: white;
            padding: 12px 36px;
            border-radius: 22px;
            border: none;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
            margin: 0 10px 0 0;
        }
        .add-cart-btn:hover {
            background-color: #FF4777;
        }
        .back-btn {
            background-color: #fff;
            color: #FF6B95;
            border: 2px solid #FF6B95;
            border-radius: 22px;
            padding: 12px 36px;
            margin: 0 0 0 10px;
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
        @media (max-width: 900px) {
            .customize-grid {
                gap: 30px 20px;
            }
            .product-card {
                max-width: 90vw;
            }
        }
        @media (max-width: 600px) {
            .customize-title {
                font-size: 1.3rem;
            }
            .customize-grid {
                flex-direction: column;
                align-items: center;
                gap: 20px;
            }
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
        .qty-controls input[type="number"] {
            margin: 0 8px;
            text-align: center;
            display: inline-block;
            background: transparent;
            border: none;
            width: 40px;
            font-size: 1rem;
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
                <a href="customize.php" class="active">Customize</a>
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
    <div class="customize-container">
        <div class="customize-title">Customize Your Bouquet</div>
        <form id="customizeForm" method="post" action="cart.php">
            <div class="customize-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php
                            // Set image based on product name
                            $lowerName = strtolower($product['name']);
                            // Change display name for specific products
                            $displayName = $product['name'];
                            if ($lowerName === 'green leaf set') {
                                $displayName = 'Pink Rose';
                            } else if ($lowerName === 'custom heart design') {
                                $displayName = 'Pink Lily';
                            }
                            if ($lowerName === 'pink crochet tulips') {
                                $img = 'images/tulips.jpg';
                            } else if ($lowerName === 'green leaf set') {
                                $img = 'images/roses.jpg';
                            } else if ($lowerName === 'custom heart design') {
                                $img = 'images/lily.jpg';
                            } else if ($lowerName === 'tulips') {
                                $img = 'images/lily.jpg';
                            } else if ($lowerName === 'rose crochet' || $lowerName === 'roses') {
                                $img = 'images/lily.jpg';
                            } else {
                                $img = !empty($product['image_url']) ? $product['image_url'] : 'images/default.jpg';
                            }
                        ?>
                        <img src="<?= htmlspecialchars($img) ?>" class="product-image" alt="<?= htmlspecialchars($displayName) ?>">
                        <div class="product-info">
                            <h3 class="product-title"><?= htmlspecialchars($displayName) ?></h3>
                            <p class="product-price">₱ <?= number_format($product['price'], 2) ?></p>
                            <div class="qty-controls">
                                <button type="button" class="qty-btn" onclick="changeQty('qty<?= $product['id'] ?>', -1)">-</button>
                                <input type="number" name="quantities[<?= $product['id'] ?>]" id="qty<?= $product['id'] ?>" value="0" min="0" style="width: 40px; text-align: center; border: none; background: transparent;" readonly>
                                <input type="hidden" name="display_names[<?= $product['id'] ?>]" value="<?= htmlspecialchars($displayName) ?>">
                                <input type="hidden" name="image_urls[<?= $product['id'] ?>]" value="<?= htmlspecialchars($img) ?>">
                                <button type="button" class="qty-btn" onclick="changeQty('qty<?= $product['id'] ?>', 1)">+</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align:center; margin-top: 30px;">
                <button type="submit" class="add-cart-btn">Add to cart</button>
                <a href="products.php" class="back-btn">Back</a>
            </div>
        </form>
    </div>
    <script>
        function changeQty(id, delta) {
            var input = document.getElementById(id);
            var value = parseInt(input.value) || 0;
            value += delta;
            if (value < 0) value = 0;
            input.value = value;
        }
    </script>
</body>
</html> 