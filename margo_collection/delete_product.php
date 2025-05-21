<?php
session_start();
require_once 'config/database.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get product ID and type from POST request
        $product_id = $_POST['product_id'];
        $product_type = $_POST['type'] ?? 'products'; // Get type, default to 'products'

        // Determine table name based on type
        $table_name = ($product_type === 'customize') ? 'customize_products' : 'products';

        // Prepare and execute the delete statement
        $stmt = $pdo->prepare("DELETE FROM " . $table_name . " WHERE id = ?");
        $stmt->execute([$product_id]);

        // Check if deletion was successful (optional, but good practice)
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Product not found or could not be deleted from specified table']);
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
        'error' => 'Invalid request method'
    ]);
} 