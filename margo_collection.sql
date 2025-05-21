-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2025 at 12:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `margo_collection`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_name`, `product_price`, `quantity`, `image_url`, `created_at`) VALUES
(26, 4, 'Pink Crochet Tulips', 29.99, 1, 'images/tulips.jpg', '2025-05-16 11:05:27'),
(27, 4, 'Pink Rose', 14.99, 2, 'images/roses.jpg', '2025-05-16 11:05:27'),
(28, 4, 'Pink Lily', 19.99, 2, 'images/lily.jpg', '2025-05-16 11:05:27');

-- --------------------------------------------------------

--
-- Table structure for table `customize_products`
--

CREATE TABLE `customize_products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customize_products`
--

INSERT INTO `customize_products` (`id`, `name`, `description`, `price`, `image_url`, `category`, `created_at`) VALUES
(2, 'Lily crochets', 'Beautiful handcrafted crochet lilies, perfect for home décor or gifting.', 78.00, 'images/682b7a1de5f9a.jpeg', '', '2025-05-19 18:36:13'),
(3, 'Sunflower crochet', 'Charming handcrafted crochet sunflowers, perfect for brightening up any space or gifting with love. 🌻', 77.00, 'images/682b7a660dc34.png', '', '2025-05-19 18:37:26'),
(4, 'dhalia crochet', '', 89.00, 'images/682b7b9ce97c8.jpeg', '', '2025-05-19 18:38:10'),
(5, 'Carnation crochet', '', 88.00, 'images/682b7aa96c49c.jpg', '', '2025-05-19 18:38:33'),
(6, 'Zinia crochet', '', 79.00, 'images/682b7abf88c4a.jpeg', '', '2025-05-19 18:38:55'),
(7, 'Lavender crochet', '', 103.00, 'images/682b7aeab08e6.png', '', '2025-05-19 18:39:38'),
(8, 'White Rose crochet', '', 83.00, 'images/682b7b0e9b6cf.jpg', '', '2025-05-19 18:40:14'),
(9, 'Dark Tulips crochet', '', 69.00, 'images/682b7b245fad6.jpeg', '', '2025-05-19 18:40:36'),
(10, 'Aster crochet', '', 88.00, 'images/682b7b66c788b.png', '', '2025-05-19 18:41:42');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `reply` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `message`, `reply`, `replied_at`, `created_at`) VALUES
(1, 'mikko', 'ranesjan@gmail.com', 'hi vyel', NULL, NULL, '2025-05-03 13:37:18'),
(2, 'mikko', 'ranesjan@gmail.com', 'hi vyel', NULL, NULL, '2025-05-03 13:39:00'),
(3, 'maevyel', '2301110373@student.buksu.edu.ph', 'hi', NULL, NULL, '2025-05-16 11:46:20'),
(4, 'maevyel', '2301110373@student.buksu.edu.ph', 'hello', NULL, NULL, '2025-05-16 11:46:32'),
(5, 'maevyel', '2301110373@student.buksu.edu.ph', 'ily', NULL, NULL, '2025-05-16 11:46:41'),
(9, 'jann', 'mikko.jacutin15@gmail.com', 'hi margo', NULL, NULL, '2025-05-20 01:49:43');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `payment_method`, `shipping_address`, `phone`, `total_amount`, `status`, `created_at`) VALUES
(9, 4, 'cod', 'salang homes', '09269441179', NULL, 'processing', '2025-05-16 10:37:24'),
(10, 4, 'cod', 'salang homes', '09269441179', NULL, 'completed', '2025-05-16 10:43:40'),
(11, 4, 'cod', 'salang homes', '09269441179', NULL, 'completed', '2025-05-16 10:47:16'),
(14, 15, 'cod', 'qwe', 'qwe', NULL, 'completed', '2025-05-17 17:35:33'),
(15, 15, 'cod', 'qwe', 'qwe', 49.98, 'completed', '2025-05-17 17:36:56'),
(16, 15, 'cod', 'qwe', 'qwe', 39.98, 'completed', '2025-05-17 17:37:12'),
(17, 15, 'cod', 'qwe', 'qwe', 39.98, 'completed', '2025-05-18 16:56:21'),
(18, 15, 'cod', 'qwe', 'qwe', 44.97, 'completed', '2025-05-18 17:09:43');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_name`, `product_price`, `product_id`, `quantity`, `image_url`) VALUES
(7, 9, 'Big Bouquets', 1130.00, NULL, 2, 'images/1.jpg'),
(8, 9, 'Special Bouquets', 1130.00, NULL, 1, 'images/2.jpg'),
(9, 10, 'Green Leaf Set', 14.99, NULL, 1, NULL),
(10, 10, 'Custom Heart Design', 19.99, NULL, 1, NULL),
(11, 11, 'Pink Crochet Tulips', 29.99, NULL, 1, NULL),
(12, 11, 'Green Leaf Set', 14.99, NULL, 1, NULL),
(13, 11, 'Custom Heart Design', 19.99, NULL, 1, NULL),
(14, 11, 'Special Bouquets', 1130.00, NULL, 1, 'images/2.jpg'),
(17, 14, 'Pink Crochet Tulips', 29.99, NULL, 2, 'images/1.jpg'),
(18, 14, 'Green Leaf Set', 14.99, NULL, 1, 'images/2.jpg'),
(19, 15, 'Pink Crochet Tulips', 29.99, NULL, 1, 'images/1.jpg'),
(20, 15, 'Custom Heart Design', 19.99, NULL, 1, 'images/3.jpg'),
(21, 16, 'Custom Heart Design', 19.99, NULL, 2, 'images/3.jpg'),
(22, 17, 'Custom Heart Design', 19.99, NULL, 2, 'images/3.jpg'),
(23, 18, 'Green Leaf Set', 14.99, NULL, 3, 'images/2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `category`, `stock`, `created_at`) VALUES
(1, 'Pink Crochet Tulips', 'Beautiful handcrafted crochet tulips, perfect for home decoration', 502.00, 'images/1.jpg', NULL, 0, '2025-05-03 12:30:22'),
(2, 'Green Leaf Set', 'Decorative crochet leaves for your tulips', 552.00, 'images/2.jpg', NULL, 0, '2025-05-03 12:30:22'),
(3, 'Custom Heart Design', 'Lovely crochet heart design, perfect for gifts', 674.00, 'images/3.jpg', NULL, 0, '2025-05-03 12:30:22'),
(13, 'Sun Flower Crochet', 'Handmade crochet sunflowers, crafted with love to bring sunshine into your home or special moments.', 599.00, 'images/682b7e8086bb8.jpg', NULL, 0, '2025-05-19 18:54:56'),
(14, 'Cute Handmade Crochet', 'Cute handmade crochet flowers, perfect for gifts, décor, or adding a touch of charm anywhere.', 799.00, 'images/682b7f8b63c43.jpg', NULL, 0, '2025-05-19 18:59:23'),
(15, 'Tulips Crochet', 'Adorable handmade crochet tulips, perfect for lasting bouquets, home décor, or heartfelt gifts.', 500.00, 'images/682b7fd072565.jpg', NULL, 0, '2025-05-19 19:00:32'),
(16, 'Pretty Crochet', 'Pretty handmade crochet pieces, crafted to add charm and warmth to any space. ', 1000.00, 'images/682b801808803.jpg', NULL, 0, '2025-05-19 19:01:44'),
(17, 'Cute Tulips Crochet', 'Cute handmade crochet tulips, perfect for brightening your day or gifting with love.', 500.00, 'images/682b805989a39.jpg', NULL, 0, '2025-05-19 19:02:49'),
(18, 'Rapunzel Flower Crochet', 'Whimsical handmade Rapunzel flower crochet, inspired by fairy tales — perfect for décor, cosplay, or magical gifts. ', 1000.00, 'images/682b80c562f85.jpg', NULL, 0, '2025-05-19 19:04:37'),
(19, 'Cute Crochet', 'Cute handmade crochet creations, perfect for adding a cozy and charming touch to your life. ', 500.00, 'images/682b8117ab6ba.jpg', NULL, 0, '2025-05-19 19:05:59'),
(20, 'balolong', 'yesser', 766.00, 'images/682be02c4b577.jpg', NULL, 0, '2025-05-20 01:51:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(10) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `username`, `email`, `address`, `phone`, `password`, `created_at`, `reset_token`, `reset_expires`, `role`) VALUES
(4, 'maevyel', 'lalang', '2301110373@student.buksu.edu.ph', 'salang homes', '09269441179', '$2y$10$wIk71VdjHAxOaUQnRQ4.3.r9z7Ne9emex9iTJf1cK5PZ0pbjXFyYu', '2025-05-16 10:23:45', NULL, NULL, 'user'),
(15, 'Margo', 'Margow', '', 'yes', 'no', '$2y$10$G/42j/40taBsN/vjF0P1B.qeZTrRBohobqPEfqXD60CDXUsfnoyq.', '2025-05-17 16:56:04', NULL, NULL, 'admin'),
(20, 'hello', 'trala', 'jeruemmanuelp@gmail.com', 'qwe', '09664756830', '$2y$10$yicfZI80LyjxZ/UnGBSGlOUC3qYD5mAhlW5lNZOgGqwz5ulUAh/b2', '2025-05-20 10:05:45', NULL, NULL, 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `customize_products`
--
ALTER TABLE `customize_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_order_items_order` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `customize_products`
--
ALTER TABLE `customize_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
