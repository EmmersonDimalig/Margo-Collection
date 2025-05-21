<?php
session_start();
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Cart Database Test</h2>";

// Test 1: Check Database Connection
echo "<h3>Test 1: Database Connection</h3>";
try {
    $pdo->query("SELECT 1");
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Check Cart Items Table
echo "<h3>Test 2: Cart Items Table</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'cart_items'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Cart items table exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Cart items table does not exist</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error checking cart items table: " . $e->getMessage() . "</p>";
}

// Test 3: Test Cart Operations
echo "<h3>Test 3: Cart Operations</h3>";

// Create a test user ID
$test_user_id = 999; // Using a high number to avoid conflicts with real users

// Test adding an item
try {
    $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_name, product_price, quantity, image_url) 
                          VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        $test_user_id,
        'Test Product',
        '100.00',
        1,
        'test.jpg'
    ]);
    
    if ($result) {
        echo "<p style='color: green;'>✓ Successfully added test item to cart</p>";
        
        // Test retrieving the item
        $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ?");
        $stmt->execute([$test_user_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($items) > 0) {
            echo "<p style='color: green;'>✓ Successfully retrieved cart items</p>";
            echo "<pre>";
            print_r($items);
            echo "</pre>";
            
            // Test removing the item
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $result = $stmt->execute([$test_user_id]);
            
            if ($result) {
                echo "<p style='color: green;'>✓ Successfully removed test items from cart</p>";
            } else {
                echo "<p style='color: red;'>✗ Failed to remove test items from cart</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Failed to retrieve cart items</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Failed to add test item to cart</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error during cart operations: " . $e->getMessage() . "</p>";
}

// Test 4: Check Session Cart
echo "<h3>Test 4: Session Cart</h3>";
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    echo "<p style='color: green;'>✓ Session cart initialized</p>";
} else {
    echo "<p style='color: green;'>✓ Session cart already exists</p>";
}

// Add test item to session cart
$_SESSION['cart']['test_product'] = [
    'name' => 'Test Product',
    'price' => '100.00',
    'quantity' => 1,
    'image' => 'test.jpg'
];

echo "<p style='color: green;'>✓ Added test item to session cart</p>";
echo "<pre>";
print_r($_SESSION['cart']);
echo "</pre>";

// Clean up session cart
unset($_SESSION['cart']);
echo "<p style='color: green;'>✓ Cleaned up session cart</p>";

// Add some basic styling
echo "<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        line-height: 1.6;
    }
    h2 {
        color: #333;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
    }
    h3 {
        color: #666;
        margin-top: 20px;
    }
    pre {
        background: #f5f5f5;
        padding: 10px;
        border-radius: 5px;
    }
</style>";
?> 