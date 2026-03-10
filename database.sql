CREATE DATABASE IF NOT EXISTS `optilux`;
USE `optilux`;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  password VARCHAR(255),
  role ENUM('user','admin') DEFAULT 'user',
  avatar VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  slug VARCHAR(100) UNIQUE,
  image VARCHAR(255)
);

CREATE TABLE brands (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  slug VARCHAR(100),
  logo VARCHAR(255)
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200),
  slug VARCHAR(200) UNIQUE,
  description TEXT,
  price DECIMAL(10,2),
  sale_price DECIMAL(10,2) DEFAULT NULL,
  stock INT DEFAULT 0,
  category_id INT,
  brand_id INT,
  frame_material VARCHAR(100),
  lens_type VARCHAR(100),
  gender ENUM('men','women','unisex','kids'),
  image VARCHAR(255),
  is_featured TINYINT(1) DEFAULT 0,
  is_new TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (brand_id) REFERENCES brands(id)
);

CREATE TABLE product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT,
  image_path VARCHAR(255),
  is_primary TINYINT(1) DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  product_id INT,
  quantity INT DEFAULT 1,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE wishlist (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  product_id INT,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  total DECIMAL(10,2),
  status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  address TEXT,
  payment_method VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES orders(id)
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  quantity INT,
  price DECIMAL(10,2),
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  product_id INT,
  rating TINYINT,
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- DUMMY DATA INSERTIONS

-- 1. Admin User
-- Password is 'password123'
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@gmail.com', 'password123', 'admin');

-- 2. Categories
INSERT INTO categories (name, slug) VALUES 
('Sunglasses', 'sunglasses'),
('Eyeglasses', 'eyeglasses'),
('Sports', 'sports'),
('Kids', 'kids');

-- 3. Brands
INSERT INTO brands (name, slug) VALUES 
('Ray-Ban', 'ray-ban'),
('Oakley', 'oakley'),
('Gucci', 'gucci'),
('Tom Ford', 'tom-ford');

-- 4. Products
INSERT INTO products (name, slug, description, price, sale_price, stock, category_id, brand_id, frame_material, lens_type, gender, image, is_featured, is_new) VALUES 
('Ray-Ban Classic Aviator', 'ray-ban-classic-aviator', 'The timeless Classic Aviator sunglasses by Ray-Ban. Sleek metal frame with green classic G-15 lenses.', 12900.00, 9900.00, 50, 1, 1, 'Metal', 'Classic', 'unisex', 'https://i.pinimg.com/736x/2f/2c/96/2f2c9611944bf8470001299fd349a93a.jpg', 1, 0),

('Oakley Holbrook', 'oakley-holbrook', 'Oakley Holbrook is a timeless, classic design fused with modern Oakley technology. Perfect for exploring and everyday wear.', 11500.00, NULL, 30, 1, 2, 'O Matter', 'Prizm', 'men', 'https://i.pinimg.com/1200x/98/87/a2/9887a2de67f0938b7cd0484d4f0385cb.jpg', 1, 1),

('Gucci Oversized Square', 'gucci-oversized-square', 'A glamorous oversized square shape crafted from bold acetate, embodying Gucci luxury and fashion.', 28000.00, NULL, 15, 1, 3, 'Acetate', 'Gradient', 'women', 'https://i.pinimg.com/1200x/c4/25/28/c425284a0c05f9cc7294a8ad27c88ab9.jpg', 1, 0),

('Tom Ford Blue Block', 'tom-ford-blue-block', 'Elegant optical frames featuring blue block lens technology to reduce eye strain from digital screens.', 22000.00, 18500.00, 25, 2, 4, 'Acetate', 'Blue Light Blocking', 'unisex', 'https://i.pinimg.com/1200x/d4/5f/8b/d45f8bf67534266f7484d532998a1f66.jpg', 1, 1),

('Ray-Ban Wayfarer', 'ray-ban-wayfarer', 'The Original Wayfarer Classics are the most recognizable style in the history of sunglasses.', 11000.00, NULL, 100, 1, 1, 'Acetate', 'Classic', 'unisex', 'https://i.pinimg.com/1200x/af/fa/4e/affa4e3a5c15daa816d2217be5e55510.jpg', 0, 0),

('Oakley Radar EV Path', 'oakley-radar-ev-path', 'A new milestone in the heritage of performance, Radar EV takes breakthroughs of a revolutionary design further.', 15000.00, NULL, 40, 3, 2, 'O Matter', 'Prizm Sport', 'unisex', 'https://i.pinimg.com/736x/5d/13/3e/5d133e2673db76b2982f10c4ab038b49.jpg', 1, 0),

('Gucci Geometric Frame', 'gucci-geometric-frame', 'Distinctive geometric optical frames that blend vintage allure with contemporary refinement.', 31000.00, 26000.00, 10, 2, 3, 'Metal', 'Clear', 'women', 'https://i.pinimg.com/1200x/fc/60/fc/fc60fcf52db1bb45be25099603d3db7f.jpg', 0, 0),

('Ray-Ban Junior Aviator', 'ray-ban-junior-aviator', 'Aviator style scaled down for kids. Same great look and protection.', 6500.00, NULL, 60, 4, 1, 'Metal', 'Classic', 'kids', 'https://i.pinimg.com/1200x/f8/74/ae/f874aee1a7d63331ca3f0d9d3f159601.jpg', 1, 1);