<?php
session_start();
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch products from the database
$stmt = $pdo->query("SELECT * FROM customize_products ORDER BY id ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Customize Products - Margo Collection</title>
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

        /* Customize Section Styles (Admin View) */
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

         .admin-product-actions {
             margin-top: 10px;
         }

        .admin-product-actions button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .edit-product-btn {
            background-color: #FFC107; /* Yellow */
            color: white;
        }

        .edit-product-btn:hover {
             background-color: #ffaa00;
        }

        .delete-product-btn {
            background-color: #DC3545; /* Red */
            color: white;
        }
         .delete-product-btn:hover {
            background-color: #c82333;
        }

         .add-product-card {
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
            justify-content: center;
            text-align: center;
            cursor: pointer;
             transition: transform 0.3s ease;
         }

        .add-product-card:hover {
             transform: translateY(-5px);
         }

        .add-product-card .plus-icon {
             font-size: 48px;
             color: #FF6B95;
             margin-bottom: 10px;
        }

         .add-product-card h3 {
            font-size: 1.1rem;
            color: #222;
            margin-bottom: 5px;
            font-weight: 600;
         }

         .add-product-card p {
             font-size: 0.9em;
             color: #666;
             margin-bottom: 20px;
         }


        @media (max-width: 900px) {
            .customize-grid {
                gap: 30px 20px;
            }
            .product-card, .add-product-card {
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

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #666;
        }

        /* Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50; /* Green color for success */
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateX(200%);
            transition: transform 0.3s ease-in-out;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification .check-icon {
            font-size: 20px;
        }

        .notification .message {
            font-size: 14px;
            font-weight: 500;
        }

         .modal-content label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }

        .modal-content input[type="text"], 
        .modal-content input[type="number"], 
        .modal-content textarea, 
        .modal-content input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box; /* Include padding and border in element's total width and height */
        }

        .modal-content textarea {
            height: 80px;
        }

        .modal-content button[type="submit"] {
             background-color: #4CAF50; /* Green */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
             margin-top: 15px;
             transition: background-color 0.3s;
        }

         .modal-content button[type="submit"]:hover {
             background-color: #45a049;
         }

    </style>
