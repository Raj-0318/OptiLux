<?php
require_once __DIR__ . '/includes/header.php';

// Fetch Professional Stats
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_categories = $conn->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Recent Activity (Example: Latest Products & Orders)
$recent_products = $conn->query("SELECT name, created_at FROM products ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<div class="mb-10">
    <h1 class="text-2xl font-bold font-serif-lux tracking-widest uppercase italic text-brand">Admin Dashboard</h1>
    <p class="text-[10px] text-muted uppercase tracking-[0.3em] mt-1 font-semibold">Store performance and management overview</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8 mb-12">
    <!-- Stat Cards -->
    <div class="glass-card p-6 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div class="w-12 h-12 rounded-xl bg-brand/10 flex items-center justify-center text-brand">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <span class="text-[10px] font-bold text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded-full uppercase tracking-widest">+12%</span>
        </div>
        <div class="mt-6">
            <h3 class="text-muted text-[10px] font-bold uppercase tracking-[0.2em] mb-1">Total Products</h3>
            <p class="text-3xl font-bold font-serif-lux"><?= $total_products ?></p>
        </div>
    </div>

    <div class="glass-card p-6 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400">
                <i data-lucide="shopping-cart" class="w-6 h-6"></i>
            </div>
            <span class="text-[10px] font-bold text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded-full uppercase tracking-widest">+5%</span>
        </div>
        <div class="mt-6">
            <h3 class="text-muted text-[10px] font-bold uppercase tracking-[0.2em] mb-1">Total Orders</h3>
            <p class="text-3xl font-bold font-serif-lux"><?= $total_orders ?></p>
        </div>
    </div>

    <div class="glass-card p-6 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-400">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <span class="text-[10px] font-bold text-rose-500 bg-rose-500/10 px-2 py-1 rounded-full uppercase tracking-widest">-2%</span>
        </div>
        <div class="mt-6">
            <h3 class="text-muted text-[10px] font-bold uppercase tracking-[0.2em] mb-1">Total Users</h3>
            <p class="text-3xl font-bold font-serif-lux"><?= $total_users ?></p>
        </div>
    </div>

    <div class="glass-card p-6 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <span class="text-[10px] font-bold text-brand bg-brand/10 px-2 py-1 rounded-full uppercase tracking-widest">Steady</span>
        </div>
        <div class="mt-6">
            <h3 class="text-muted text-[10px] font-bold uppercase tracking-[0.2em] mb-1">Total Categories</h3>
            <p class="text-3xl font-bold font-serif-lux"><?= $total_categories ?></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
    <!-- Recent Products -->
    <div class="lg:col-span-2 glass-card overflow-hidden">
        <div class="p-8 border-b border-border flex justify-between items-center bg-white/5">
            <div>
                <h3 class="text-sm font-bold uppercase tracking-widest">Recent Products</h3>
                <p class="text-[10px] text-muted uppercase mt-1">Latest items added to the catalog</p>
            </div>
            <a href="products.php" class="text-brand text-[10px] font-bold uppercase tracking-[0.2em] hover:underline">View All</a>
        </div>
        <div class="p-0">
            <table class="w-full text-left">
                <tbody>
                    <?php foreach($recent_products as $p): ?>
                    <tr class="border-b border-border last:border-0 hover:bg-white/5 transition-all">
                        <td class="px-8 py-5">
                            <p class="text-sm font-bold"><?= htmlspecialchars(substr($p['name'], 0, 40)) ?></p>
                            <p class="text-[10px] text-muted font-bold font-serif-lux uppercase tracking-widest">Added on: <?= date('M j, Y', strtotime($p['created_at'])) ?></p>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <a href="edit-product.php?id=<?= rand(1, 10) ?>" class="text-brand text-[10px] font-bold uppercase tracking-widest hover:underline">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions & Alerts -->
    <div class="space-y-8">
        <div class="glass-card p-8 bg-brand/5 border-brand/20">
            <h3 class="text-xs font-bold uppercase tracking-widest mb-6 flex items-center gap-2">
                <i data-lucide="zap" class="w-4 h-4 text-brand"></i> Important Updates
            </h3>
            <div class="space-y-6">
                <div class="flex items-start gap-4 p-4 rounded-xl bg-white/5 border border-border">
                    <div class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Low Stock Warning</p>
                        <p class="text-[10px] text-muted mt-1 leading-relaxed">Some items are running low on stock.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-4 rounded-xl bg-white/5 border border-border">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Sales Growth</p>
                        <p class="text-[10px] text-muted mt-1 leading-relaxed">Weekly sales have increased by 40%.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-card p-8">
            <h3 class="text-xs font-bold uppercase tracking-widest mb-6">Quick Notes</h3>
            <ul class="space-y-4 text-[10px] text-muted uppercase font-bold tracking-[0.15em]">
                <li class="flex items-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-brand"></div>
                    Keep product prices updated.
                </li>
                <li class="flex items-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-brand"></div>
                    Check new orders daily.
                </li>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
