<?php
require_once __DIR__ . '/includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Get wishlist count for sidebar
$stmt_wishlist = $conn->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
$stmt_wishlist->execute([$user_id]);
$wishlist_count = $stmt_wishlist->fetch()['count'];

// Fetch all orders
$stmt_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt_orders->execute([$user_id]);
$orders = $stmt_orders->fetchAll();

$user_stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user_data = $user_stmt->fetch();
?>

<!-- Orders Cinematic Hero -->
<div class="bg-primary text-white py-20 border-b border-white/5 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div>
                <span class="text-accent text-[10px] font-bold tracking-[0.4em] uppercase mb-4 block">Personal Workspace</span>
                <h1 class="text-4xl md:text-6xl font-serif tracking-widest uppercase leading-tight">Order Chronicle</h1>
            </div>
            <div class="text-right">
                <p class="text-white/40 text-[10px] font-bold tracking-[0.2em] uppercase mb-2">Authenticated as</p>
                <p class="text-xl font-serif text-accent tracking-wider"><?= htmlspecialchars($user_data['name'] ?? 'Member') ?></p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-20">
    <div class="flex flex-col lg:flex-row gap-20">
        <!-- Unified Sidebar Navigation -->
        <div class="w-full lg:w-64 flex-shrink-0">
            <div class="bg-white border border-black/5 p-8 sticky top-32 shadow-2xl">
                <nav class="space-y-6">
                    <p class="text-[9px] font-black tracking-[0.4em] text-slate-300 uppercase mb-8">Navigation</p>
                    
                    <a href="/Optilux/account.php" class="group flex items-center justify-between py-2 border-b border-transparent hover:border-black/10 transition-all duration-500">
                        <span class="text-[11px] font-serif tracking-[0.2em] uppercase text-slate-400 group-hover:text-primary transition-colors">Profile Details</span>
                        <i data-lucide="user" class="w-3 h-3 text-slate-300 group-hover:text-accent transition-colors"></i>
                    </a>
                    
                    <a href="/Optilux/orders.php" class="group flex items-center justify-between py-2 border-b border-primary transition-all duration-500">
                        <span class="text-[11px] font-serif tracking-[0.2em] uppercase text-primary font-bold">Order History</span>
                        <div class="w-1.5 h-1.5 bg-accent rounded-full shadow-[0_0_10px_rgba(251,191,36,0.6)]"></div>
                    </a>
                    
                    <a href="/Optilux/wishlist.php" class="group flex items-center justify-between py-2 border-b border-transparent hover:border-black/10 transition-all duration-500">
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-serif tracking-[0.2em] uppercase text-slate-400 group-hover:text-primary transition-colors">Wishlist</span>
                            <span class="text-[8px] font-bold bg-slate-50 text-slate-400 px-1.5 py-0.5 border border-black/5"><?= $wishlist_count ?></span>
                        </div>
                        <i data-lucide="heart" class="w-3 h-3 text-slate-300 group-hover:text-rose-400 transition-colors"></i>
                    </a>
                    
                    <a href="/Optilux/logout.php" class="group flex items-center justify-between py-2 mt-8 opacity-60 hover:opacity-100 transition-opacity">
                        <span class="text-[10px] font-bold tracking-[0.2em] uppercase text-rose-500">Secure Logout</span>
                        <i data-lucide="power" class="w-3 h-3 text-rose-500"></i>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content Area: Elite Ledger -->
        <div class="flex-grow">
            <header class="mb-12 border-b border-black/10 pb-4 flex items-center justify-between">
                <h2 class="text-xl font-serif text-primary tracking-widest uppercase">The Ledger</h2>
                <span class="text-[9px] font-bold tracking-widest text-slate-400 uppercase"><?= count($orders) ?> Recorded Acquisitions</span>
            </header>
            
            <?php if (empty($orders)): ?>
                <div class="text-center py-32 bg-slate-50 border border-black/5">
                    <i data-lucide="package" class="w-12 h-12 mx-auto text-slate-200 mb-6 stroke-[1]"></i>
                    <h3 class="text-2xl font-serif text-primary mb-4 tracking-widest uppercase">The record is void</h3>
                    <p class="text-slate-400 text-xs tracking-wider uppercase mb-8 max-w-sm mx-auto leading-relaxed">Your journey with Optilux is just beginning. Begin your collection today.</p>
                    <a href="/Optilux/shop.php" class="inline-block bg-primary text-white hover:bg-black transition-all duration-300 px-10 py-5 text-[10px] tracking-[0.2em] font-semibold uppercase">
                        Start Curation
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-12">
                    <?php foreach($orders as $order): ?>
                        <div class="bg-white border border-black/5 p-8 shadow-sm group hover:shadow-2xl transition-all duration-700 animate-in fade-in slide-in-from-bottom-4">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-black/5 pb-8 mb-8">
                                <div>
                                    <h3 class="font-serif text-2xl text-primary tracking-wider mb-2">ORD-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></h3>
                                    <p class="text-[9px] font-bold text-slate-400 tracking-[0.3em] uppercase italic"><?= date('F d, Y • g:i A', strtotime($order['created_at'])) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-serif text-2xl text-primary mb-3"><?= formatPrice($order['total']) ?></p>
                                    <?php 
                                        $statusConfig = [
                                            'pending' => ['bg' => 'bg-amber-400/10', 'text' => 'text-amber-600', 'dot' => 'bg-amber-400'],
                                            'processing' => ['bg' => 'bg-blue-400/10', 'text' => 'text-blue-600', 'dot' => 'bg-blue-400'],
                                            'shipped' => ['bg' => 'bg-indigo-400/10', 'text' => 'text-indigo-600', 'dot' => 'bg-indigo-400'],
                                            'delivered' => ['bg' => 'bg-emerald-400/10', 'text' => 'text-emerald-600', 'dot' => 'bg-emerald-400'],
                                            'cancelled' => ['bg' => 'bg-rose-400/10', 'text' => 'text-rose-600', 'dot' => 'bg-rose-400'],
                                        ];
                                        $cfg = $statusConfig[$order['status']] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'dot' => 'bg-slate-400'];
                                    ?>
                                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full <?= $cfg['bg'] ?> <?= $cfg['text'] ?> text-[9px] font-bold tracking-widest uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $cfg['dot'] ?> shadow-[0_0_8px_rgba(0,0,0,0.1)]"></span>
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Fetch Order Items -->
                            <?php 
                            $stmt_items = $conn->prepare("SELECT oi.*, p.name, p.image 
                                        FROM order_items oi 
                                        JOIN products p ON oi.product_id = p.id 
                                        WHERE oi.order_id = ?");
                            $stmt_items->execute([$order['id']]);
                            $items = $stmt_items->fetchAll();
                            ?>
                            
                            <div class="space-y-6">
                                <?php foreach($items as $item): ?>
                                    <div class="flex items-center gap-8 group/item">
                                        <?php 
                                            $img = !empty($item['image']) ? $item['image'] : '';
                                            if ($img && strpos($img, 'http') !== 0 && strpos($img, '/') !== 0) {
                                                $img = '/Optilux/uploads/products/' . basename($img);
                                            }
                                        ?>
                                        <div class="w-24 h-24 bg-slate-50 flex-shrink-0 overflow-hidden border border-black/[0.03]">
                                            <img src="<?= $img ?>" class="w-full h-full object-cover mix-blend-multiply transition duration-700 group-hover/item:scale-110" onerror="this.src=''">
                                        </div>
                                        <div class="flex-grow">
                                            <h4 class="font-serif text-lg text-primary tracking-wide mb-1 group-hover/item:text-accent transition-colors"><?= htmlspecialchars($item['name']) ?></h4>
                                            <div class="flex items-center gap-4 text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                                <span>Quantity: <?= $item['quantity'] ?></span>
                                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                                <span class="text-primary"><?= formatPrice($item['price']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>



