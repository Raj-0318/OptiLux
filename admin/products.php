<?php
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $id = $_POST['delete_id'];
        
        try {
            $conn->beginTransaction();
            
            // Clean up related records
            $conn->prepare("DELETE FROM wishlist WHERE product_id = ?")->execute([$id]);
            $conn->prepare("DELETE FROM cart WHERE product_id = ?")->execute([$id]);
            $conn->prepare("DELETE FROM reviews WHERE product_id = ?")->execute([$id]);
            $conn->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$id]);
            $conn->prepare("DELETE FROM order_items WHERE product_id = ?")->execute([$id]); // Cleanup orders to prevent FK error
            
            // Delete the product
            $conn->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
            
            $conn->commit();
            setFlash('success', "Product and all related data have been deleted.");
        } catch (Exception $e) {
            $conn->rollBack();
            setFlash('error', "Could not delete product: " . $e->getMessage());
        }
        
        header("Location: products.php");
        exit;
    }
}

$products = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetchAll();
?>

<div class="glass-card overflow-hidden">
    <div class="p-10 border-b border-border bg-white/5 flex justify-between items-center bg-gradient-to-r from-white/5 to-transparent">
        <div>
            <h1 class="text-xl font-bold font-serif-lux tracking-widest uppercase">All Products</h1>
            <p class="text-[9px] text-muted uppercase tracking-[0.2em] mt-1 font-semibold"><?= count($products) ?> Total Products</p>
        </div>
        <a href="add-product.php" class="bg-brand text-dark font-bold px-6 py-3 rounded-xl hover:opacity-95 transition-all uppercase tracking-[0.2em] text-[10px] shadow-xl shadow-brand/10 flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Product
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-muted text-[10px] uppercase tracking-[0.2em] border-b border-border">
                    <th class="px-8 py-6 font-bold">ID</th>
                    <th class="px-8 py-6 font-bold">Product</th>
                    <th class="px-8 py-6 font-bold">Category</th>
                    <th class="px-8 py-6 font-bold">Price</th>
                    <th class="px-8 py-6 font-bold text-center">Stock</th>
                    <th class="px-8 py-6 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium tracking-wide">
                <?php foreach($products as $prod): ?>
                <tr class="border-b border-border last:border-0 hover:bg-white/5 transition-all group">
                    <td class="px-8 py-6 text-muted font-bold">#PROD-<?= str_pad($prod['id'], 3, '0', STR_PAD_LEFT) ?></td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 rounded-xl bg-white/5 overflow-hidden border border-border flex-shrink-0 p-1">
                                <?php 
                                    $img_src = !empty($prod['image']) ? $prod['image'] : ''; 
                                    if(strpos($img_src, 'http') !== 0) $img_src = '/Optilux/' . $img_src;
                                ?>
                                <img src="<?= $img_src ?>" class="w-full h-full object-cover rounded-lg group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <div>
                                <span class="font-bold text-sm tracking-tight block text-main"><?= htmlspecialchars($prod['name']) ?></span>
                                <span class="text-[9px] text-muted uppercase tracking-[0.25em] font-bold"><?= htmlspecialchars($prod['slug']) ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1.5 rounded-lg bg-white/5 border border-border text-[9px] font-bold uppercase tracking-widest text-muted">
                            <?= htmlspecialchars($prod['cat_name'] ?? 'Uncategorized') ?>
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <?php if ($prod['sale_price']): ?>
                            <p class="font-bold text-brand">₹<?= number_format($prod['sale_price'], 2) ?></p>
                            <p class="text-muted line-through text-[10px] opacity-40">₹<?= number_format($prod['price'], 2) ?></p>
                        <?php else: ?>
                            <p class="font-bold text-main">₹<?= number_format($prod['price'], 2) ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <p class="font-bold text-sm <?= $prod['stock'] > 10 ? 'text-emerald-500' : 'text-rose-500' ?>"><?= $prod['stock'] ?></p>
                        <p class="text-[8px] uppercase tracking-widest text-muted font-bold">Units Available</p>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="edit-product.php?id=<?= $prod['id'] ?>" class="w-10 h-10 rounded-xl bg-white/5 border border-border text-muted hover:text-brand transition-all flex items-center justify-center">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Permission-Based Delete Button -->
                            <form method="POST" id="delete-form-<?= $prod['id'] ?>" class="relative inline-block">
                                <input type="hidden" name="delete_id" value="<?= $prod['id'] ?>">
                                <button type="button" 
                                        id="delete-btn-<?= $prod['id'] ?>"
                                        onmousedown="startHold(<?= $prod['id'] ?>)" 
                                        onmouseup="stopHold(<?= $prod['id'] ?>)" 
                                        onmouseleave="stopHold(<?= $prod['id'] ?>)"
                                        ontouchstart="startHold(<?= $prod['id'] ?>)"
                                        ontouchend="stopHold(<?= $prod['id'] ?>)"
                                        class="w-10 h-10 rounded-xl bg-rose-500/5 border border-rose-500/10 text-rose-500/80 hover:bg-rose-500/10 transition-all flex items-center justify-center relative overflow-hidden group">
                                    <i data-lucide="trash-2" id="icon-<?= $prod['id'] ?>" class="w-4 h-4 relative z-10 transition-all duration-300"></i>
                                    <span id="text-<?= $prod['id'] ?>" class="absolute inset-0 z-20 flex items-center justify-center text-[8px] font-bold uppercase tracking-tighter opacity-0 scale-50 transition-all duration-300 pointer-events-none">Confirm?</span>
                                    <!-- Progress Layer -->
                                    <div id="progress-<?= $prod['id'] ?>" class="absolute inset-0 bg-rose-500 origin-bottom scale-y-0 opacity-20"></div>
                                </button>
                                <!-- Tooltip / Instruction -->
                                <div id="hint-<?= $prod['id'] ?>" class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-rose-500 text-[8px] text-white rounded opacity-0 pointer-events-none transition-opacity uppercase font-bold tracking-tighter whitespace-nowrap">Hold to seeking permission</div>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
