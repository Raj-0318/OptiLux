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

-- 4. Products (10 per brand = 40 products)

-- RAY-BAN (Brand ID: 1)
INSERT INTO products (name, slug, description, price, sale_price, stock, category_id, brand_id, frame_material, lens_type, gender, image, is_featured, is_new) VALUES 
('Ray-Ban Classic Aviator', 'ray-ban-classic-aviator', 'The timeless Classic Aviator sunglasses by Ray-Ban. Sleek metal frame with green classic G-15 lenses.', 12900.00, 9900.00, 50, 1, 1, 'Metal', 'Classic', 'unisex', 'https://i.pinimg.com/736x/e7/d4/2d/e7d42da88cdf85d498e12407a7206208.jpg', 1, 0),

('Ray-Ban Wayfarer Classic', 'ray-ban-wayfarer-classic', 'The Original Wayfarer Classics are the most recognizable style in the history of sunglasses.', 11000.00, NULL, 100, 1, 1, 'Acetate', 'Classic', 'unisex', 'https://i.pinimg.com/736x/a6/ba/c4/a6bac43df2e31e72b2beb03614586208.jpg', 0, 0),

('Ray-Ban Clubmaster', 'ray-ban-clubmaster', 'Retro-inspired frames with a sophisticated, vintage look. A true icon of style.', 13500.00, 11500.00, 45, 1, 1, 'Acetate/Metal', 'Polarized', 'unisex', 'https://i.pinimg.com/1200x/48/c5/6c/48c56c7e8a75c68273a233e0f6177e28.jpg', 1, 1),

('Ray-Ban Round Metal', 'ray-ban-round-metal', 'Totally retro. This look has been worn by legendary musicians and inspired by the 1960s counter-culture.', 12000.00, NULL, 35, 1, 1, 'Metal', 'Classic', 'unisex', 'https://i.pinimg.com/1200x/c2/2e/e1/c22ee139e4799b98fc6f5dedefa1c9c3.jpg', 0, 0),

('Ray-Ban Erika', 'ray-ban-erika', 'Erika sunglasses are the perfect accessory to complete any look. Featuring both classic and bright rubber fronts.', 9500.00, 8500.00, 60, 1, 1, 'Nylon', 'Gradient', 'women', 'https://i.pinimg.com/1200x/64/10/e8/6410e8df0c668aa3727b1e4b3e016dfe.jpg', 1, 0),

('Ray-Ban Optical Round', 'ray-ban-optical-round', 'Smart and sophisticated round eyeglasses for daily wear. Lightweight and comfortable.', 8500.00, NULL, 25, 2, 1, 'Acetate', 'Clear', 'unisex', 'https://i.pinimg.com/736x/3a/34/a9/3a34a90fc5ede7c19cd9b5d2ffbb3855.jpg', 0, 1),

('Ray-Ban Justin', 'ray-ban-justin', 'Justin sunglasses may just be one of the coolest looks in the Ray-Ban collection.', 10500.00, NULL, 80, 1, 1, 'Nylon', 'Solid', 'men', 'https://i.pinimg.com/1200x/5b/52/81/5b5281cdfecd0b33aad2434f03b9a658.jpg', 0, 0),

('Ray-Ban Hexagonal', 'ray-ban-hexagonal', 'What do you get when you cross a circle with a square? You get the Ray-Ban Hexagonal.', 14000.00, 12500.00, 40, 1, 1, 'Metal', 'Flat', 'unisex', 'https://i.pinimg.com/1200x/15/a5/10/15a510c8f6a84b26ce55ea3cc67d190d.jpg', 1, 0),

('Ray-Ban Junior Aviator', 'ray-ban-junior-aviator', 'Aviator style scaled down for kids. Same great look and protection.', 6500.00, NULL, 60, 4, 1, 'Metal', 'Classic', 'kids', 'https://i.pinimg.com/1200x/5f/dc/58/5fdc58d1b4a3d787bbc0cbe6450dee69.jpg', 1, 1),

('Ray-Ban Kids New Wayfarer', 'ray-ban-kids-wayfarer', 'The classic Wayfarer, sized specifically for children with durable frames.', 5800.00, 4900.00, 50, 4, 1, 'Acetate', 'Classic', 'kids', 'https://i.pinimg.com/736x/0c/4e/5f/0c4e5f361a30c76c45bde5fbece524da.jpg', 0, 0);

