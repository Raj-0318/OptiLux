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
?>

<div class="max-w-7xl mx-auto px-4 py-12 flex flex-col md:flex-row gap-8">
    <!-- Sidebar -->
    <div class="w-full md:w-64 flex-shrink-0">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 sticky top-24">
            <nav class="space-y-1">
                <a href="/Optilux/account.php" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 hover:text-accent rounded-xl transition">
                    <i data-lucide="user" class="w-5 h-5"></i> Profile
                </a>
                <a href="/Optilux/orders.php" class="flex items-center gap-3 px-4 py-3 bg-slate-50 text-accent font-bold rounded-xl border border-slate-100 transition">
                    <i data-lucide="package" class="w-5 h-5"></i> My Orders
                </a>
                <a href="/Optilux/wishlist.php" class="flex items-center justify-between px-4 py-3 text-slate-600 hover:bg-slate-50 hover:text-accent rounded-xl transition">
                    <div class="flex items-center gap-3"><i data-lucide="heart" class="w-5 h-5"></i> Wishlist</div>
                    <span class="bg-slate-100 text-slate-600 text-xs px-2 py-1 rounded-md font-bold"><?= $wishlist_count ?></span>
                </a>
                <a href="/Optilux/logout.php" class="flex items-center gap-3 px-4 py-3 text-rose-500 hover:bg-rose-50 rounded-xl transition mt-4">
                    <i data-lucide="log-out" class="w-5 h-5"></i> Logout
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow space-y-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <h2 class="text-2xl font-bold text-primary mb-6">Order History</h2>
            
            <?php if (empty($orders)): ?>
                <div class="text-center py-12 text-slate-500 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                    <i data-lucide="package-search" class="w-16 h-16 mx-auto mb-4 text-slate-300"></i>
                    <p class="text-lg mb-2">You haven't placed any orders yet.</p>
                    <p class="text-sm">When you place an order, it will appear here.</p>
                    <a href="/Optilux/shop.php" class="btn-primary inline-block mt-6">Start Shopping</a>
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach($orders as $order): ?>
                        <div class="border border-slate-200 rounded-xl p-6 hover:shadow-md transition">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-100 pb-4 mb-4">
                                <div>
                                    <h3 class="font-bold text-lg text-primary">Order #ORD-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></h3>
                                    <p class="text-sm text-slate-500">Placed on <?= date('F j, Y, g:i a', strtotime($order['created_at'])) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-xl text-primary"><?= formatPrice($order['total']) ?></p>
                                    <?php 
                                        $statusClass = 'bg-slate-100 text-slate-600';
                                        if ($order['status'] == 'pending') $statusClass = 'bg-amber-100 text-amber-700 border border-amber-200';
                                        if ($order['status'] == 'processing') $statusClass = 'bg-blue-100 text-blue-700 border border-blue-200';
                                        if ($order['status'] == 'shipped') $statusClass = 'bg-indigo-100 text-indigo-700 border border-indigo-200';
                                        if ($order['status'] == 'delivered') $statusClass = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                                        if ($order['status'] == 'cancelled') $statusClass = 'bg-rose-100 text-rose-700 border border-rose-200';
                                    ?>
                                    <span class="mt-2 inline-block px-3 py-1 rounded-full text-xs font-bold capitalize <?= $statusClass ?>"><?= htmlspecialchars($order['status']) ?></span>
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
                            
                            <div class="space-y-4">
                                <?php foreach($items as $item): ?>
                                    <div class="flex items-center gap-4">
                                        <?php 
                                            $img = !empty($item['image']) ? $item['image'] : '';
                                            if ($img && strpos($img, 'http') !== 0 && strpos($img, '/') !== 0) {
                                                $img = '/Optilux/uploads/products/' . basename($img);
                                            }
                                        ?>
                                        <div class="flex items-center justify-center p-2">
                                            <img src="<?= $img ?>" class="max-w-full max-h-full object-contain mix-blend-multiply" onerror="this.src=''">
                                        </div>
                                        <div class="flex-grow">
                                            <h4 class="font-bold text-slate-800 text-sm md:text-base"><?= htmlspecialchars($item['name']) ?></h4>
                                            <p class="text-sm text-slate-500">Qty: <?= $item['quantity'] ?> × <?= formatPrice($item['price']) ?></p>
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


