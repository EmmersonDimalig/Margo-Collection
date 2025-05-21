<?php
session_start();
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Cart Table Check</h2>";

// Check table structure
try {
    $stmt = $pdo->query("DESCRIBE cart_items");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse; margin-bottom: 20px;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error checking table structure: " . $e->getMessage() . "</p>";
}

// Check existing data
try {
    $stmt = $pdo->query("SELECT * FROM cart_items");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Existing Cart Items:</h3>";
    if (count($items) > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Product Name</th><th>Price</th><th>Quantity</th><th>Image URL</th><th>Created At</th></tr>";
        foreach ($items as $item) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($item['id']) . "</td>";
            echo "<td>" . htmlspecialchars($item['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($item['product_name']) . "</td>";
            echo "<td>" . htmlspecialchars($item['product_price']) . "</td>";
            echo "<td>" . htmlspecialchars($item['quantity']) . "</td>";
            echo "<td>" . htmlspecialchars($item['image_url']) . "</td>";
            echo "<td>" . htmlspecialchars($item['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No items found in the cart.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error checking cart items: " . $e->getMessage() . "</p>";
}

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
    table {
        width: 100%;
        margin-bottom: 20px;
    }
    th {
        background-color: #f5f5f5;
        text-align: left;
    }
</style>";
?> 