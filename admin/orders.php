<?php
require_once __DIR__ . '/includes/header.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $id])) {
        $msg = "Order #$id status successfully transitioned to $status.";
    }
}

$orders = $conn->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();
?>

<div class="glass-card overflow-hidden">
    <div class="p-10 border-b border-border bg-white/5 flex justify-between items-center bg-gradient-to-r from-white/5 to-transparent">
        <div>
            <h1 class="text-xl font-bold font-serif-lux tracking-widest uppercase">All Orders</h1>
            <p class="text-[9px] text-muted uppercase tracking-[0.2em] mt-1 font-semibold"><?= count($orders) ?> Total Orders</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead>
                <tr class="text-muted text-[10px] uppercase tracking-[0.2em] border-b border-border">
                    <th class="px-8 py-6 font-bold">Trace ID</th>
                    <th class="px-8 py-6 font-bold">Patron</th>
                    <th class="px-8 py-6 font-bold">Established Date</th>
                    <th class="px-8 py-6 font-bold">Valuation</th>
                    <th class="px-8 py-6 font-bold">Settlement</th>
                    <th class="px-8 py-6 font-bold text-right">Status Control</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium tracking-wide text-main">
                <?php foreach($orders as $order): ?>
                <tr class="border-b border-border last:border-0 hover:bg-white/5 transition-all">
                    <td class="px-8 py-6 text-muted font-bold tracking-widest">#ORD-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></td>
                    <td class="px-8 py-6">
                        <span class="font-bold text-sm tracking-tight block"><?= htmlspecialchars($order['user_name']) ?></span>
                        <span class="text-[9px] text-muted uppercase tracking-widest font-bold">Registered Patron</span>
                    </td>
                    <td class="px-8 py-6 text-muted font-semibold"><?= date('M j, Y | h:i A', strtotime($order['created_at'])) ?></td>
                    <td class="px-8 py-6">
                        <p class="font-bold text-accent">₹<?= number_format($order['total'], 2) ?></p>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-500 text-[9px] font-bold uppercase tracking-widest border border-emerald-500/20">
                            <?= strtoupper($order['payment_method']) ?>
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <form method="POST" class="inline">
                            <input type="hidden" name="id" value="<?= $order['id'] ?>">
                            <select name="status" class="pro-input !w-auto !py-2 !text-[10px] font-bold uppercase tracking-widest cursor-pointer <?= $order['status'] == 'delivered' ? 'text-emerald-500 border-emerald-500/20' : ($order['status'] == 'cancelled' ? 'text-rose-500 border-rose-500/20' : 'text-amber-500 border-amber-500/20') ?>" onchange="this.form.submit()">
                                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($orders)): ?>
                    <tr><td colspan="6" class="px-8 py-10 text-center text-muted uppercase font-bold tracking-[0.2em]">No order records found in registry.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
