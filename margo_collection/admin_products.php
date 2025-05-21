<?php
session_start();
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Margo Collection</title>
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

        .login-btn {
            background-color: #FF6B95;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #FF4777;
        }

        /* Products Grid */
        .products-container {
            max-width: 900px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 50px;
            margin-top: 40px;
        }

        .product-card {
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            max-width: 280px;
            margin: 0 auto;
            padding-bottom: 15px;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: auto;
            display: block;
        }

        .product-info {
            padding: 15px;
            text-align: center;
        }

        .product-title {
            font-size: 1rem;
            color: #333;
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 1rem;
            color: #FF6B95;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .checkout-btn {
            background-color: #FF6B95;
            color: white;
            padding: 6px 20px;
            border-radius: 15px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            display: inline-block;
            margin-top: 8px;
        }

        .checkout-btn:hover {
            background-color: #FF4777;
        }

        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(1, 1fr);
            }
            
            .nav-menu {
                display: none;
            }
        }

        /* Cart Overlay Styles */
        .cart-overlay {
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

        .cart-content {
            background-color: white;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 800px;
            position: relative;
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .cart-header h2 {
            color: #333;
            margin: 0;
        }

        .cart-items {
            margin-bottom: 20px;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 100px 2fr 1fr 1fr 1fr;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
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

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #FF6B95;
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
                    <a href="admin_products.php" class="active">Products</a>
                    <a href="admin_customize.php">Customize</a>
                    <a href="admin_order.php">Orders</a>
                    <a href="admin_history.php">Order History</a>
                    <a href="admin_contact.php">Contact Messages</a>
                </nav>
                <div class="nav-icons">
                    <a href="admin_account.php" class="profile-icon">👤</a>
                </div>
            </div>
        </div>
    </header>

    <main class="products-container">
        <div class="products-grid">
            <?php
            // Fetch products from database
            $stmt = $pdo->query("SELECT * FROM products ORDER BY id ASC");
            $products = $stmt->fetchAll();

            foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                    <div class="product-info">
                        <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-price">₱<?= number_format($product['price'], 2) ?></p>
                        <p><?= htmlspecialchars($product['description']) ?></p>
                        <div class="admin-product-actions" style="margin-top: 10px;">
                            <button class="edit-product-btn" data-id="<?= $product['id'] ?>" style="background-color: #FFC107; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; margin-right: 5px;">Edit</button>
                            <button class="delete-product-btn" data-id="<?= $product['id'] ?>" style="background-color: #DC3545; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Delete</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Add Product Card -->
            <div class="product-card">
                <div style="height: 200px; background-color: #FFE1E9; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 48px;">+</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Add New Product</h3>
                    <p>Click to add a new product to the store</p>
                    <button class="add-product-btn" style="background-color: #4CAF50; color: white; padding: 6px 20px; border-radius: 15px; text-decoration: none; font-size: 13px; font-weight: 500; transition: background-color 0.3s; border: none; cursor: pointer; display: inline-block; margin-top: 8px;">Add Product</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Cart Overlay -->
    <div class="cart-overlay">
        <div class="cart-content">
            <button class="close-btn">&times;</button>
            <div class="cart-header">
                <h2>Shopping Cart</h2>
            </div>
            <div class="cart-items">
                <div class="cart-item">
                    <div>Product</div>
                    <div>Price</div>
                    <div>Qty</div>
                    <div>Total</div>
                </div>
            </div>
            <div class="cart-total">
                <p>Cart Total</p>
                <p class="shipping">Shipping fee included</p>
                <p class="total">₱0.00</p>
            </div>
            <div class="cart-buttons">
                <button class="cart-btn edit-btn">Edit order</button>
                <button class="cart-btn proceed-btn">Proceed to Payment</button>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal" id="addProductModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div class="modal-content" style="background-color: white; padding: 30px; border-radius: 20px; width: 90%; max-width: 500px; position: relative;">
            <button class="close-btn" onclick="closeAddProductModal()">&times;</button>
            <h2 id="modalTitle" style="margin-bottom: 20px; color: #333;">Add New Product</h2>
            <form id="addProductForm" enctype="multipart/form-data">
                <input type="hidden" name="product_id" id="productId">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #666;">Product Name</label>
                    <input type="text" name="product_name" id="productName" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #666;">Price (₱)</label>
                    <input type="number" name="product_price" id="productPrice" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #666;">Description</label>
                    <textarea name="product_description" id="productDescription" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; height: 80px;"></textarea>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; color: #666;">Product Image</label>
                    <input type="file" name="product_image" id="productImage" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                    <small id="currentImage" style="display: none; margin-top: 5px; color: #666;">Current Image: </small>
                </div>
                <button type="submit" id="submitButton" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%;">Add Product</button>
            </form>
        </div>
    </div>

    <!-- Notification -->
    <div class="notification" id="notification">
        <span class="check-icon">✓</span>
        <span class="message">Item added to cart!</span>
    </div>

    <script>
        // Get all add to cart buttons
        const addToCartButtons = document.querySelectorAll('.checkout-btn');
        const cartOverlay = document.querySelector('.cart-overlay');
        const closeButton = document.querySelector('.close-btn');
        const cartItems = document.querySelector('.cart-items');
        const notification = document.getElementById('notification');

        // Function to show notification
        function showNotification(message = 'Item added to cart!') {
            notification.querySelector('.message').textContent = message;
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Add click event to all add to cart buttons
        addToCartButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const productName = button.getAttribute('data-name');
                const productPrice = button.getAttribute('data-price');
                const productImage = button.getAttribute('data-image');

                // Add to cart using AJAX
                fetch('cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `action=add&product_id=${encodeURIComponent(productName)}&price=${productPrice}&name=${encodeURIComponent(productName)}&image=${encodeURIComponent(productImage)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification();
                    } else if (data.error) {
                        showNotification('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error adding item to cart');
                });
            });
        });

        // Close overlay when clicking close button
        closeButton.addEventListener('click', () => {
            cartOverlay.style.display = 'none';
        });

        // Close overlay when clicking outside
        cartOverlay.addEventListener('click', (e) => {
            if (e.target === cartOverlay) {
                cartOverlay.style.display = 'none';
            }
        });

        // Add Product Modal Functions
        const addProductModal = document.getElementById('addProductModal');
        const addProductForm = document.getElementById('addProductForm');
        const modalTitle = document.getElementById('modalTitle');
        const productIdInput = document.getElementById('productId');
        const productNameInput = document.getElementById('productName');
        const productPriceInput = document.getElementById('productPrice');
        const productDescriptionInput = document.getElementById('productDescription');
        const productImageInput = document.getElementById('productImage');
        const currentImageText = document.getElementById('currentImage');
        const submitButton = document.getElementById('submitButton');

        // Show modal when clicking Add Product button
        document.querySelector('.add-product-btn').addEventListener('click', () => {
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

        // Handle edit button click
        document.querySelectorAll('.edit-product-btn').forEach(button => {
            button.addEventListener('click', async (e) => {
                const productId = e.target.getAttribute('data-id');

                try {
                    // Fetch product data
                    const response = await fetch(`get_product.php?id=${productId}`);
                    const data = await response.json();

                    if (data.success) {
                        // Populate the modal form
                        productIdInput.value = data.product.id;
                        productNameInput.value = data.product.name;
                        productPriceInput.value = data.product.price;
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
            });
        });

        // Handle form submission (will be updated to handle both add and edit)
        addProductForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(addProductForm);

            const url = productIdInput.value ? 'update_product.php' : 'add_product.php';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    if (productIdInput.value) {
                        // Handle successful edit
                        showNotification('Product updated successfully!');
                        // Note: You might want to update the product card on the page here
                         location.reload(); // Simple reload for now
                    } else {
                        // Handle successful add
                        const productsGrid = document.querySelector('.products-grid');
                        const newProductCard = document.createElement('div');
                        newProductCard.className = 'product-card';
                        newProductCard.innerHTML = `
                            <img src="${data.image_url}" alt="${data.name}" class="product-image">
                            <div class="product-info">
                                <h3 class="product-title">${data.name}</h3>
                                <p class="product-price">₱${data.price}</p>
                                <p>${data.description}</p>
                                <div class="admin-product-actions" style="margin-top: 10px;">
                                    <button class="edit-product-btn" data-id="${data.id}" style="background-color: #FFC107; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; margin-right: 5px;">Edit</button>
                                    <button class="delete-product-btn" data-id="${data.id}" style="background-color: #DC3545; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Delete</button>
                                </div>
                            </div>
                        `;
                        
                        // Insert before the Add Product card
                        productsGrid.insertBefore(newProductCard, productsGrid.lastElementChild);
                        
                        // Show success notification
                        showNotification('Product added successfully!');
                    }
                    
                    // Close modal and reset form
                    closeAddProductModal();
                    addProductForm.reset();

                } else {
                    showNotification('Error: ' + data.error);
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error processing product');
            }
        });

        // Handle delete button click
        document.querySelectorAll('.delete-product-btn').forEach(button => {
            button.addEventListener('click', async (e) => {
                const productId = e.target.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this product?')) {
                    try {
                        const response = await fetch('delete_product.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: `product_id=${productId}`
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
            });
        });
    </script>
</body>
</html> 