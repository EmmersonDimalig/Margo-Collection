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

// Check if order_id and status are provided
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Validate the status value
    $allowed_statuses = ['pending', 'accepted', 'processing', 'ready_to_ship', 'shipping', 'completed'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
        exit();
    }

    try {
        // Update the order status in the database
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);

        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            // Order ID not found or status was already the same
            echo json_encode(['success' => false, 'message' => 'Order not found or status already updated.']);
        }

    } catch (Exception $e) {
        // Log or display error appropriately
        error_log("Error updating order status: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }

} else {
    // Required parameter not provided
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?> 