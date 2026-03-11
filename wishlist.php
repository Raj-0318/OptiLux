<?php
require_once __DIR__ . '/includes/header.php';

// Handle Add/Remove/Toggle
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $product_id = (int)$_GET['id'];
    $redirect = $_GET['redirect'] ?? '/Optilux/wishlist.php';
    $is_ajax = isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
    
    $response = ['success' => false, 'message' => ''];

    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        
        if ($action === 'toggle') {
            $stmt_check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt_check->execute([$user_id, $product_id]);
            if ($stmt_check->fetch()) {
                $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$user_id, $product_id]);
                $response = ['success' => true, 'status' => 'removed'];
            } else {
                $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $product_id]);
                $response = ['success' => true, 'status' => 'added'];
            }
        } elseif ($action === 'add') {
            $stmt_check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt_check->execute([$user_id, $product_id]);
            if (!$stmt_check->fetch()) {
                $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $product_id]);
            }
            $response = ['success' => true, 'status' => 'added'];
        } elseif ($action === 'remove') {
            $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            $response = ['success' => true, 'status' => 'removed'];
        }
    } else {
        if ($is_ajax) {
            $response = ['success' => false, 'redirect' => '/Optilux/login.php'];
        } else {
            $_SESSION['redirect_after_login'] = $redirect;
            header("Location: /Optilux/login.php");
            exit;
        }
    }

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    header("Location: " . $redirect);
    exit;
}

$items = [];
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT p.*, w.id as wishlist_id, b.name as brand_name
                            FROM wishlist w 
                            JOIN products p ON w.product_id = p.id 
                            LEFT JOIN brands b ON p.brand_id = b.id
                            WHERE w.user_id = ?");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll();
} else {
    $_SESSION['redirect_after_login'] = '/Optilux/wishlist.php';
    header("Location: /Optilux/login.php");
    exit;
}
?>

<div class="border-b border-black/5 py-4">
    <div class="max-w-7xl mx-auto px-6 text-[10px] uppercase tracking-widest font-semibold text-slate-400">
        <a href="/Optilux/" class="hover:text-primary transition duration-300">Optilux</a> / 
        <span class="text-primary">Wishlist</span>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-20">
    <h1 class="text-4xl md:text-5xl font-serif tracking-widest uppercase mb-12 text-primary">My Wishlist</h1>

    <?php if (empty($items)): ?>
        <div class="text-center py-32 bg-slate-50 border border-black/5">
            <i data-lucide="heart" class="w-12 h-12 mx-auto text-slate-300 mb-6 stroke-[1]"></i>
            <h2 class="text-2xl font-serif text-primary mb-4 tracking-widest uppercase">Your wishlist is empty</h2>
            <p class="text-slate-400 text-xs tracking-wider uppercase mb-8 max-w-md mx-auto">Save your favorite pieces here to easily find them later.</p>
            <a href="/Optilux/shop.php" class="inline-block bg-primary text-white hover:bg-black transition-all duration-300 px-8 py-4 text-[10px] tracking-[0.2em] font-semibold uppercase">
                Explore Collection
            </a>
        </div>
    <?php else: ?>
        <div class="w-full">
            <div class="border-t border-black/10">
                <div class="flex justify-between items-center py-4 border-b border-black/10 text-[10px] font-bold text-slate-400 uppercase tracking-widest hidden md:flex">
                    <div class="w-3/5">Product</div>
                    <div class="w-2/5 text-right">Actions</div>
                </div>
                
                <?php foreach($items as $product): ?>
                <div data-wishlist-item class="flex flex-col md:flex-row items-center border-b border-black/5 py-8 gap-8 relative group transition-all duration-500">
                    <a href="javascript:void(0)" onclick="toggleWishlist(<?= $product['id'] ?>, this)" class="absolute top-8 right-0 md:relative md:top-auto md:right-auto text-slate-300 hover:text-primary transition duration-300" title="Remove">
                        <i data-lucide="x" class="w-5 h-5 stroke-[1]"></i>
                    </a>
                    
                    <div class="w-full md:w-3/5 flex items-center gap-6">
                        <?php 
                            $img = !empty($product['image']) ? $product['image'] : 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?auto=format&fit=crop&q=80&w=200';
                            if ($img && strpos($img, 'http') !== 0 && strpos($img, '/') !== 0) {
                                $img = '/Optilux/' . $img;
                            }
                        ?>
                        <a href="/Optilux/product.php?id=<?= $product['id'] ?>" class="w-32 h-32 bg-[#F7F7F7] flex-shrink-0 overflow-hidden">
                            <img src="<?= $img ?>" class="w-full h-full object-cover mix-blend-multiply transition duration-500 group-hover:scale-105" onerror="this.src='https://images.unsplash.com/photo-1572635196237-14b3f281503f?auto=format&fit=crop&q=80&w=200'">
                        </a>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em] mb-2"><?= htmlspecialchars($product['brand_name'] ?? 'Optilux') ?></p>
                            <h3 class="font-serif text-primary tracking-wider text-xl mb-2"><a href="/Optilux/product.php?id=<?= $product['id'] ?>" class="hover:text-accent transition duration-300"><?= htmlspecialchars($product['name']) ?></a></h3>
                            <div class="flex items-center gap-3">
                                <?php if ($product['sale_price']): ?>
                                    <span class="text-accent font-light tracking-widest"><?= formatPrice($product['sale_price']) ?></span>
                                    <span class="text-slate-300 line-through text-xs"><?= formatPrice($product['price']) ?></span>
                                <?php else: ?>
                                    <p class="text-primary font-light tracking-widest"><?= formatPrice($product['price']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-2/5 flex flex-col md:flex-row items-center justify-center md:justify-end gap-6">
                        <a href="javascript:void(0)" onclick="toggleWishlist(<?= $product['id'] ?>, this)" class="text-[10px] font-bold text-slate-400 hover:text-rose-500 uppercase tracking-widest transition-colors duration-300">
                            Remove from Wishlist
                        </a>
                        <a href="/Optilux/cart.php?action=add&id=<?= $product['id'] ?>" class="bg-primary text-white hover:bg-black transition-all duration-300 uppercase tracking-[0.2em] font-semibold text-[10px] px-8 py-4 border border-primary">
                            Move to Cart
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-12 text-center">
                <a href="/Optilux/shop.php" class="inline-block text-slate-400 hover:text-primary transition duration-300 text-[10px] uppercase tracking-[0.3em] font-bold border-b border-black/10 hover:border-primary pb-2">
                    Continue Shopping
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


