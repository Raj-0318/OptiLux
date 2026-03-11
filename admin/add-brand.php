<?php
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $logo = $_POST['logo'];

    try {
        $stmt = $conn->prepare("INSERT INTO brands (name, slug, logo) VALUES (?, ?, ?)");
        $stmt->execute([$name, $slug, $logo]);
        setFlash('success', "New maison established: $name");
        header("Location: brands.php");
        exit;
    } catch (Exception $e) {
        setFlash('error', "Failed to establish maison: " . $e->getMessage());
    }
}
?>

<div class="max-w-4xl mx-auto py-10">
    <div class="mb-16 flex items-center gap-8">
        <a href="brands.php" class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-muted hover:text-brand transition-all">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif-lux tracking-widest uppercase text-brand">Add <span class="text-white italic">Brand</span></h1>
            <p class="text-[9px] text-muted uppercase tracking-[0.3em] mt-1 font-semibold">Enter brand details and identity</p>
        </div>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="p-12 relative bg-gradient-to-b from-brand/5 to-transparent">
            <form method="POST" class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-brand uppercase tracking-[0.3em]">Brand Name</label>
                        <input type="text" name="name" required placeholder="e.g. Celine" 
                               class="pro-input bg-dark font-serif-lux text-lg tracking-widest placeholder:text-white/10">
                    </div>
                    
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-brand uppercase tracking-[0.3em]">Slug</label>
                        <input type="text" name="slug" placeholder="e.g. celine-paris" 
                               class="pro-input bg-dark font-bold text-slate-400 placeholder:text-white/10">
                        <p class="text-[9px] text-muted uppercase italic">Automatic if left empty</p>
                    </div>
                </div>

                <div class="space-y-4">
                        <label class="block text-[10px] font-black text-brand uppercase tracking-[0.3em]">Logo Link (Optional)</label>
                        <input type="url" name="logo" placeholder="https://..." 
                               class="pro-input bg-dark font-mono text-xs">
                         <p class="text-[9px] text-muted uppercase italic">Leave empty to use the first product image</p>
                </div>

                <div class="pt-8 border-t border-white/5">
                    <button type="submit" class="w-full bg-brand text-dark font-black px-12 py-5 rounded-2xl hover:bg-white transition-all duration-500 uppercase tracking-[0.4em] text-[10px] shadow-2xl shadow-brand/20">
                        Save Brand
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
