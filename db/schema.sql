CREATE DATABASE IF NOT EXISTS caffe_aroma;
USE caffe_aroma;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    guests INT NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert a default admin account (password is 'admin123')
-- password_hash('admin123', PASSWORD_BCRYPT) gives $2y$10$vG0.pU5j7q/s4y/P4V.H1.l9F/.wK5n.sW...
INSERT IGNORE INTO users (username, email, password, role) VALUES 
('admin', 'admin@caffearoma.com', '$2y$10$tZ261/U/n29vH03sLw.o2uwFm20hK9R0fN4uH/d2sV8Yd51D7J0/m', 'admin');

-- Insert some dummy products
INSERT IGNORE INTO products (name, description, price, category, image_url) VALUES 
('Espresso', 'Strong and bold shot of pure coffee.', 2.50, 'Coffee', 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=500&q=80'),
('Latte', 'Smooth espresso with steamed milk and a light layer of foam.', 4.00, 'Coffee', 'https://images.unsplash.com/photo-1561047029-3000c68339ca?w=500&q=80'),
('Cappuccino', 'Perfect balance of espresso, steamed milk and foam.', 4.50, 'Coffee', 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=500&q=80'),
('Blueberry Muffin', 'Freshly baked muffin bursting with blueberries.', 3.50, 'Pastry', 'https://images.unsplash.com/photo-1525124568695-c4c6cd3ea847?w=500&q=80'),
('Croissant', 'Buttery, flaky, classic French pastry.', 3.00, 'Pastry', 'https://images.unsplash.com/photo-1555507036-ab1f40ce88cb?w=500&q=80');
