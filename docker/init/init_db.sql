-- Drop existing tables
DROP TABLE IF EXISTS order_item;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS coupon;
DROP TABLE IF EXISTS product_stock;
DROP TABLE IF EXISTS product_variation;
DROP TABLE IF EXISTS product;

-- Create product table
CREATE TABLE product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

-- Create variation table
CREATE TABLE product_variation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    name VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
);

-- Create stock table
CREATE TABLE product_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    variation_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (variation_id) REFERENCES product_variation(id) ON DELETE CASCADE
);

-- Create coupon table
CREATE TABLE coupon (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    value DECIMAL(10,2),
    valid_until DATE,
    minimum_value DECIMAL(10,2)
);

-- Create orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subtotal DECIMAL(10,2),
    shipping DECIMAL(10,2),
    total DECIMAL(10,2),
    coupon_id INT NULL,
    postal_code VARCHAR(9),
    address TEXT,
    status VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES coupon(id) ON DELETE SET NULL
);

-- Table for linking products and orders
CREATE TABLE order_item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    variation_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE,
    FOREIGN KEY (variation_id) REFERENCES product_variation(id) ON DELETE SET NULL
);

-- Table for cart items
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    variation_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE,
    FOREIGN KEY (variation_id) REFERENCES product_variation(id) ON DELETE CASCADE
);

-- Table for linking coupons to carts
CREATE TABLE cart_coupon (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coupon_id INT NOT NULL,
    FOREIGN KEY (coupon_id) REFERENCES coupon(id) ON DELETE CASCADE
);

