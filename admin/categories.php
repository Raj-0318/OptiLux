<?php
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $id = $_POST['delete_id'];
        
        try {
            $conn->beginTransaction();
            
            // Unlink products from this category
            $conn->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?")->execute([$id]);
            
            // Delete the category
            $conn->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
            
            $conn->commit();
            setFlash('success', "Category has been deleted. Related products are now uncategorized.");
        } catch (Exception $e) {
            $conn->rollBack();
            setFlash('error', "Could not delete category: " . $e->getMessage());
        }
        
        header("Location: categories.php");
        exit;
    }
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<div class="glass-card overflow-hidden">
    <div class="p-10 border-b border-border bg-white/5 flex justify-between items-center bg-gradient-to-r from-white/5 to-transparent">
        <div>
            <h1 class="text-xl font-bold font-serif-lux tracking-widest uppercase">All Categories</h1>
            <p class="text-[9px] text-muted uppercase tracking-[0.2em] mt-1 font-semibold"><?= count($categories) ?> Active Categories</p>
        </div>
        <a href="add-category.php" class="bg-brand text-dark font-bold px-6 py-3 rounded-xl hover:opacity-95 transition-all uppercase tracking-[0.2em] text-[10px] shadow-xl shadow-brand/10 flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Category
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-muted text-[10px] uppercase tracking-[0.2em] border-b border-border">
                    <th class="px-8 py-6 font-bold">ID</th>
                    <th class="px-8 py-6 font-bold">Name</th>
                    <th class="px-8 py-6 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium tracking-wide">
                <?php foreach($categories as $cat): ?>
                <tr class="border-b border-border last:border-0 hover:bg-white/5 transition-all group">
                    <td class="px-8 py-6 text-muted font-bold tracking-widest">#CAT-<?= str_pad($cat['id'], 2, '0', STR_PAD_LEFT) ?></td>
                    <td class="px-8 py-6">
                        <span class="font-bold text-sm tracking-tight text-main"><?= htmlspecialchars($cat['name']) ?></span>
                        <p class="text-[9px] text-muted uppercase tracking-[0.2em] font-bold mt-0.5"><?= htmlspecialchars($cat['slug']) ?></p>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="edit-category.php?id=<?= $cat['id'] ?>" class="w-10 h-10 rounded-xl bg-white/5 border border-border text-muted hover:text-brand transition-all flex items-center justify-center">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Permission-Based Delete Button -->
                            <form method="POST" id="delete-form-<?= $cat['id'] ?>" class="relative inline-block">
                                <input type="hidden" name="delete_id" value="<?= $cat['id'] ?>">
                                <button type="button" 
                                        id="delete-btn-<?= $cat['id'] ?>"
                                        onmousedown="startHold(<?= $cat['id'] ?>)" 
                                        onmouseup="stopHold(<?= $cat['id'] ?>)" 
                                        onmouseleave="stopHold(<?= $cat['id'] ?>)"
                                        ontouchstart="startHold(<?= $cat['id'] ?>)"
                                        ontouchend="stopHold(<?= $cat['id'] ?>)"
                                        class="w-10 h-10 rounded-xl bg-rose-500/5 border border-rose-500/10 text-rose-500/80 hover:bg-rose-500/10 transition-all flex items-center justify-center relative overflow-hidden group">
                                    <i data-lucide="trash-2" id="icon-<?= $cat['id'] ?>" class="w-4 h-4 relative z-10 transition-all duration-300"></i>
                                    <span id="text-<?= $cat['id'] ?>" class="absolute inset-0 z-20 flex items-center justify-center text-[8px] font-bold uppercase tracking-tighter opacity-0 scale-50 transition-all duration-300 pointer-events-none">Confirm?</span>
                                    <!-- Progress Layer -->
                                    <div id="progress-<?= $cat['id'] ?>" class="absolute inset-0 bg-rose-500 origin-bottom scale-y-0 opacity-20"></div>
                                </button>
                                <!-- Tooltip / Instruction -->
                                <div id="hint-<?= $cat['id'] ?>" class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-rose-500 text-[8px] text-white rounded opacity-0 pointer-events-none transition-opacity uppercase font-bold tracking-tighter whitespace-nowrap">Hold to seeking permission</div>
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
