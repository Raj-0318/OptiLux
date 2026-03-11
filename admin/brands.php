<?php
require_once __DIR__ . '/includes/header.php';

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    try {
        $conn->beginTransaction();
        // Unlink products
        $conn->prepare("UPDATE products SET brand_id = NULL WHERE brand_id = ?")->execute([$id]);
        // Delete brand
        $conn->prepare("DELETE FROM brands WHERE id = ?")->execute([$id]);
        $conn->commit();
        setFlash('success', "Brand deleted successfully.");
    } catch (Exception $e) {
        $conn->rollBack();
        setFlash('error', "Error: " . $e->getMessage());
    }
    header("Location: brands.php");
    exit;
}

// Fetch brands with fallback image from first product
$brands = $conn->query("SELECT b.*, 
                       (SELECT image FROM products WHERE brand_id = b.id ORDER BY id ASC LIMIT 1) as fallback_image 
                       FROM brands b ORDER BY b.name ASC")->fetchAll();
?>

<div class="space-y-16 mt-4">
    <!-- Header Section (Simplified English) -->
    <div class="glass-card overflow-hidden">
        <div class="p-12 relative bg-gradient-to-br from-brand/10 via-dark to-dark border-b border-brand/20">
            <!-- Background Decorative Element -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand/5 rounded-full blur-[100px] pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="text-center md:text-left">
                    <span class="text-brand text-[10px] font-bold tracking-[0.5em] uppercase mb-4 block">Store Management</span>
                    <h1 class="text-4xl md:text-5xl font-serif-lux tracking-[0.1em] text-white uppercase italic">Our <span class="text-brand not-italic">Brands</span></h1>
                    <div class="h-1 w-24 bg-brand mt-6 mx-auto md:mx-0"></div>
                </div>
                <a href="add-brand.php" class="group relative px-10 py-5 bg-brand text-dark font-black text-[10px] tracking-[0.3em] uppercase rounded-2xl hover:bg-white transition-all duration-500 shadow-2xl shadow-brand/20">
                    <span class="relative z-10 flex items-center gap-3">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i> Add New Brand
                    </span>
                </a>
            </div>
        </div>

        <div class="p-10 bg-dark/50 backdrop-blur-md">
             <p class="text-[9px] text-muted tracking-[0.3em] uppercase font-bold"><?= count($brands) ?> Total Brands Registered</p>
        </div>
    </div>

    <!-- Brands Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
        <?php foreach($brands as $brand): ?>
        <div class="glass-card group relative overflow-hidden transition-all duration-700 hover:-translate-y-2 border-white/5 hover:border-slate-400/30">
            <!-- Background Overlay -->
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-slate-400/20 to-transparent"></div>
            
            <div class="p-10">
                <div class="flex items-start justify-between mb-8">
                    <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center overflow-hidden group-hover:border-slate-400/40 transition-colors duration-500">
                        <?php 
                            $admin_brand_img = !empty($brand['logo']) ? $brand['logo'] : $brand['fallback_image'];
                            if($admin_brand_img): 
                        ?>
                            <img src="<?= htmlspecialchars($admin_brand_img) ?>" class="w-full h-full object-cover transition-all duration-700 <?= empty($brand['logo']) ? '' : 'grayscale group-hover:grayscale-0' ?>">
                        <?php else: ?>
                            <span class="text-2xl font-serif-lux text-slate-500 italic"><?= substr($brand['name'], 0, 1) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex gap-4">
                        <a href="edit-brand.php?id=<?= $brand['id'] ?>" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 text-slate-400 hover:text-white hover:bg-white/10 flex items-center justify-center transition-all duration-300">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </a>
                        
                        <form method="POST" id="delete-form-<?= $brand['id'] ?>" class="relative inline-block">
                            <input type="hidden" name="delete_id" value="<?= $brand['id'] ?>">
                            <button type="button" 
                                    id="delete-btn-<?= $brand['id'] ?>"
                                    onmousedown="startHold(<?= $brand['id'] ?>)" 
                                    onmouseup="stopHold(<?= $brand['id'] ?>)" 
                                    onmouseleave="stopHold(<?= $brand['id'] ?>)"
                                    ontouchstart="startHold(<?= $brand['id'] ?>)"
                                    ontouchend="stopHold(<?= $brand['id'] ?>)"
                                    class="w-10 h-10 rounded-xl bg-rose-500/5 border border-rose-500/10 text-rose-500/80 hover:bg-rose-500/10 transition-all flex items-center justify-center relative overflow-hidden group">
                                <i data-lucide="trash-2" id="icon-<?= $brand['id'] ?>" class="w-4 h-4 relative z-10 transition-all duration-300"></i>
                                <span id="text-<?= $brand['id'] ?>" class="absolute inset-0 z-20 flex items-center justify-center text-[8px] font-bold uppercase tracking-tighter opacity-0 scale-50 transition-all duration-300 pointer-events-none">Confirm?</span>
                                <!-- Progress Layer -->
                                <div id="progress-<?= $brand['id'] ?>" class="absolute inset-0 bg-rose-500 origin-bottom scale-y-0 opacity-20"></div>
                            </button>
                            <!-- Tooltip / Instruction -->
                            <div id="hint-<?= $brand['id'] ?>" class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-rose-500 text-[8px] text-white rounded opacity-0 pointer-events-none transition-opacity uppercase font-bold tracking-tighter whitespace-nowrap">Hold to seeking permission</div>
                        </form>
                    </div>
                </div>

                <div>
                    <span class="text-[8px] font-black tracking-[0.4em] uppercase text-brand/60 mb-3 block italic">Distinguished House</span>
                    <h3 class="text-xl font-serif-lux text-white tracking-[0.2em] uppercase mb-2"><?= htmlspecialchars($brand['name']) ?></h3>
                    <p class="text-[9px] text-slate-500 tracking-[0.2em] font-bold uppercase mb-6 italic opacity-60">ID Tag: <?= htmlspecialchars($brand['slug']) ?></p>
                    
                    <a href="products.php?brand_id=<?= $brand['id'] ?>" class="inline-flex items-center gap-2 text-[9px] font-black text-slate-400 hover:text-white tracking-[0.3em] uppercase transition-colors group/link">
                        View Products 
                        <i data-lucide="arrow-right" class="w-3 h-3 group-hover/link:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            
            <div class="absolute bottom-4 right-4 text-white/[0.02] font-serif-lux text-7xl select-none pointer-events-none italic uppercase -rotate-12 group-hover:text-white/[0.04] transition-colors duration-700">
                <?= substr($brand['name'], 0, 2) ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Add Placeholder -->
        <a href="add-brand.php" class="glass-card group flex flex-col items-center justify-center p-12 border-dashed border-white/10 hover:border-brand/40 transition-all duration-500 min-h-[300px] bg-white/[0.02] hover:bg-brand/[0.02]">
            <div class="w-16 h-16 rounded-full border border-white/10 flex items-center justify-center text-white/20 group-hover:text-brand group-hover:border-brand/40 transition-all duration-500 mb-6 group-hover:scale-110">
                <i data-lucide="plus" class="w-8 h-8"></i>
            </div>
            <p class="text-[10px] font-black text-white/20 group-hover:text-brand uppercase tracking-[0.4em] transition-all">Add New Brand</p>
        </a>
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
