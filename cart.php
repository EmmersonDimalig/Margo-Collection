<?php
session_start();
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug log function
function debug_log($message) {
    error_log(print_r($message, true));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Store the cart items in session if user is not logged in
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    debug_log("User not logged in, using session cart");
}

// Handle POST requests for cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_log("POST request received");
    debug_log($_POST); // Log the POST data

    // Handle bulk add from customize.php
    if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $product_id => $quantity) {
            $quantity = (int)$quantity;
            if ($quantity > 0) {
                // Fetch product info from DB
                $stmt = $pdo->prepare("SELECT * FROM customize_products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch();
                if ($product) {
                    $name = isset($_POST['display_names'][$product_id]) ? $_POST['display_names'][$product_id] : $product['name'];
                    $price = $product['price'];
                    $image = isset($_POST['image_urls'][$product_id]) ? $_POST['image_urls'][$product_id] : $product['image_url'];
                    if (isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];
                        // Check if item already exists in cart
                        $stmt2 = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_name = ?");
                        $stmt2->execute([$user_id, $name]);
                        $existing_item = $stmt2->fetch();
                        if ($existing_item) {
                            // Update quantity
                            $stmt3 = $pdo->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE id = ?");
                            $stmt3->execute([$quantity, $existing_item['id']]);
                        } else {
                            // Insert new item
                            $stmt3 = $pdo->prepare("INSERT INTO cart_items (user_id, product_name, product_price, quantity, image_url) VALUES (?, ?, ?, ?, ?)");
                            $stmt3->execute([$user_id, $name, $price, $quantity, $image]);
                        }
                    } else {
                        // Session cart for guests
                        if (!isset($_SESSION['cart'][$product_id])) {
                            $_SESSION['cart'][$product_id] = [
                                'quantity' => $quantity,
                                'price' => $price,
                                'name' => $name,
                                'image' => $image
                            ];
                        } else {
                            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                        }
                    }
                }
            }
        }
        // Redirect to cart page after adding
        header('Location: cart.php');
        exit();
    }

    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $product_id = $_POST['product_id'] ?? null;
        
        // If user is logged in, use database
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            debug_log("Processing for logged in user: " . $user_id);
            
            try {
                switch ($action) {
                    case 'add':
                        if ($product_id) {
                            // Check if item already exists in cart
                            $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_name = ?");
                            $stmt->execute([$user_id, $product_id]);
                            $existing_item = $stmt->fetch();

                            if ($existing_item) {
                                // Update quantity if item exists
                                $stmt = $pdo->prepare("UPDATE cart_items SET quantity = quantity + 1 WHERE id = ?");
                                $result = $stmt->execute([$existing_item['id']]);
                                debug_log("Updated existing item: " . ($result ? "success" : "failed"));
                            } else {
                                // Add new item if it doesn't exist
                                $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_name, product_price, image_url) VALUES (?, ?, ?, ?)");
                                $result = $stmt->execute([
                                    $user_id,
                                    $_POST['name'],
                                    $_POST['price'],
                                    $_POST['image']
                                ]);
                                debug_log("Inserted new item: " . ($result ? "success" : "failed"));
                                if (!$result) {
                                    debug_log("PDO Error: " . print_r($stmt->errorInfo(), true));
                                }
                            }
                        }
                        break;
                    
                    case 'remove':
                        if ($product_id) {
                            if (isset($_SESSION['user_id'])) {
                                // Use product_name for logged-in users
                                $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_name = ?");
                                $stmt->execute([$user_id, $product_id]);
                            } else {
                                // Use array key for guests
                                if (isset($_SESSION['cart'][$product_id])) {
                                    unset($_SESSION['cart'][$product_id]);
                                }
                            }
                        }
                        break;
                    
                    case 'update':
                        $quantity = $_POST['quantity'] ?? 1;
                        if ($product_id) {
                            if ($quantity > 0) {
                                $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_name = ?");
                                $stmt->execute([$quantity, $user_id, $product_id]);
                            } else {
                                $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_name = ?");
                                $stmt->execute([$user_id, $product_id]);
                            }
                        }
                        break;
                }
            } catch (PDOException $e) {
                debug_log("Database error: " . $e->getMessage());
                // Return error response for AJAX requests
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                    exit;
                }
            }
        } else {
            debug_log("User not logged in, processing for session cart");
            // Use session-based cart for non-logged in users
            switch ($action) {
                case 'add':
                    if ($product_id) {
                        if (!isset($_SESSION['cart'][$product_id])) {
                            $_SESSION['cart'][$product_id] = [
                                'quantity' => 1,
                                'price' => $_POST['price'],
                                'name' => $_POST['name'],
                                'image' => $_POST['image']
                            ];
                        } else {
                            $_SESSION['cart'][$product_id]['quantity']++;
                        }
                    }
                    break;
                    
                case 'remove':
                    if ($product_id && isset($_SESSION['cart'][$product_id])) {
                        unset($_SESSION['cart'][$product_id]);
                    }
                    break;
                    
                case 'update':
                    $quantity = $_POST['quantity'] ?? 1;
                    if ($product_id) {
                        if ($quantity > 0) {
                            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                        } else {
                            unset($_SESSION['cart'][$product_id]);
                        }
                    }
                    break;
            }
        }
        
        // If it's an AJAX request, return JSON response
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if (isset($_POST['edit_profile'])) {
        $new_name = trim($_POST['name']);
        $new_address = trim($_POST['address']);
        $new_phone = trim($_POST['phone']);
        $stmt = $pdo->prepare("UPDATE users SET firstname = ?, address = ?, phone = ? WHERE id = ?");
        $stmt->execute([$new_name, $new_address, $new_phone, $_SESSION['user_id']]);
        header('Location: account.php');
        exit();
    }
}

