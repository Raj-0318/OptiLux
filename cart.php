<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Handle Cart Actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // For now we assume a user must be logged in to use the cart properly based on earlier prompt.
    // If not logged in, redirect to login
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = "/Optilux/cart.php?action=$action&id=$product_id";
        header("Location: /Optilux/login.php");
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    if ($action === 'add' && $product_id) {
        $qty = isset($_GET['qty']) ? max(1, (int)$_GET['qty']) : 1;
        // Check if exists
        $stmt_check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt_check->execute([$user_id, $product_id]);
        $existing = $stmt_check->fetch();
        
        if ($existing) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
            $stmt->execute([$qty, $existing['id']]);
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $product_id, $qty]);
        }
    } elseif ($action === 'remove' && $product_id) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
    } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach($_POST['qty'] ?? [] as $cid => $q) {
            $q = max(1, (int)$q);
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$q, $cid, $user_id]);
        }
    }
    header("Location: /Optilux/cart.php");
    exit;
}

$cart_items = [];
$subtotal = 0;
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT c.id as cart_id, c.quantity, p.*, b.name as brand_name
                            FROM cart c
                            JOIN products p ON c.product_id = p.id
                            LEFT JOIN brands b ON p.brand_id = b.id
                            WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    foreach($cart_items as $item) {
        $curr_price = $item['sale_price'] ?: $item['price'];
        $subtotal += $curr_price * $item['quantity'];
    }
}
$shipping = $subtotal > 999 || $subtotal == 0 ? 0 : 99; // Free shipping over 999
$total = $subtotal + $shipping;

require_once __DIR__ . '/includes/header.php';
?>

