<?php
session_start();
require_once 'config/database.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

if (isset($_GET['id'])) {
    try {
        $product_id = $_GET['id'];
        $product_type = $_GET['type'] ?? 'products'; // Get type, default to 'products'

        // Determine table name based on type
        $table_name = ($product_type === 'customize') ? 'customize_products' : 'products';

        // Fetch product details from database
        $stmt = $pdo->prepare("SELECT * FROM " . $table_name . " WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            echo json_encode(['success' => true, 'product' => $product]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Product not found in specified table']);
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No product ID provided'
    ]);
} 