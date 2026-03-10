<?php
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    echo "<script>window.location.href='/shop.php';</script>";
    exit;
}

// Fetch Product
$stmt = $conn->prepare("SELECT p.*, b.name as brand_name, c.name as category_name 
                        FROM products p 
                        LEFT JOIN brands b ON p.brand_id = b.id 
                        LEFT JOIN categories c ON p.category_id = c.id
                        WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='max-w-7xl mx-auto px-6 py-32 text-center'><h2 class='text-2xl font-serif text-primary tracking-widest uppercase mb-6'>Product Not Found</h2><a href='/Optilux/shop.php' class='border border-primary text-primary hover:bg-primary hover:text-white transition-all duration-300 px-8 py-3 text-[10px] tracking-[0.2em] font-semibold uppercase'>Return to Shop</a></div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Fetch Images
$stmt_images = $conn->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC");
$stmt_images->execute([$id]);
$images = $stmt_images->fetchAll();

// Fetch Related Products (same category)
$stmt_related = $conn->prepare("SELECT p.*, b.name as brand_name 
                            FROM products p 
                            LEFT JOIN brands b ON p.brand_id = b.id 
                            WHERE p.category_id = ? AND p.id != ? LIMIT 4");
$stmt_related->execute([$product['category_id'], $id]);
$related = $stmt_related->fetchAll();

$main_image = !empty($product['image']) ? $product['image'] : '';
if ($main_image && strpos($main_image, 'http') !== 0 && strpos($main_image, '/') !== 0) {
    $main_image = '/Optilux/' . $main_image;
}
?>

<div class="border-b border-black/5 py-4">
    <div class="max-w-7xl mx-auto px-6 text-[10px] uppercase tracking-widest font-semibold text-slate-400">
        <a href="/Optilux/" class="hover:text-primary transition duration-300">Optilux</a> / 
        <a href="/Optilux/shop.php" class="hover:text-primary transition duration-300">Shop</a> / 
        <a href="/Optilux/shop.php?category_id=<?= $product['category_id'] ?>" class="hover:text-primary transition duration-300"><?= htmlspecialchars($product['category_name']) ?></a> / 
        <span class="text-primary"><?= htmlspecialchars($product['name']) ?></span>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-20">
    <div class="flex flex-col md:flex-row gap-16 lg:gap-24">
        <!-- Image Gallery Minimal -->
        <div class="w-full md:w-1/2">
            <div class="bg-slate-50 relative h-[600px] flex items-center justify-center mb-6 overflow-hidden group border border-black/[0.03]">
                <?php if ($product['sale_price']): ?>
                    <span class="absolute top-6 left-6 bg-primary text-white text-[9px] uppercase tracking-[0.2em] px-3 py-1 font-bold z-20">Sale</span>
                <?php endif; ?>
                <img id="mainImage" src="<?= $main_image ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover transition-transform duration-1000 ease-in-out group-hover:scale-110">
            </div>
            <?php if (count($images) > 1): ?>
            <div class="grid grid-cols-4 gap-4">
                <?php foreach($images as $img): ?>
                    <div class="aspect-square bg-slate-50 p-2 cursor-pointer border-b-2 border-transparent hover:border-primary transition duration-300 flex items-center justify-center" onclick="document.getElementById('mainImage').src='/uploads/products/<?= $img['image_path'] ?>'">
                        <img src="/uploads/products/<?= $img['image_path'] ?>" class="w-full h-full object-cover mix-blend-multiply opacity-70 hover:opacity-100 transition duration-300">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Details Couture -->
        <div class="w-full md:w-1/2 flex flex-col justify-center">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-[0.3em] mb-4"><?= htmlspecialchars($product['brand_name'] ?? 'Optilux') ?></h2>
            <h1 class="text-4xl md:text-5xl font-serif text-primary tracking-wider mb-8"><?= htmlspecialchars($product['name']) ?></h1>
            
            <div class="text-2xl font-light tracking-widest text-primary mb-10 pb-10 border-b border-black/10">
                <?php if ($product['sale_price']): ?>
                    <span class="text-accent"><?= formatPrice($product['sale_price']) ?></span>
                    <span class="text-slate-300 line-through text-sm ml-4"><?= formatPrice($product['price']) ?></span>
                <?php else: ?>
                    <span><?= formatPrice($product['price']) ?></span>
                <?php endif; ?>
            </div>

            <div class="prose prose-sm prose-slate mb-12 max-w-none text-slate-500 font-light leading-relaxed">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>

            <!-- Pre-order / Stock minimal display -->
            <div class="mb-10 text-[10px] uppercase tracking-[0.2em] font-semibold flex items-center gap-2 <?= $product['stock'] > 0 ? 'text-primary' : 'text-slate-400' ?>">
                <div class="w-2 h-2 rounded-full <?= $product['stock'] > 0 ? 'bg-primary' : 'bg-slate-300' ?>"></div>
                <?= $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
            </div>

            <form action="/Optilux/cart.php" method="GET" class="flex flex-col sm:flex-row gap-4 mb-16">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                
                <div class="flex items-center gap-6 border-b border-black/10 pb-2 px-1 mb-4 sm:mb-0">
                    <button type="button" class="text-primary transition-colors duration-300 transform active:scale-90" onclick="this.nextElementSibling.stepDown()">
                        <i data-lucide="minus" class="w-4 h-4 stroke-[1.5]"></i>
                    </button>
                    <input type="number" name="qty" value="1" min="1" max="<?= max(1, $product['stock']) ?>" class="w-10 text-center font-serif text-2xl bg-transparent focus:outline-none text-primary appearance-none cursor-default" readonly <?= $product['stock'] < 1 ? 'disabled' : '' ?>>
                    <button type="button" class="text-primary transition-colors duration-300 transform active:scale-90" onclick="this.previousElementSibling.stepUp()">
                        <i data-lucide="plus" class="w-4 h-4 stroke-[1.5]"></i>
                    </button>
                </div>
                
                <button type="submit" class="flex-grow bg-primary text-white hover:bg-black transition-all duration-300 uppercase tracking-[0.2em] font-semibold text-xs py-4 flex justify-center items-center gap-3 border border-primary" <?= $product['stock'] < 1 ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : '' ?>>
                    <i data-lucide="shopping-bag" class="w-4 h-4 stroke-[1.5]"></i> Add to Cart
                </button>
                
                <a href="/Optilux/wishlist.php?action=add&id=<?= $product['id'] ?>" class="h-[54px] w-[54px] flex-shrink-0 flex items-center justify-center border border-black/10 hover:border-black hover:bg-primary hover:text-white transition duration-300 group">
                    <i data-lucide="heart" class="w-4 h-4 stroke-[1.5] text-primary group-hover:text-white transition duration-300"></i>
                </a>
            </form>

            <!-- Specs list Minimal -->
            <div class="border-t border-black/10 pt-10">
                <h3 class="font-serif text-lg tracking-widest uppercase mb-6 text-primary">Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-xs">
                    <div class="flex flex-col border-b border-black/5 pb-2">
                        <dt class="text-slate-400 uppercase tracking-widest font-semibold mb-1 text-[9px]">Material</dt>
                        <dd class="text-primary font-medium tracking-wide"><?= htmlspecialchars($product['frame_material'] ?: 'N/A') ?></dd>
                    </div>
                    <div class="flex flex-col border-b border-black/5 pb-2">
                        <dt class="text-slate-400 uppercase tracking-widest font-semibold mb-1 text-[9px]">Lenses</dt>
                        <dd class="text-primary font-medium tracking-wide"><?= htmlspecialchars($product['lens_type'] ?: 'N/A') ?></dd>
                    </div>
                    <div class="flex flex-col border-b border-black/5 pb-2">
                        <dt class="text-slate-400 uppercase tracking-widest font-semibold mb-1 text-[9px]">Style</dt>
                        <dd class="text-primary font-medium tracking-wide capitalize"><?= htmlspecialchars($product['gender'] ?: 'Unisex') ?></dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Recommendations (Similar pieces) -->
<?php if (!empty($related)): ?>
<section class="py-24 bg-[#0A0A0A] border-t border-white/5 mt-12">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <span class="text-accent text-[10px] font-semibold tracking-[0.3em] uppercase mb-4 block">Recommended For You</span>
            <h2 class="text-3xl font-serif text-white tracking-widest uppercase">Similar Products</h2>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-16">
            <?php foreach($related as $prod): ?>
                <div class="group flex flex-col">
                <div class="relative w-full aspect-square bg-[#F7F7F7] mb-6 overflow-hidden group">
                    <?php $img = !empty($prod['image']) ? $prod['image'] : ''; ?>
                    <a href="/Optilux/product.php?id=<?= $prod['id'] ?>" class="block w-full h-full flex items-center justify-center">
                         <img src="<?= $img ?>" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700 ease-in-out mix-blend-screen">
                    </a>
                </div>
                <div class="text-center">
                    <p class="text-white/40 text-[10px] uppercase font-bold tracking-[0.2em] mb-2"><?= htmlspecialchars($prod['brand_name'] ?? 'Optilux') ?></p>
                    <a href="/Optilux/product.php?id=<?= $prod['id'] ?>">
                        <h3 class="font-serif text-white text-md mb-2 tracking-wider group-hover:text-accent transition duration-300"><?= htmlspecialchars($prod['name']) ?></h3>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