</head>
<body>
    <header class="header">
        <div class="search-container">
            <div class="search-bar">
                <!-- Search bar can be kept or removed based on admin needs -->
                <input type="text" placeholder="Search...">
            </div>
        </div>
        <div class="nav-container">
            <div class="nav-content">
                <nav class="nav-menu">
                    <a href="admin_products.php">Products</a>
                    <a href="admin_customize.php" class="active">Customize</a>
                    <a href="admin_order.php">Orders</a>
                    <a href="admin_history.php">Order History</a>
                    <a href="admin_contact.php">Contact Messages</a>
                </nav>
                <div class="nav-icons">
                    <a href="admin_account.php" class="profile-icon">👤</a>
                     <!-- Consider adding a logout link here -->
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="customize-container">
            <div class="customize-title">Manage Customizable Products</div>
            <div class="customize-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php
                            // Set image based on product name (keeping original logic for image selection)
                            $lowerName = strtolower($product['name']);
                             $img = !empty($product['image_url']) ? $product['image_url'] : 'images/default.jpg'; // Use $product['image_url'] consistently
                             // Override image for specific products as in original customize.php
                            if ($lowerName === 'pink crochet tulips') {
                                $img = 'images/tulips.jpg';
                            } else if ($lowerName === 'green leaf set') {
                                $img = 'images/roses.jpg';
                            } else if ($lowerName === 'custom heart design') {
                                $img = 'images/lily.jpg';
                            }
                        ?>
                        <img src="<?= htmlspecialchars($img) ?>" class="product-image" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="product-info">
                            <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="product-price">₱<?= number_format($product['price'], 2) ?></p>
                             <div class="admin-product-actions">
                                <button class="edit-product-btn" data-id="<?= $product['id'] ?>">Edit</button>
                                <button class="delete-product-btn" data-id="<?= $product['id'] ?>">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Add Product Card -->
                 <div class="add-product-card" id="addProductCard">
                    <span class="plus-icon">+</span>
                    <h3>Add New Product</h3>
                    <p>Click to add a new product</p>
                 </div>

            </div>
        </div>
    </main>

    <!-- Add/Edit Product Modal -->
    <div class="modal" id="addProductModal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeAddProductModal()">&times;</button>
            <h2 id="modalTitle">Add New Product</h2>
            <form id="addProductForm" enctype="multipart/form-data">
                <input type="hidden" name="product_id" id="productId">
                <div style="margin-bottom: 15px;">
                    <label for="productName">Product Name</label>
                    <input type="text" name="product_name" id="productName" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="productPrice">Price (₱)</label>
                    <input type="number" name="product_price" id="productPrice" step="0.01" required>
                </div>
                 <div style="margin-bottom: 15px;">
                    <label for="productCategory">Category</label>
                    <input type="text" name="product_category" id="productCategory">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="productDescription">Description</label>
                    <textarea name="product_description" id="productDescription"></textarea>
                </div>
                <div style="margin-bottom: 20px;">
                    <label for="productImage">Product Image</label>
                    <input type="file" name="product_image" id="productImage" accept="image/*">
                    <small id="currentImage" style="display: none;">Current Image: </small>
                </div>
                <button type="submit" id="submitButton">Add Product</button>
            </form>
        </div>
    </div>

    <!-- Notification -->
    <div class="notification" id="notification">
        <span class="check-icon">✓</span>
        <span class="message">Action successful!</span>
    </div>

    <script>
        // Function to show notification
        function showNotification(message = 'Action successful!') {
            const notification = document.getElementById('notification');
            notification.querySelector('.message').textContent = message;
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Add/Edit Product Modal Functions
        const addProductModal = document.getElementById('addProductModal');
        const addProductForm = document.getElementById('addProductForm');
        const modalTitle = document.getElementById('modalTitle');
        const productIdInput = document.getElementById('productId');
        const productNameInput = document.getElementById('productName');
        const productPriceInput = document.getElementById('productPrice');
        const productCategoryInput = document.getElementById('productCategory');
        const productDescriptionInput = document.getElementById('productDescription');
        const productImageInput = document.getElementById('productImage');
        const currentImageText = document.getElementById('currentImage');
        const submitButton = document.getElementById('submitButton');
        const customizeGrid = document.querySelector('.customize-grid'); // Use customize-grid
        const addProductCard = document.getElementById('addProductCard');

        // Show modal when clicking Add Product card
        addProductCard.addEventListener('click', () => {
            addProductForm.reset();
            productIdInput.value = '';
            modalTitle.textContent = 'Add New Product';
            submitButton.textContent = 'Add Product';
            addProductModal.style.display = 'flex';
            currentImageText.style.display = 'none';
            productImageInput.required = true;
        });

        function closeAddProductModal() {
            addProductModal.style.display = 'none';
        }

        // Close modal when clicking outside
        addProductModal.addEventListener('click', (e) => {
            if (e.target === addProductModal) {
                closeAddProductModal();
            }
        });

        // Handle edit button click (using event delegation)
        customizeGrid.addEventListener('click', async (e) => {
            if (e.target.classList.contains('edit-product-btn')) {
                 const productId = e.target.getAttribute('data-id');

                try {
                    // Fetch product data
                    const response = await fetch(`get_product.php?id=${productId}&type=customize`);
                    const data = await response.json();

                    if (data.success) {
                        // Populate the modal form
                        productIdInput.value = data.product.id;
                        productNameInput.value = data.product.name;
                        productPriceInput.value = data.product.price;
                         productCategoryInput.value = data.product.category;
                        productDescriptionInput.value = data.product.description;

                        // Show current image info
                        if (data.product.image_url) {
                            currentImageText.textContent = 'Current Image: ' + data.product.image_url.split('/').pop();
                            currentImageText.style.display = 'block';
                            productImageInput.required = false; // Image is not required on edit if one exists
                        } else {
                             currentImageText.style.display = 'none';
                             productImageInput.required = true;
                        }

                        // Change modal title and button text
                        modalTitle.textContent = 'Edit Product';
                        submitButton.textContent = 'Save Changes';

                        // Show the modal
                        addProductModal.style.display = 'flex';

                    } else {
                        showNotification('Error fetching product details: ' + data.error);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Error fetching product details');
                }
            }
        });

        // Handle delete button click (using event delegation)
         customizeGrid.addEventListener('click', async (e) => {
            if (e.target.classList.contains('delete-product-btn')) {
                const productId = e.target.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this product?')) {
                    try {
                        const response = await fetch('delete_product.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: `product_id=${productId}&type=customize`
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Remove the product card from the grid
                            e.target.closest('.product-card').remove();
                            showNotification('Product deleted successfully!');
                        } else {
                            showNotification('Error: ' + data.error);
                        }                            
                    } catch (error) {
                        console.error('Error:', error);
                        showNotification('Error deleting product');
                    }
                }
            }
        });

        // Handle form submission (for add and edit)
        addProductForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(addProductForm);
            formData.append('type', 'customize');

            const url = productIdInput.value ? 'update_product.php' : 'add_product.php';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showNotification(productIdInput.value ? 'Product updated successfully!' : 'Product added successfully!');
                    closeAddProductModal();
                    // Reload the page to show updated products (simpler for now)
                    location.reload();

                } else {
                    showNotification('Error: ' + data.error);
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error processing product');
            }
        });

    </script>
</body>
</html> 