-- OAKLEY (Brand ID: 2)
INSERT INTO products (name, slug, description, price, sale_price, stock, category_id, brand_id, frame_material, lens_type, gender, image, is_featured, is_new) VALUES 
('Oakley Holbrook', 'oakley-holbrook', 'Oakley Holbrook is a timeless, classic design fused with modern Oakley technology.', 11500.00, NULL, 30, 1, 2, 'O Matter', 'Prizm', 'men', 'https://i.pinimg.com/1200x/f7/61/c8/f761c8150d109738ad6a0f310935d09c.jpg', 1, 1),

('Oakley Frogskins', 'oakley-frogskins', 'In pop culture, it was a time like no other. Ronald Reagan was in the White House, The Terminator was in the box office.', 9000.00, 7500.00, 40, 1, 2, 'O Matter', 'Classic', 'unisex', 'https://i.pinimg.com/1200x/a7/85/e1/a785e1f127c515a6ab1eecd9babc8031.jpg', 0, 0),

('Oakley Radar EV Path', 'oakley-radar-ev-path', 'A new milestone in the heritage of performance, Radar EV takes breakthroughs of a revolutionary design further.', 15000.00, NULL, 40, 3, 2, 'O Matter', 'Prizm Sport', 'unisex', 'https://i.pinimg.com/736x/5d/13/3e/5d133e2673db76b2982f10c4ab038b49.jpg', 1, 0),

('Oakley Jawbreaker', 'oakley-jawbreaker', 'The ultimate sport design - answering the demands of world-class athletes with a 40-year heritage of uncompromising excellence.', 18000.00, 16500.00, 25, 3, 2, 'O Matter', 'Prizm Road', 'unisex', 'https://i.pinimg.com/1200x/71/59/01/715901ad881d57771b390fdab6fc94af.jpg', 1, 1),

('Oakley Sutro', 'oakley-sutro', 'Designed with performance in mind, Sutro gives cyclists a bold and versatile look that they can wear on and off the bike.', 14500.00, NULL, 30, 3, 2, 'O Matter', 'Prizm 24K', 'unisex', 'https://i.pinimg.com/1200x/4f/59/e2/4f59e2eb2a0e0187984d30cc7fad7af4.jpg', 0, 0),

('Oakley Flak 2.0 XL', 'oakley-flak-2-0-xl', 'The XL edition offers a standard size frame with enhanced lens coverage, and every millimeter of the peripheral view is optimized.', 13000.00, 11000.00, 35, 3, 2, 'O Matter', 'Prizm Field', 'unisex', 'https://i.pinimg.com/736x/88/3b/5f/883b5f9950e56af92dfef22587212aeb.jpg', 1, 0),

('Oakley Gascan', 'oakley-gascan', 'We swapped soft curves for straight edges and hard lines to sculpt our first high-wrap lifestyle shades.', 10000.00, NULL, 45, 1, 2, 'O Matter', 'Plutonite', 'men', 'https://i.pinimg.com/1200x/b6/1f/54/b61f540007fde7d5529c710929f4987d.jpg', 0, 0),

('Oakley Turbine', 'oakley-turbine', 'Oakley Turbine cranks up the active look with interchangeable icons plus inset zones of sure-grip Unobtainium.', 12500.00, NULL, 20, 1, 2, 'O Matter', 'Prizm Daily', 'men', 'https://i.pinimg.com/736x/29/8c/de/298cde3bc93d83447b473e6d6b0dca02.jpg', 0, 1),

('Oakley Resistor Kids', 'oakley-resistor-kids', 'Engineered for youth athletes, the Resistor is sized specifically for smaller faces without compromising performance.', 7500.00, 6500.00, 30, 4, 2, 'O Matter', 'Prizm Youth', 'kids', 'https://i.pinimg.com/1200x/b4/25/14/b4251458f32df57afe4230ff0718db9d.jpg', 1, 1),