let holdIntervals = {};
let isConfirmState = {};

function startHold(id) {
    if (isConfirmState[id]) {
        document.getElementById(`delete-form-${id}`).submit();
        return;
    }

    const progress = document.getElementById(`progress-${id}`);
    const hint = document.getElementById(`hint-${id}`);
    const icon = document.getElementById(`icon-${id}`);
    const text = document.getElementById(`text-${id}`);
    const btn = document.getElementById(`delete-btn-${id}`);
    
    hint.classList.add('opacity-100');
    let startTime = Date.now();
    let duration = 1500; // 1.5 seconds

    holdIntervals[id] = setInterval(() => {
        let elapsed = Date.now() - startTime;
        let p = Math.min(elapsed / duration, 1);
        progress.style.transform = `scaleY(${p})`;
        
        if (p >= 1) {
            clearInterval(holdIntervals[id]);
            // Transition to Permission/Confirm state
            isConfirmState[id] = true;
            icon.classList.add('opacity-0', 'scale-50');
            text.classList.remove('opacity-0', 'scale-50');
            text.classList.add('opacity-100', 'scale-100');
            btn.classList.add('!bg-rose-500', '!text-white', '!border-rose-500');
            progress.style.transform = 'scaleY(1)';
            progress.classList.add('!opacity-100');
            hint.textContent = "Tap to confirm deletion";
        }
    }, 10);
}

function stopHold(id) {
    if (isConfirmState[id]) return; // Stay in confirm state once reached

    const progress = document.getElementById(`progress-${id}`);
    const hint = document.getElementById(`hint-${id}`);
    
    clearInterval(holdIntervals[id]);
    hint.classList.remove('opacity-100');
    progress.style.transform = 'scaleY(0)';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