// Get cart items
$cart_items = [];
$total = 0;

if (isset($_SESSION['user_id'])) {
    try {
        // Get items from database for logged-in users
        $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        debug_log("Retrieved cart items for user " . $_SESSION['user_id'] . ": " . count($cart_items) . " items");
        
        // Calculate total
        foreach ($cart_items as $item) {
            $total += $item['product_price'] * $item['quantity'];
        }
    } catch (PDOException $e) {
        debug_log("Error retrieving cart items: " . $e->getMessage());
    }
} else {
    // Use session cart for non-logged in users
    $cart_items = $_SESSION['cart'];
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Margo Collection</title>
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

        .cart-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .cart-header h1 {
            color: #333;
            font-size: 24px;
        }

        .cart-items {
            margin-bottom: 30px;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 100px 2fr 1fr 1fr auto auto;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }

        .cart-item-details h3 {
            color: #333;
            margin-bottom: 5px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-controls input {
            width: 50px;
            padding: 5px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .price {
            font-weight: 500;
            color: #333;
        }

        .subtotal {
            font-weight: 500;
            color: #333;
        }

        .subtotal-controls {
            display: flex;
            align-items: center;
            gap: 10px; /* Adjust this value for desired spacing */
        }

        .remove-btn {
            background: #ff6b6b;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .remove-btn:hover {
            background: #ff4757;
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

        .empty-cart {
            text-align: center;
            padding: 40px;
        }

        .empty-cart p {
            color: #666;
            margin-bottom: 20px;
        }

        .continue-shopping {
            display: inline-block;
            padding: 10px 20px;
            background-color: #FF6B95;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-size: 14px;
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
                    <a href="user_pending_orders.php">Pending Orders</a>
                    <a href="contact.php">Contact</a>
                    <a href="about.php">About</a>
                </nav>
                <div class="nav-icons">
                    <a href="cart.php" class="cart-icon active">🛒</a>
                    <a href="account.php" class="profile-icon">👤</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="cart-container">
            <div class="cart-header">
                <h1>Shopping Cart</h1>
            </div>

            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <p>Your cart is empty</p>
                    <a href="products.php" class="continue-shopping">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-items">
                    <?php foreach ($cart_items as $product_id => $item): ?>
                        <div class="cart-item">
                            <?php
                                // Determine product name and image for both logged-in and guest users
                                if (isset($_SESSION['user_id'])) {
                                    $name = $item['product_name'];
                                } else {
                                    $name = $item['name'];
                                }
                                $lowerName = strtolower($name);
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
                                    if (isset($_SESSION['user_id'])) {
                                        $img = !empty($item['image_url']) ? $item['image_url'] : 'images/default.jpg';
                                    } else {
                                        $img = !empty($item['image']) ? $item['image'] : 'images/default.jpg';
                                    }
                                }
                            ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($name); ?>">
                            <div class="cart-item-details">
                                <h3><?php echo htmlspecialchars($name); ?></h3>
                            </div>
                            <div class="price">₱<?php echo number_format($item['product_price'], 2); ?></div>
                            <div class="quantity-controls">
                                <input type="number" value="<?php echo $item['quantity']; ?>" min="0"
                                       onchange="updateQuantity('<?php echo $product_id; ?>', this.value)"
                                       oninput="updateQuantity('<?php echo $product_id; ?>', this.value)">
                            </div>
                            <div class="subtotal-controls">
                                <div class="subtotal">₱<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></div>
                                <?php
                                if (isset($_SESSION['user_id'])) {
                                    $removeKey = $item['product_name'];
                                } else {
                                    $removeKey = $product_id;
                                }
                                ?>
                                <button class="remove-btn" onclick="removeItem('<?php echo htmlspecialchars($removeKey); ?>')">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-total">
                    <p>Cart Total</p>
                    <p class="shipping">Shipping fee included</p>
                    <p class="total">₱<?php echo number_format($total, 2); ?></p>
                </div>

                <div class="cart-buttons">
                    <button class="cart-btn edit-btn" onclick="window.location.href='products.php'">Continue Shopping</button>
                    <button class="cart-btn proceed-btn" onclick="window.location.href='checkout.php'">Proceed to Payment</button>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p style="text-align: center;">&copy; 2025 Margo Collection. All rights reserved.</p>
    </footer>

    <script>
        function updateQuantity(productId, quantity) {
            quantity = parseInt(quantity);
            if (quantity <= 0) {
                removeItem(productId);
                return;
            }
            fetch('cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `action=update&product_id=${productId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function removeItem(productId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `action=remove&product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }
    </script>
</body>
</html> 