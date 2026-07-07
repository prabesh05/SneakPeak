CREATE DATABASE SneakPeak;

USE SneakPeak;

CREATE TABLE login(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user'
);



-- to promote the registered account in to admin

UPDATE login SET role = 'admin' WHERE email = 'admin1@sneakpeak.np';
UPDATE login SET role = 'admin' WHERE email = 'admin2@sneakpeak.np';
UPDATE login SET role = 'admin' WHERE email = 'owner@sneakpeak.np';


CREATE TABLE  products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(50) NOT NULL,
    name VARCHAR(150) NOT NULL,
    colorway VARCHAR(150) DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    badge VARCHAR(20) DEFAULT NULL,
    img VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL,
    user_id INT DEFAULT 0,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    size VARCHAR(50) DEFAULT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    line_total DECIMAL(10,2) NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    shipping_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);




