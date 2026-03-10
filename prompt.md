🎨 COLOR THEME
RoleColor NameHex CodeUsagePrimaryDeep Navy#0F172AHeader, footer, hero bgAccentGold / Amber#F59E0BButtons, highlights, badgesSecondarySlate Gray#475569Subtext, bordersSurfaceOff-White#F8FAFCPage backgroundCard BGWhite#FFFFFFProduct cardsSuccessEmerald#10B981Stock badges, success msgsDangerRose#F43F5EErrors, sale tagsText DarkCharcoal#1E293BHeadingsText LightCool Gray#94A3B8Placeholders, captions
Tailwind Config additions in tailwind.config.js:
jstheme: {
  extend: {
    colors: {
      primary: '#0F172A',
      accent:  '#F59E0B',
      surface: '#F8FAFC',
    }
  }
}
```

----------------------imortant notice----------------------

i want a clean and minimalist design 
this is major project and i want to this project on live so make it perefct
this is major project 
This is a major project, so whatever number of pages are needed should be created because this is a major project and the navigation flow should also be in a proper way.

---

## 🗂️ FULL PAGE / NAVIGATION FLOW
```
HOME
 ├── SHOP
 │    ├── All Products
 │    ├── Sunglasses
 │    ├── Eyeglasses
 │    ├── Sports / Kids
 │    └── New Arrivals / Sale
 ├── BRANDS  (brand listing page)
 ├── ABOUT
 ├── CONTACT
 │
 ├── [USER AREA]
 │    ├── Login / Register
 │    ├── My Account
 │    │    ├── Profile
 │    │    ├── Orders
 │    │    └── Wishlist
 │    └── Logout
 │
 ├── CART
 └── CHECKOUT → Order Confirmation
```

---

## 📁 FOLDER STRUCTURE
```
optilux/
├── index.php               # Home
├── shop.php                # All products
├── product.php             # Single product detail
├── cart.php                # Cart page
├── checkout.php            # Checkout
├── order-confirm.php       # Thank you page
├── login.php
├── register.php
├── account.php             # Dashboard
├── orders.php
├── wishlist.php
├── about.php
├── contact.php
├── brands.php
│
├── admin/
│   ├── index.php           # Dashboard
│   ├── products.php        # CRUD products
│   ├── orders.php          # Manage orders
│   ├── users.php
│   └── categories.php
│
├── includes/
│   ├── db.php              # DB connection
│   ├── auth.php            # Session/auth helpers
│   ├── header.php
│   ├── footer.php
│   └── functions.php
│
├── assets/
│   ├── css/
│   │   └── output.css      # Compiled Tailwind
│   ├── js/
│   │   └── main.js
│   └── images/
│
└── uploads/
    └── products/           # Product images

🗃️ DATABASE SCHEMA (MySQL)
users
sqlCREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  password VARCHAR(255),
  role ENUM('user','admin') DEFAULT 'user',
  avatar VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
categories
sqlCREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  slug VARCHAR(100) UNIQUE,
  image VARCHAR(255)
);
brands
sqlCREATE TABLE brands (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  slug VARCHAR(100),
  logo VARCHAR(255)
);
products
sqlCREATE TABLE products (
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
  is_featured TINYINT(1) DEFAULT 0,
  is_new TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (brand_id) REFERENCES brands(id)
);
product_images
sqlCREATE TABLE product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT,
  image_path VARCHAR(255),
  is_primary TINYINT(1) DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id)
);
cart
sqlCREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  product_id INT,
  quantity INT DEFAULT 1,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);
wishlist
sqlCREATE TABLE wishlist (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  product_id INT,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);
orders
sqlCREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  total DECIMAL(10,2),
  status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  address TEXT,
  payment_method VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
order_items
sqlCREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  quantity INT,
  price DECIMAL(10,2),
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);
reviews
sqlCREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  product_id INT,
  rating TINYINT,
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

🖥️ PAGE-BY-PAGE UI BREAKDOWN
🏠 index.php — Home
SectionDetailsNavbarLogo left, nav links center, icons right (search🔍, heart🤍, cart🛒, user👤)Hero BannerFull-width bg-primary, big headline, gold CTA button, product imageCategory Grid4 cards: Sunglasses / Eyeglasses / Sports / KidsFeatured Products8-card grid with hover effects, add-to-cartBrands StripLogo carousel/gridPromo Banner"Free Shipping on orders over ₹999" stripNew Arrivals4-card sectionTestimonialsStar ratings, customer quotesNewsletterEmail signup with amber buttonFooterLinks, social icons, copyright
🛍️ shop.php — Shop
SectionDetailsLeft SidebarFilter by: Category, Brand, Price range, Gender, Lens type, Frame materialTop BarSort by (Price, New, Popular), results count, grid/list toggleProduct GridCards with image, name, brand, price, sale badge, wishlist heart, add to cartPaginationBottom of grid
🔍 product.php — Single Product
SectionDetailsImage GalleryMain image + thumbnailsInfo PanelName, brand, rating, price (with sale), stock statusOptionsColor selector, quantity inputButtonsAdd to Cart (amber), Add to Wishlist (outline)TabsDescription / Specifications / ReviewsRelated Products4 similar items below
🛒 cart.php

