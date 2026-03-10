<?php
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if ($name) {
        $slug = strtolower(str_replace(' ', '-', $name));
        $stmt = $conn->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        if ($stmt->execute([$name, $slug])) {
            setFlash('success', "New category added successfully.");
            header("Location: categories.php");
            exit;
        }
    }
}
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-10 flex items-center gap-4">
        <a href="categories.php" class="w-10 h-10 rounded-xl bg-white/5 border border-border flex items-center justify-center hover:bg-white/10 transition-all">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold font-serif-lux tracking-widest uppercase">Add New Category</h1>
            <p class="text-[10px] text-muted uppercase tracking-[0.2em] mt-1 font-semibold">Create a new product grouping</p>
        </div>
    </div>

    <div class="glass-card p-10">
        <form method="POST" class="space-y-8">
            <div>
                <label class="block text-[10px] uppercase font-bold text-muted mb-3 tracking-widest">Category Name</label>
                <input type="text" name="name" required placeholder="e.g. Sunglasses, Eyeglasses" class="pro-input py-4">
            </div>
            
            <div class="pt-6 border-t border-border flex gap-4">
                <button type="submit" class="flex-grow bg-brand text-dark font-bold py-4 rounded-xl hover:opacity-90 transition-all uppercase tracking-[0.2em] text-xs shadow-xl shadow-brand/10">
                    Save Category
                </button>
                <a href="categories.php" class="px-8 py-4 rounded-xl border border-border text-muted hover:text-white transition-all uppercase tracking-[0.2em] text-xs font-bold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
