CREATE DATABASE IF NOT EXISTS caffe_aroma;
USE caffe_aroma;

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    phone      VARCHAR(20)  DEFAULT NULL,
    role       ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    price       DECIMAL(10,2) NOT NULL,
    category    VARCHAR(50) NOT NULL,
    image_url   VARCHAR(255),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reservations (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    user_id          INT NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    guests           INT  NOT NULL,
    phone            VARCHAR(20) DEFAULT NULL,
    status           ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS login_attempts (
    ip_address   VARCHAR(45) NOT NULL,
    attempts     TINYINT     NOT NULL DEFAULT 1,
    last_attempt TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (ip_address)
);

CREATE TABLE IF NOT EXISTS orders (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status       ENUM('pending','completed','cancelled') DEFAULT 'pending',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    order_id   INT NOT NULL,
    product_id INT NOT NULL,
    quantity   INT NOT NULL,
    price      DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT     NOT NULL,
    product_id INT     NOT NULL,
    rating     TINYINT NOT NULL,
    comment    TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY one_review_per_user (user_id, product_id),
    CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5),
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ----------------------------------------------------------------
-- Default admin account  (password: admin123)
-- ----------------------------------------------------------------
INSERT IGNORE INTO users (username, email, password, role) VALUES
('admin', 'admin@caffearoma.com',
 '$2y$10$tZ261/U/n29vH03sLw.o2uwFm20hK9R0fN4uH/d2sV8Yd51D7J0/m',
 'admin');

-- ----------------------------------------------------------------
-- Products  — high-quality Unsplash photos
-- ----------------------------------------------------------------
INSERT IGNORE INTO products (name, description, price, category, image_url) VALUES

-- ☕ Coffees
('Espresso',
 'Koncentrat i fortë me aromë intensive. Baza e çdo kafeje të mirë.',
 2.50, 'Kafe',
 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=600&q=80'),

('Latte',
 'Espresso i butë me qumësht të avulluar dhe shtresë të lehtë shkume.',
 4.00, 'Kafe',
 'https://images.unsplash.com/photo-1561047029-3000c68339ca?w=600&q=80'),

('Cappuccino',
 'Ekuilibri perfekt i espressit, qumështit të avulluar dhe shkumes.',
 4.50, 'Kafe',
 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=600&q=80'),

('Americano',
 'Espresso i zgjatur me ujë të nxehtë. I pastër, i plotë, i patundur.',
 3.00, 'Kafe',
 'https://images.unsplash.com/photo-1551030173-122aabc4489c?w=600&q=80'),

('Macchiato',
 'Espresso i markuar me pak shkumë qumështi mbi sipërfaqe.',
 3.50, 'Kafe',
 'https://images.unsplash.com/photo-1485808191679-5f86510bd9d4?w=600&q=80'),

('Flat White',
 'Espresso dyfish me qumësht mikrofoam të situr — zgjedhja e njohur.',
 4.50, 'Kafe',
 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=600&q=80'),

-- 🥐 Pastries
('Kroasan',
 'Pastë franceze me gjalp, e brishtë dhe e artë nga jashtë, e butë nga brenda.',
 3.00, 'Pastiçeri',
 'https://images.unsplash.com/photo-1555507036-ab1f40ce88cb?w=600&q=80'),

('Muffin me Boronica',
 'Muffin i freskët i pjekur plot me boronica të plota. I ngrohtë nga furra.',
 3.50, 'Pastiçeri',
 'https://images.unsplash.com/photo-1607958996333-41aef7caefaa?w=600&q=80'),

('Tiramisu',
 'Klasiku italian: biskota savoiardi, mascarpone kremoz dhe kakao mbi sipërfaqe.',
 5.00, 'Pastiçeri',
 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=600&q=80'),

('Cheesecake',
 'Cheesecake i kremoz me bazë biskote dhe xhel frutash të kuqe.',
 5.50, 'Pastiçeri',
 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=600&q=80');
