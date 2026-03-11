<?php
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM brands WHERE id = ?");
$stmt->execute([$id]);
$brand = $stmt->fetch();

if (!$brand) {
    setFlash('error', "Maison identity missing from archives.");
    header("Location: brands.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $logo = $_POST['logo'];

    try {
        $stmt = $conn->prepare("UPDATE brands SET name = ?, slug = ?, logo = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $logo, $id]);
        setFlash('success', "Maison records updated: $name");
        header("Location: brands.php");
        exit;
    } catch (Exception $e) {
        setFlash('error', "Failed to update maison: " . $e->getMessage());
    }
}
?>

<div class="max-w-4xl mx-auto py-10">
    <div class="mb-16 flex items-center gap-8">
        <a href="brands.php" class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-muted hover:text-brand transition-all">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif-lux tracking-widest uppercase text-brand">Edit <span class="text-white italic">Brand</span></h1>
            <p class="text-[9px] text-muted uppercase tracking-[0.3em] mt-1 font-semibold">Update brand details</p>
        </div>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="p-12 relative bg-gradient-to-b from-brand/5 to-transparent">
            <form method="POST" class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-brand uppercase tracking-[0.3em]">Brand Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($brand['name']) ?>" required 
                               class="pro-input bg-dark font-serif-lux text-lg tracking-widest">
                    </div>
                    
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-brand uppercase tracking-[0.3em]">Slug</label>
                        <input type="text" name="slug" value="<?= htmlspecialchars($brand['slug']) ?>" required
                               class="pro-input bg-dark font-bold text-slate-400">
                    </div>
                </div>

                <div class="space-y-4">
                        <label class="block text-[10px] font-black text-brand uppercase tracking-[0.3em]">Logo Asset URL (Optional)</label>
                        <input type="url" name="logo" value="<?= htmlspecialchars($brand['logo']) ?>" 
                               class="pro-input bg-dark font-mono text-xs">
                         <p class="text-[9px] text-muted uppercase italic">If empty, we'll use the first product's photo.</p>
                </div>

                <div class="pt-8 border-t border-white/5">
                    <button type="submit" class="w-full bg-brand text-dark font-black px-12 py-5 rounded-2xl hover:bg-white transition-all duration-500 uppercase tracking-[0.4em] text-[10px] shadow-2xl shadow-brand/20">
                        Update Brand
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