Item list with image, name, price, qty stepper, remove
Order summary sidebar: subtotal, shipping, total
"Proceed to Checkout" amber button

💳 checkout.php

Billing form (name, address, phone)
Payment method radio (COD / UPI / Card)
Order summary on right

👤 account.php

Sidebar: Profile / My Orders / Wishlist / Logout
Profile edit form
Orders table with status badge

🔧 admin/ Panel

Sidebar nav (dark navy bg)
Dashboard: stats cards (total orders, revenue, products, users)
Products CRUD with image upload
Orders management with status update
Users list


⚙️ KEY PHP FILES
includes/db.php
php<?php
$host = 'localhost';
$db   = 'optilux';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
?>
includes/auth.php
php<?php
session_start();
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
function requireLogin() {
    if (!isLoggedIn()) { header("Location: /login.php"); exit; }
}
function requireAdmin() {
    if (!isAdmin()) { header("Location: /index.php"); exit; }
}
?>
includes/functions.php
php<?php
function getProducts($conn, $limit = 8, $category = null) {
    $sql = "SELECT p.*, b.name as brand_name, pi.image_path
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1";
    if ($category) $sql .= " WHERE p.category_id = " . intval($category);
    $sql .= " ORDER BY p.created_at DESC LIMIT $limit";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

function getCartCount($conn, $user_id) {
    $r = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
    return $r->fetch_assoc()['total'] ?? 0;
}

function formatPrice($price) {
    return '₹' . number_format($price, 2);
}
?>

🧩 REUSABLE TAILWIND COMPONENTS
Navbar
html<nav class="bg-[#0F172A] text-white sticky top-0 z-50 shadow-lg">
  <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16">
    <a href="/" class="text-2xl font-bold text-amber-400">OptiLux</a>
    <div class="hidden md:flex gap-8 text-sm font-medium">
      <a href="/shop.php" class="hover:text-amber-400 transition">Shop</a>
      <a href="/brands.php" class="hover:text-amber-400 transition">Brands</a>
      <a href="/about.php" class="hover:text-amber-400 transition">About</a>
      <a href="/contact.php" class="hover:text-amber-400 transition">Contact</a>
    </div>
    <div class="flex items-center gap-4">
      <!-- Search, Wishlist, Cart, User icons -->
    </div>
  </div>
</nav>
Product Card
html<div class="bg-white rounded-2xl shadow hover:shadow-xl transition-all group overflow-hidden">
  <div class="relative overflow-hidden">
    <img src="..." class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-300">
    <span class="absolute top-2 left-2 bg-rose-500 text-white text-xs px-2 py-1 rounded-full">Sale</span>
    <button class="absolute top-2 right-2 p-2 bg-white rounded-full shadow hover:text-rose-500">❤</button>
  </div>
  <div class="p-4">
    <p class="text-slate-400 text-xs mb-1">Ray-Ban</p>
    <h3 class="font-semibold text-slate-800 mb-2">Classic Aviator</h3>
    <div class="flex items-center justify-between">
      <div>
        <span class="text-amber-500 font-bold text-lg">₹2,499</span>
        <span class="text-slate-400 line-through text-sm ml-2">₹3,999</span>
      </div>
      <button class="bg-[#0F172A] text-white text-xs px-3 py-2 rounded-lg hover:bg-amber-500 transition">Add to Cart</button>
    </div>
  </div>
</div>
CTA Button
html<button class="bg-amber-400 hover:bg-amber-500 text-[#0F172A] font-bold px-6 py-3 rounded-xl transition-all shadow-md">
  Shop Now
</button>

🔐 FEATURES CHECKLIST
FeatureStatusUser Registration & Login✅Product Listing with Filters✅Product Detail with Gallery✅Cart (session + DB)✅Wishlist✅Checkout + Order Placement✅Order History✅Admin Dashboard✅Product CRUD + Image Upload✅Search Functionality✅Responsive (Tailwind)✅Sale / New badges✅Star Ratings & Reviews✅Brand Filtering✅

🚀 SUGGESTED DEVELOPMENT ORDER

db.php + SQL schema setup
header.php + footer.php (navbar/footer)
index.php (home page)
shop.php (product grid + filters)
product.php (detail page)
login.php + register.php
cart.php + checkout.php
account.php (user dashboard)
admin/ panel (CRUD)
Polish: search, reviews, pagination