<?php
require_once __DIR__ . '/includes/header.php';

// Pagination setup
$limit = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Filters
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build Query
$where = [];
$params = [];
if ($category_id) { $where[] = "p.category_id = :category_id"; $params[':category_id'] = $category_id; }
if ($brand_id) { $where[] = "p.brand_id = :brand_id"; $params[':brand_id'] = $brand_id; }

$where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$order_by = "ORDER BY p.created_at DESC";
if ($sort === 'price_asc') $order_by = "ORDER BY COALESCE(p.sale_price, p.price) ASC";
if ($sort === 'price_desc') $order_by = "ORDER BY COALESCE(p.sale_price, p.price) DESC";

// Get Total
$sql_count = "SELECT COUNT(*) as total FROM products p $where_clause";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->execute($params);
$total_products = $stmt_count->fetch()['total'];
$total_pages = ceil($total_products / $limit);

// Get Products
$sql = "SELECT p.*, b.name as brand_name 
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.id
        $where_clause $order_by LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get Categories and Brands for sidebar
$categories = $conn->query("SELECT * FROM categories")->fetchAll();
$brands = $conn->query("SELECT * FROM brands")->fetchAll();

// Get User's Wishlist IDs
$wishlist_ids = [];
if (isLoggedIn()) {
    $stmt_wish = $conn->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $stmt_wish->execute([$_SESSION['user_id']]);
    $wishlist_ids = $stmt_wish->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!-- Cinematic Hero: The Collection -->
<section class="relative h-[80vh] bg-primary flex items-center justify-center overflow-hidden border-b border-white/5">
    <!-- Sophisticated Background -->
    <div class="absolute inset-0 z-0">
        <img src="https://i.pinimg.com/1200x/9b/7b/15/9b7b15899ec41c0c4a81c4a5ffd16f3e.jpg  " 
             alt="The Archive" class="w-full h-full object-cover opacity-20 grayscale scale-110 animate-[pulse_8s_ease-in-out_infinite]">
        <div class="absolute inset-0 bg-gradient-to-t from-primary via-transparent to-primary/80"></div>
    </div>
    
    <div class="relative z-10 text-center px-6 max-w-5xl animate-in fade-in slide-in-from-bottom-8 duration-1000">
        <span class="text-accent text-[10px] font-bold tracking-[0.5em] uppercase mb-8 block font-sans">Curated Optical Goods</span>
        <h1 class="text-6xl md:text-8xl font-serif text-white tracking-widest leading-none mb-10 italic">
            The <br><span class="not-italic text-white/40 tracking-[0.2em] font-light uppercase text-3xl md:text-5xl mt-6 block">Collection</span>
        </h1>
        <div class="w-[1px] h-20 bg-gradient-to-b from-accent to-transparent mx-auto"></div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-6 py-20 flex flex-col md:flex-row gap-16">
    <!-- Filters Sidebar Minimal -->
    <div class="w-full md:w-56 flex-shrink-0">
        <div class="sticky top-32">
            <h3 class="font-bold text-[10px] tracking-[0.2em] uppercase text-white/50 mb-6 border-b border-black/10 pb-4">Categories</h3>
            <ul class="space-y-4 mb-12">
                <li><a href="/Optilux/shop.php" class="<?= !$category_id ? 'text-primary font-bold' : 'text-slate-500 hover:text-primary transition duration-300' ?> text-xs tracking-wider uppercase">All Products</a></li>
                <?php foreach($categories as $cat): ?>
                    <li><a href="/Optilux/shop.php?category_id=<?= $cat['id'] ?>" class="<?= $category_id == $cat['id'] ? 'text-primary font-bold' : 'text-slate-500 hover:text-primary transition duration-300' ?> text-xs tracking-wider uppercase"><?= htmlspecialchars($cat['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>

            <h3 class="font-bold text-[10px] tracking-[0.2em] uppercase text-white/50 mb-6 border-b border-black/10 pb-4">Brands</h3>
            <ul class="space-y-4">
                <li><a href="/Optilux/shop.php" class="<?= !$brand_id ? 'text-primary font-bold' : 'text-slate-500 hover:text-primary transition duration-300' ?> text-xs tracking-wider uppercase">All Brands</a></li>
                <?php foreach($brands as $b): ?>
                    <li><a href="/Optilux/shop.php?brand_id=<?= $b['id'] ?>" class="<?= $brand_id == $b['id'] ? 'text-primary font-bold' : 'text-slate-500 hover:text-primary transition duration-300' ?> text-xs tracking-wider uppercase"><?= htmlspecialchars($b['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow">
        <!-- Minimal Top Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-end sm:items-center gap-4 mb-12 pb-6 border-b border-black/5">
            <p class="text-slate-400 text-xs tracking-widest uppercase"><?= min($offset + 1, $total_products) ?>–<?= min($offset + $limit, $total_products) ?> OF <?= $total_products ?> PRODUCTS</p>
            <div class="flex items-center gap-4">
                <span class="text-slate-400 text-xs tracking-widest uppercase">Sort:</span>
                <select onchange="window.location.href=this.value" class="bg-transparent border-b border-black/20 pb-1 text-xs tracking-wider uppercase focus:outline-none focus:border-primary text-primary font-medium cursor-pointer">
                    <option value="?sort=newest<?= $category_id ? '&category_id='.$category_id : '' ?>" <?= $sort == 'newest' ? 'selected' : '' ?>>Latest</option>
                    <option value="?sort=price_asc<?= $category_id ? '&category_id='.$category_id : '' ?>" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price (Asc)</option>
                    <option value="?sort=price_desc<?= $category_id ? '&category_id='.$category_id : '' ?>" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price (Desc)</option>
                </select>
            </div>
        </div>

        <!-- Product Layout Match Index -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-16">
            <?php foreach($products as $product): ?>
            <div class="group flex flex-col">
                <div class="relative w-full aspect-square bg-[#F7F7F7] mb-6 overflow-hidden group">
                    <?php $img = !empty($product['image']) ? $product['image'] : ''; ?>
                    <a href="/Optilux/product.php?id=<?= $product['id'] ?>" class="group bg-white block aspect-[4/5] relative overflow-hidden transition-all duration-700">
                        <div class="absolute inset-0 bg-slate-50 transition-colors duration-700 group-hover:bg-slate-100"></div>
                        <img src="<?= $img ?>" class="w-full h-full object-cover mix-blend-multiply transition-all duration-700 ease-in-out group-hover:scale-110" onerror="this.src=''">
                    </a>
                    
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        <?php if ($product['is_new']): ?>
                            <span class="bg-primary text-white text-[9px] px-3 py-1 font-bold tracking-[0.2em] uppercase">New</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="absolute inset-0 bg-white/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-4 pointer-events-none backdrop-blur-[2px]">
                        <?php $in_wishlist = in_array($product['id'], $wishlist_ids); ?>
                        <a href="javascript:void(0)" onclick="toggleWishlist(<?= $product['id'] ?>, this)"
                           class="pointer-events-auto w-12 h-12 <?= $in_wishlist ? 'bg-accent text-white' : 'bg-white text-primary' ?> hover:text-accent rounded-full flex items-center justify-center transition duration-300 shadow-xl border border-black/5"
                           title="<?= $in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist' ?>">
                            <i data-lucide="heart" class="w-4 h-4 <?= $in_wishlist ? 'fill-current' : 'stroke-[1.5]' ?>"></i>
                        </a>
                        <a href="/Optilux/cart.php?action=add&id=<?= $product['id'] ?>" class="pointer-events-auto w-12 h-12 bg-primary text-white hover:bg-accent rounded-full flex items-center justify-center transition duration-300 shadow-xl">
                            <i data-lucide="shopping-bag" class="w-4 h-4 stroke-[1.5]"></i>
                        </a>
                    </div>
                </div>
                
                <div class="text-center">
                    <p class="text-slate-400 text-[10px] uppercase font-bold tracking-[0.2em] mb-2"><?= htmlspecialchars($product['brand_name'] ?? 'Optilux') ?></p>
                    <a href="/Optilux/product.php?id=<?= $product['id'] ?>">
                        <h3 class="font-serif text-primary text-lg mb-3 tracking-wider group-hover:text-accent transition duration-300"><?= htmlspecialchars($product['name']) ?></h3>
                    </a>
                    <div class="flex justify-center items-center font-light text-sm tracking-widest text-primary/80">
                        <?php if ($product['sale_price']): ?>
                            <span class="text-accent mr-3"><?= formatPrice($product['sale_price']) ?></span>
                            <span class="text-slate-300 line-through text-xs"><?= formatPrice($product['price']) ?></span>
                        <?php else: ?>
                            <span><?= formatPrice($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($products)): ?>
            <div class="py-24 text-center">
                <i data-lucide="scan-line" class="w-12 h-12 mx-auto text-slate-200 mb-6 stroke-[1]"></i>
                <h3 class="text-xl font-serif tracking-widest text-primary uppercase mb-2">No Products Found</h3>
                <p class="text-slate-400 text-xs tracking-wider uppercase mb-8">We couldn't find any products matching your selection.</p>
                <a href="/Optilux/shop.php" class="border border-primary text-primary hover:bg-primary hover:text-white transition-all duration-300 px-8 py-3 text-[10px] tracking-[0.2em] font-semibold uppercase">View All Products</a>
            </div>
        <?php endif; ?>

        <!-- Pagination Minimal -->
        <?php if ($total_pages > 1): ?>
        <div class="mt-20 flex justify-center gap-4">
            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <a href="?page=<?= $i ?><?= $category_id ? '&category_id='.$category_id : '' ?><?= $sort ? '&sort='.$sort : '' ?>" 
                   class="w-8 h-8 flex items-center justify-center text-xs tracking-widest font-medium transition duration-300 <?= $i == $page ? 'border-b-2 border-primary text-primary' : 'text-slate-400 hover:text-primary' ?>">
                   <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
