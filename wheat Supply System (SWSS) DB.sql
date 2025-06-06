-- Database Creation
DROP DATABASE IF EXISTS `wheat_supply_system`;
CREATE DATABASE `wheat_supply_system` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `wheat_supply_system`;

-- Users Table
CREATE TABLE `users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('farmer', 'supplier', 'manufacturer', 'distributor', 'retailer') NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `phone` VARCHAR(20),
  `address` TEXT,
  `last_login` TIMESTAMP NULL,
  `is_active` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `idx_username` (`username`),
  UNIQUE KEY `idx_email` (`email`)
) ENGINE=InnoDB;

-- Products Table
CREATE TABLE `products` (
  `product_id` INT NOT NULL AUTO_INCREMENT,
  `product_name` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `specifications` TEXT,
  `sku` VARCHAR(30) UNIQUE,
  `unit_weight_kg` DECIMAL(5,2),
  `expiry_months` INT,
  `min_stock_threshold` INT DEFAULT 100,
  `is_active` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`product_id`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB;

-- Locations Table
CREATE TABLE `locations` (
  `location_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `type` ENUM('farm', 'warehouse', 'processing_plant', 'retail') NOT NULL,
  `address` TEXT NOT NULL,
  `capacity_kg` DECIMAL(10,2),
  `gps_coordinates` VARCHAR(50),
  `is_active` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB;

-- Inventory Table
CREATE TABLE `inventory` (
  `inventory_id` INT NOT NULL AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `location_id` INT NOT NULL,
  `batch_number` VARCHAR(50) NOT NULL,
  `quantity` INT NOT NULL,
  `expiry_date` DATE,
  `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` ENUM('in_stock', 'reserved', 'shipped', 'expired') DEFAULT 'in_stock',
  PRIMARY KEY (`inventory_id`),
  KEY `idx_product_location` (`product_id`, `location_id`),
  CONSTRAINT `fk_inventory_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_inventory_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Inventory Transactions Table
CREATE TABLE `inventory_transactions` (
  `transaction_id` INT NOT NULL AUTO_INCREMENT,
  `inventory_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `transaction_type` ENUM('purchase', 'sale', 'transfer', 'adjustment', 'loss') NOT NULL,
  `quantity` INT NOT NULL,
  `reference_id` INT,
  `reference_type` ENUM('order', 'shipment', 'manual'),
  `transaction_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`),
  KEY `idx_inventory` (`inventory_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_transaction_date` (`transaction_date`),
  CONSTRAINT `fk_transaction_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`inventory_id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_transaction_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Orders Table
CREATE TABLE `orders` (
  `order_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `order_number` VARCHAR(20) NOT NULL,
  `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pending', 'confirmed', 'shipped', 'delivered') DEFAULT 'pending',
  `total_amount` DECIMAL(12,2) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `billing_address` TEXT,
  `payment_method` ENUM('credit_card', 'bank_transfer', 'cash_on_delivery'),
  `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
  `expected_delivery` DATE,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `idx_order_number` (`order_number`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Order Items Table
CREATE TABLE `order_items` (
  `item_id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Vendors Table
CREATE TABLE `vendors` (
  `vendor_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `company_name` VARCHAR(100) NOT NULL,
  `tax_id` VARCHAR(30) UNIQUE,
  `business_reg_no` VARCHAR(50),
  `validation_status` ENUM('pending', 'approved', 'rejected', 'pending_visit') DEFAULT 'pending',
  `approval_date` DATE,
  `financial_score` DECIMAL(3,2),
  `reputation_score` DECIMAL(3,2),
  `compliance_status` DECIMAL(3,2),
  PRIMARY KEY (`vendor_id`),
  UNIQUE KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_vendor_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Vendor Documents Table
CREATE TABLE `vendor_documents` (
  `document_id` INT NOT NULL AUTO_INCREMENT,
  `vendor_id` INT NOT NULL,
  `document_type` ENUM('tax_cert', 'license', 'insurance', 'financial_statement', 'other') NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `upload_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `is_verified` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`document_id`),
  KEY `idx_vendor` (`vendor_id`),
  CONSTRAINT `fk_document_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Demand Forecasts Table
CREATE TABLE `demand_forecasts` (
  `forecast_id` INT NOT NULL AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `forecast_date` DATE NOT NULL,
  `period` ENUM('weekly', 'monthly', 'quarterly') NOT NULL,
  `predicted_demand` INT NOT NULL,
  `confidence_score` DECIMAL(3,2),
  `actual_demand` INT,
  `accuracy_score` DECIMAL(3,2),
  `generated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`forecast_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_forecast_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Customer Segments Table
CREATE TABLE `customer_segments` (
  `segment_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `segment_type` ENUM('premium_buyer', 'bulk_purchaser', 'seasonal_customer', 'price_sensitive_buyer', 'occasional_purchaser') NOT NULL,
  `lifetime_value` DECIMAL(10,2),
  `last_purchase_date` DATE,
  `purchase_frequency` INT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`segment_id`),
  UNIQUE KEY `idx_user_segment` (`user_id`),
  CONSTRAINT `fk_segment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Chat Messages Table
CREATE TABLE `chat_messages` (
  `message_id` INT NOT NULL AUTO_INCREMENT,
  `sender_id` INT NOT NULL,
  `receiver_id` INT NOT NULL,
  `message_content` TEXT NOT NULL,
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('sent', 'delivered', 'read') DEFAULT 'sent',
  PRIMARY KEY (`message_id`),
  KEY `idx_sender` (`sender_id`),
  KEY `idx_receiver` (`receiver_id`),
  CONSTRAINT `fk_message_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_message_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Reports Table
CREATE TABLE `reports` (
  `report_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `report_type` ENUM('sales', 'inventory', 'vendor_performance', 'demand_forecast', 'customer_segmentation') NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `schedule` ENUM('daily', 'weekly', 'monthly', 'one_time') NOT NULL,
  `generated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pending', 'generated', 'failed') DEFAULT 'pending',
  PRIMARY KEY (`report_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_report_type` (`report_type`),
  CONSTRAINT `fk_report_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Stored Procedure for Order Number Generation
DELIMITER //
CREATE PROCEDURE `generate_order_number`(OUT new_order_number VARCHAR(20))
BEGIN
  DECLARE prefix VARCHAR(3) DEFAULT 'ORD';
  DECLARE sequence_num INT;
  
  SELECT COALESCE(MAX(SUBSTRING(order_number, 5)), 0) + 1 INTO sequence_num 
  FROM orders 
  WHERE order_number LIKE CONCAT(prefix, '%');
  
  SET new_order_number = CONCAT(prefix, LPAD(sequence_num, 7, '0'));
END //
DELIMITER ;

-- Initial Data
INSERT INTO `users` (`username`, `password_hash`, `role`, `email`, `phone`, `is_active`) 
VALUES 
('admin_user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'farmer', 'admin@swss.com', '+1234567890', TRUE);