('Oakley Fuel Cell', 'oakley-fuel-cell', 'The idea was to create clean, authentic style for those who dont just walk the path of life — they stomp it and leave footprints.', 11500.00, NULL, 40, 1, 2, 'O Matter', 'Polarized', 'men', 'https://i.pinimg.com/1200x/44/cf/bb/44cfbbd8601a2a7d27c633171fc02c23.jpg', 0, 0);

-- GUCCI (Brand ID: 3)
INSERT INTO products (name, slug, description, price, sale_price, stock, category_id, brand_id, frame_material, lens_type, gender, image, is_featured, is_new) VALUES 
('Gucci Oversized Square', 'gucci-oversized-square', 'A glamorous oversized square shape crafted from bold acetate, embodying Gucci luxury.', 28000.00, NULL, 15, 1, 3, 'Acetate', 'Gradient', 'women', 'https://i.pinimg.com/1200x/2e/7f/33/2e7f33d1a1bad7484f29006bd28bdf02.jpg', 1, 0),

('Gucci Geometric Frame', 'gucci-geometric-frame', 'Distinctive geometric optical frames that blend vintage allure with contemporary refinement.', 31000.00, 26000.00, 10, 2, 3, 'Metal', 'Clear', 'women', 'https://i.pinimg.com/736x/fc/60/fc/fc60fcf52db1bb45be25099603d3db7f.jpg', 0, 0),

('Gucci Aviator with Web', 'gucci-aviator-web', 'Classic aviator silhouette with Gucci Signature Web detail on the temples.', 25000.00, 22000.00, 20, 1, 3, 'Metal', 'Solid', 'unisex', 'https://i.pinimg.com/736x/8b/fd/f0/8bfdf032b06a2a84edbdb4a8d9f69ad2.jpg', 1, 1),

('Gucci Cat Eye Acetate', 'gucci-cat-eye-acetate', 'Retro-inspired cat-eye frames with subtle Gucci branding at the temples.', 26500.00, NULL, 18, 1, 3, 'Acetate', 'Dark Grey', 'women', 'https://i.pinimg.com/1200x/18/ff/b9/18ffb9ac2a27046decf6d8dbb3b984d0.jpg', 0, 0),

('Gucci Round Optical', 'gucci-round-optical', 'Elegant round optical frames for a scholarly and fashionable aesthetic.', 24000.00, NULL, 12, 2, 3, 'Metal', 'Clear', 'unisex', 'https://i.pinimg.com/736x/b2/9b/e2/b29be25f63d81f7ee83e1d7ce6065577.jpg', 0, 1),

('Gucci Rectangular Sunglasses', 'gucci-rectangular-sunglasses', 'Bold rectangular frames with thick temples featuring the iconic GG logo.', 27500.00, 24500.00, 22, 1, 3, 'Acetate', 'Polarized', 'men', 'https://i.pinimg.com/1200x/2b/14/fb/2b14fb4426cadd38b6e7f0112349771c.jpg', 1, 0),

('Gucci Square Optical', 'gucci-square-optical', 'Sophisticated square optical frames, perfect for professional or academic settings.', 23500.00, NULL, 15, 2, 3, 'Acetate', 'Clear', 'men', 'https://i.pinimg.com/736x/5e/d5/0a/5ed50abd7c25dfda3b8a88692b277757.jpg', 0, 0),

('Gucci Mask Sunglasses', 'gucci-mask-sunglasses', 'Avant-garde mask-style sunglasses for a truly high-fashion statement.', 35000.00, NULL, 8, 1, 3, 'Nylon', 'Mirrored', 'unisex', 'https://i.pinimg.com/1200x/6d/ac/92/6dac929702af7710bf0f2378be005769.jpg', 1, 1),

('Gucci Kids Round', 'gucci-kids-round', 'Luxury for luxury lovers — sized down for the next generation of fashion icons.', 15000.00, 12000.00, 20, 4, 3, 'Acetate', 'Classic', 'kids', 'https://i.pinimg.com/1200x/24/05/20/2405206665ede961fbb155b9cb665c82.jpg', 0, 0),

('Gucci Pilot Optical', 'gucci-pilot-optical', 'Refined pilot-style optical frames, blending aeronautical inspiration with Gucci luxury.', 29000.00, NULL, 10, 2, 3, 'Metal', 'Clear', 'unisex', 'https://i.pinimg.com/1200x/64/d8/d9/64d8d9c8cb53ffe7e1e54b578047c049.jpg', 0, 0);