<div class="border-b border-black/5 py-4">
    <div class="max-w-7xl mx-auto px-6 text-[10px] uppercase tracking-widest font-semibold text-slate-400">
        <a href="/Optilux/" class="hover:text-primary transition duration-300">Optilux</a> / 
        <span class="text-primary">Cart</span>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-20">
    <h1 class="text-4xl md:text-5xl font-serif tracking-widest uppercase mb-12 text-primary">Your Cart</h1>

    <?php if (empty($cart_items)): ?>
        <div class="text-center py-32 bg-slate-50 border border-black/5">
            <i data-lucide="shopping-bag" class="w-12 h-12 mx-auto text-slate-300 mb-6 stroke-[1]"></i>
            <h2 class="text-2xl font-serif text-primary mb-4 tracking-widest uppercase">Your cart is empty</h2>
            <p class="text-slate-400 text-xs tracking-wider uppercase mb-8 max-w-md mx-auto">Discover our collection of premium eyewear.</p>
            <a href="/Optilux/shop.php" class="inline-block bg-primary text-white hover:bg-black transition-all duration-300 px-8 py-4 text-[10px] tracking-[0.2em] font-semibold uppercase">
                Shop Now
            </a>
        </div>
    <?php else: ?>
        <div class="flex flex-col lg:flex-row gap-16">
            <!-- Cart Items Minimal -->
            <div class="w-full lg:w-2/3">
                <div class="border-t border-black/10">
                    <div class="flex justify-between items-center py-4 border-b border-black/10 text-[10px] font-bold text-slate-400 uppercase tracking-widest hidden md:flex">
                        <div class="w-3/5">Product</div>
                        <div class="w-1/5 text-center">Quantity</div>
                        <div class="w-1/5 text-right">Total</div>
                    </div>
                    
                    <form action="/Optilux/cart.php?action=update" method="POST" id="cartForm">
                        <?php foreach($cart_items as $item): 
                            $curr_price = $item['sale_price'] ?: $item['price'];
                            $item_total = $curr_price * $item['quantity'];
                        ?>
                        <div class="flex flex-col md:flex-row items-center border-b border-black/[0.05] py-12 gap-10 relative group animate-in slide-in-from-bottom-4 duration-700">
                            <div class="w-full md:w-3/5 flex items-center gap-10">
                                <?php 
                                    $img = !empty($item['image']) ? $item['image'] : '';
                                    if ($img && strpos($img, 'http') !== 0 && strpos($img, '/') !== 0) {
                                        $img = '/Optilux/' . $img;
                                    }
                                ?>
                                <a href="/Optilux/product.php?id=<?= $item['id'] ?>" class="w-40 h-40 bg-[#F8F9FA] flex-shrink-0 overflow-hidden rounded-2xl group border border-black/[0.02]">
                                    <img src="<?= $img ?>" class="w-full h-full object-cover mix-blend-multiply transition-transform duration-1000 group-hover:scale-110" onerror="this.src=''">
                                </a>
                                <div>
                                    <p class="text-[9px] font-black tracking-[0.4em] text-slate-300 uppercase mb-3 italic"><?= htmlspecialchars($item['brand_name'] ?? 'Maison Selection') ?></p>
                                    <h3 class="font-serif text-primary tracking-tight text-2xl mb-3"><a href="/Optilux/product.php?id=<?= $item['id'] ?>" class="hover:text-accent transition-colors duration-500 uppercase"><?= htmlspecialchars($item['name']) ?></a></h3>
                                    <p class="text-primary/60 font-serif tracking-[0.2em] text-sm mb-6"><?= formatPrice($curr_price) ?></p>
                                    
                                    <a href="/Optilux/cart.php?action=remove&id=<?= $item['id'] ?>" class="inline-flex items-center gap-2 text-[9px] font-bold tracking-[0.3em] uppercase text-primary hover:text-rose-500 transition-colors duration-500">
                                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                                        <span>Remove Item</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Unique Quantity Control Integration -->
                            <div class="w-full md:w-1/5 flex justify-center">
                                <div class="relative group/qty">
                                    <div class="flex items-center gap-6 border-b border-black/10 pb-2 px-1 transition-all duration-500 group-hover/qty:border-black/30">
                                        <button type="button" class="text-primary transition-colors duration-300 transform active:scale-90" onclick="this.nextElementSibling.stepDown(); document.getElementById('cartForm').submit();">
                                            <i data-lucide="minus" class="w-3.5 h-3.5 stroke-[1.5]"></i>
                                        </button>
                                        <input type="number" name="qty[<?= $item['cart_id'] ?>]" value="<?= $item['quantity'] ?>" min="1" max="<?= max(1, $item['stock']) ?>" class="w-8 text-center font-serif text-2xl bg-transparent focus:outline-none text-primary appearance-none cursor-default" readonly>
                                        <button type="button" class="text-primary transition-colors duration-300 transform active:scale-90" onclick="this.previousElementSibling.stepUp(); document.getElementById('cartForm').submit();">
                                            <i data-lucide="plus" class="w-3.5 h-3.5 stroke-[1.5]"></i>
                                        </button>
                                    </div>
                                    <p class="absolute -bottom-6 left-0 right-0 text-center text-[8px] font-black tracking-[0.3em] text-slate-200 uppercase opacity-0 group-hover/qty:opacity-100 transition-opacity duration-500">Adjust</p>
                                </div>
                            </div>
                            
                            <div class="w-full md:w-1/5 text-right font-serif text-2xl text-primary tracking-tighter">
                                <?= formatPrice($item_total) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </form>
                </div>
            </div>
            
            <!-- Order Summary Minimal -->
            <div class="w-full lg:w-1/3">
                <div class="bg-slate-50 p-8 border border-black/5 sticky top-32">
                    <h2 class="text-lg font-serif tracking-widest text-primary uppercase border-b border-black/10 pb-6 mb-6">Summary</h2>
                    
                    <div class="space-y-4 text-xs tracking-widest uppercase font-semibold text-slate-500 border-b border-black/10 pb-8 mb-8">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="text-primary"><?= formatPrice($subtotal) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span class="text-primary"><?= $shipping == 0 ? 'Free' : formatPrice($shipping) ?></span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-end mb-10">
                        <div>
                            <span class="text-xs tracking-widest uppercase font-bold text-primary block mb-1">Total</span>
                            <span class="text-[9px] tracking-wider text-slate-400">VAT Included</span>
                        </div>
                        <span class="text-3xl font-serif text-primary tracking-widest"><?= formatPrice($total) ?></span>
                    </div>
                    
                    <a href="/Optilux/checkout.php" class="block w-full bg-primary text-white hover:bg-black transition-all duration-300 uppercase tracking-[0.2em] font-semibold text-xs py-5 text-center border border-primary">
                        Checkout
                    </a>
                    
                    <div class="mt-8 flex items-center justify-center gap-3 text-slate-400">
                        <i data-lucide="lock" class="w-3 h-3 stroke-[1.5]"></i>
                        <span class="text-[9px] font-bold uppercase tracking-[0.3em]">Secure Checkout</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
