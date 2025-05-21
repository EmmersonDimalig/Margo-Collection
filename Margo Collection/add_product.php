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
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_description = $_POST['product_description'] ?? null; // Description might be optional
        $product_category = $_POST['product_category'] ?? null; // Category might be optional
        $product_stock = $_POST['product_stock'] ?? 0; // Stock might be optional, default to 0
        $product_type = $_POST['type'] ?? 'products'; // Get type, default to 'products'

        // Determine table name based on type
        $table_name = ($product_type === 'customize') ? 'customize_products' : 'products';

        // Handle image upload
        $upload_path = null;
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
        }

        // Prepare INSERT statement based on table
        if ($table_name === 'customize_products') {
            // For customize_products, no stock column
            $stmt = $pdo->prepare("INSERT INTO customize_products (name, price, description, image_url, category) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$product_name, $product_price, $product_description, $upload_path, $product_category]);
        } else {
            // For products, include stock column
            $stmt = $pdo->prepare("INSERT INTO products (name, price, description, image_url, category, stock) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$product_name, $product_price, $product_description, $upload_path, $product_category, $product_stock]);
        }

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Product added successfully!',
            'id' => $pdo->lastInsertId(), // Return the new product ID
            'name' => $product_name,
            'price' => $product_price,
            'description' => $product_description,
            'image_url' => $upload_path,
            'category' => $product_category,
            'stock' => $product_stock // Include stock in response even if not used by customize
        ]);

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