-- TOM FORD (Brand ID: 4)
INSERT INTO products (name, slug, description, price, sale_price, stock, category_id, brand_id, frame_material, lens_type, gender, image, is_featured, is_new) VALUES 
('Tom Ford Blue Block', 'tom-ford-blue-block', 'Elegant optical frames featuring blue block lens technology to reduce eye strain.', 22000.00, 18500.00, 25, 2, 4, 'Acetate', 'Blue Light Blocking', 'unisex', 'https://i.pinimg.com/1200x/d4/5f/8b/d45f8bf67534266f7484d532998a1f66.jpg', 1, 1),

('Tom Ford Marko', 'tom-ford-marko', 'The Marko sunglasses as seen on James Bond in Skyfall. Timeless pilot shape.', 24500.00, NULL, 15, 1, 4, 'Metal', 'Solid Grey', 'men', 'https://i.pinimg.com/736x/d0/d6/f0/d0d6f0b346f9bc4b5d881b4a74a91fb8.jpg', 1, 0),

('Tom Ford Snowdon', 'tom-ford-snowdon', 'Bold acetate frames with a thick profile, epitomising Tom Ford sophisticated masculinity.', 26000.00, 23000.00, 20, 1, 4, 'Acetate', 'Dark Brown', 'men', 'https://i.pinimg.com/736x/e5/6b/bb/e56bbb9b8067fcd5d88960378058c22c.jpg', 0, 0),

('Tom Ford Whitney', 'tom-ford-whitney', 'Modern cross-frame design for women, a true staple of the Tom Ford collection.', 28500.00, NULL, 12, 1, 4, 'Acetate', 'Gradient Grey', 'women', 'https://i.pinimg.com/736x/c4/35/c4/c435c43a7e5e30b3c2bab66e8f9af267.jpg', 1, 1),

('Tom Ford FT5401', 'tom-ford-ft5401', 'Sleek and minimalist rectangular optical frames for high-end everyday use.', 21000.00, NULL, 30, 2, 4, 'Metal/Acetate', 'Clear', 'unisex', 'https://i.pinimg.com/736x/68/30/a5/6830a5e8b2610de615eae898bb344b5a.jpg', 0, 0),

('Tom Ford Jennifer', 'tom-ford-jennifer', 'Cut-away detail lens for a sophisticated and modern look. High fashion at its best.', 27000.00, 24000.00, 18, 1, 4, 'Acetate', 'Gradient Brown', 'women', 'https://i.pinimg.com/1200x/b7/d4/36/b7d4361ec08f8477ea605f310a7f9ce4.jpg', 1, 0),

('Tom Ford Arnaud', 'tom-ford-arnaud', 'Soft square frames with the iconic metal T logo on the temples.', 25500.00, NULL, 22, 1, 4, 'Acetate', 'Blue', 'men', 'https://i.pinimg.com/736x/45/4d/35/454d35c82fd19aa9c62e3874d5fe3124.jpg', 0, 1),

('Tom Ford Square Optical', 'tom-ford-square-optical', 'Classic square optical frames that provide a bold and intellectual look.', 22500.00, NULL, 15, 2, 4, 'Acetate', 'Clear', 'unisex', 'https://i.pinimg.com/736x/6c/cd/7b/6ccd7b42cc67e075f3127274c7810543.jpg', 0, 0),

('Tom Ford Kids Wayfarer', 'tom-ford-kids-wayfarer', 'The Tom Ford touch applied to children eyewear. Classic design, ultimate quality.', 12000.00, 9500.00, 20, 4, 4, 'Acetate', 'Solid', 'kids', 'https://i.pinimg.com/736x/39/1d/f9/391df9483b637af57071e188f8995c27.jpg', 0, 0),

('Tom Ford Wallace', 'tom-ford-wallace', 'A modern take on the classic aviator with a thick acetate bridge and sleek temples.', 29500.00, 26000.00, 10, 1, 4, 'Acetate/Metal', 'Green', 'men', 'https://i.pinimg.com/1200x/67/2f/38/672f38fdbf29e368530c312343b3b81e.jpg', 1, 0);