<?php
require_once __DIR__ . '/includes/header.php';
requireLogin();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// Verify order belongs to user
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: /Optilux/orders.php");
    exit;
}

// Fetch Items
$stmt_items = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll();
?>

<div class="max-w-3xl mx-auto px-4 py-20">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 md:p-12 text-center">
        <div class="w-24 h-24 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="check" class="w-12 h-12"></i>
        </div>
        
        <h1 class="text-3xl md:text-4xl font-bold text-primary mb-4">Thank you for your order!</h1>
        <p class="text-slate-500 text-lg mb-8">Your order <span class="font-bold text-primary">#ORD-<?= str_pad($order_id, 5, '0', STR_PAD_LEFT) ?></span> has been placed successfully.</p>
        
        <div class="bg-slate-50 rounded-xl p-6 mb-8 text-left border border-slate-100">
            <h3 class="font-bold text-primary mb-4 border-b pb-2">Order Summary</h3>
            
            <div class="space-y-3 mb-6">
                <?php foreach($items as $item): ?>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-600 flex-grow pr-4"><?= htmlspecialchars($item['name']) ?> <span class="text-slate-400">x <?= $item['quantity'] ?></span></span>
                        <span class="font-bold text-slate-800"><?= formatPrice($item['price'] * $item['quantity']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                <span class="font-bold text-primary text-lg">Total Paid</span>
                <span class="font-bold text-accent text-2xl"><?= formatPrice($order['total']) ?></span>
            </div>
            
            <div class="mt-6 pt-4 border-t border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-600">
                <div>
                    <span class="font-bold text-slate-800 block mb-1">Payment Method:</span>
                    <?= htmlspecialchars($order['payment_method']) ?>
                </div>
                <div>
                    <span class="font-bold text-slate-800 block mb-1">Shipping Address:</span>
                    <?= nl2br(htmlspecialchars($order['address'])) ?>
                </div>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="/Optilux/orders.php" class="border-2 border-primary text-primary hover:bg-primary hover:text-white font-bold py-3 px-8 rounded-xl transition">View Order History</a>
            <a href="/Optilux/shop.php" class="btn-primary">Continue Shopping</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


