<?php
session_start();

require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Basic admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Check if order_id is provided
if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Delete order items first due to foreign key constraint
        $stmt_items = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt_items->execute([$order_id]);

        // Then delete the order from the orders table
        $stmt_order = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt_order->execute([$order_id]);

        // Commit the transaction
        $pdo->commit();

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        // Rollback the transaction in case of error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Log or display error appropriately
        error_log("Error deleting order: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }

} else {
    // Required parameter not provided
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?> 