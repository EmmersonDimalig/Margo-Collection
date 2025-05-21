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
        // Get form data and type
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_description = $_POST['product_description'] ?? null;
        $product_category = $_POST['product_category'] ?? null;
        $product_stock = $_POST['product_stock'] ?? 0; // Default to 0 if not provided (for products table)
        $product_type = $_POST['type'] ?? 'products'; // Get type, default to 'products'

        // Determine table name based on type
        $table_name = ($product_type === 'customize') ? 'customize_products' : 'products';

        $image_url = null;
        $update_image = false; // Flag to indicate if image needs to be updated

        // Handle image upload if a new image is provided
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'images/';
            $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            // Check if image file is a actual image
            $check = getimagesize($_FILES['product_image']['tmp_name']);
            if ($check === false) {
                throw new Exception('File is not an image.');
            }

            // Check file size (5MB max)
            if ($_FILES['product_image']['size'] > 5000000) {
                throw new Exception('File is too large.');
            }

            // Allow certain file formats
            if (!in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed.');
            }

            // Upload file
            if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                throw new Exception('Failed to upload image.');
            }
            $image_url = $upload_path;
            $update_image = true;

            // Optional: Delete old image if it exists (requires fetching old image_url)
            // You might want to implement this carefully, especially if images are shared

        }

        // Prepare UPDATE statement based on table and whether image is updated
        $sql = "UPDATE " . $table_name . " SET name = ?, price = ?, description = ?, category = ?";
        $params = [$product_name, $product_price, $product_description, $product_category];

        if ($table_name === 'products') {
            // Add stock to update query for products table
            $sql .= ", stock = ?";
            $params[] = $product_stock;
        }

        if ($update_image) {
            // Add image_url to update query if image is updated
            $sql .= ", image_url = ?";
            $params[] = $image_url;
        }

        $sql .= " WHERE id = ?";
        $params[] = $product_id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Return success response
        echo json_encode(['success' => true, 'message' => 'Product updated successfully!']);

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