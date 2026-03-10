<?php
require_once __DIR__ . '/includes/header.php';

$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$brands = $conn->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $slug = strtolower(str_replace(' ', '-', $name));
    $description = $_POST['description'];
    $price = $_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? $_POST['sale_price'] : NULL;
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];
    $brand_id = !empty($_POST['brand_id']) ? $_POST['brand_id'] : NULL;
    $image = $_POST['external_image'] ?? '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = __DIR__ . '/../uploads/products/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $img_name = time() . '_' . $_FILES['image']['name'];
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $img_name)) {
            $image = 'uploads/products/' . $img_name;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (name, slug, description, price, sale_price, stock, category_id, brand_id, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $slug, $description, $price, $sale_price, $stock, $category_id, $brand_id, $image])) {
        setFlash('success', "New product added successfully.");
        header("Location: products.php");
        exit;
    }
}
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-10 flex items-center gap-4">
        <a href="products.php" class="w-10 h-10 rounded-xl bg-white/5 border border-border flex items-center justify-center hover:bg-white/10 transition-all">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold font-serif-lux tracking-widest uppercase">Add New Product</h1>
            <p class="text-[10px] text-muted uppercase tracking-[0.2em] mt-1 font-semibold">Add a new item to the store catalog</p>
        </div>
    </div>

    <div class="glass-card p-10">
        <form method="POST" enctype="multipart/form-data" class="space-y-8">
            <div class="grid grid-cols-2 gap-8">
                <div class="col-span-2">
                    <label class="block text-[10px] uppercase font-bold text-muted mb-3 tracking-widest">Product Name</label>
                    <input type="text" name="name" required placeholder="e.g. Classic Aviator Sunglasses" class="pro-input py-4">
                </div>
                
                <div>
                    <label class="block text-[10px] uppercase font-bold text-muted mb-3 tracking-widest">Original Price (₹)</label>
                    <input type="number" step="0.01" name="price" required class="pro-input py-4">
                </div>
                
                <div>
                    <label class="block text-[10px] uppercase font-bold text-muted mb-3 tracking-widest">Sale Price (Optional)</label>
                    <input type="number" step="0.01" name="sale_price" class="pro-input py-4">
                </div>

                <div>
                    <label class="block text-[10px] uppercase font-bold text-muted mb-3 tracking-widest">Category</label>
                    <select name="category_id" required class="pro-input py-4">
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] uppercase font-bold text-muted mb-3 tracking-widest">Brand</label>
                    <select name="brand_id" class="pro-input py-4">
                        <option value="">None</option>
                        <?php foreach($brands as $brand): ?>
                            <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] uppercase font-bold text-muted mb-3 tracking-widest">Available Stock</label>
                    <input type="number" name="stock" value="10" required class="pro-input py-4">
                </div>

                <div>
                    <label class="block text-[10px] uppercase font-bold text-muted mb-3 tracking-widest">Image URL</label>
                    <input type="url" id="external_image" name="external_image" placeholder="https://..." class="pro-input py-4">
                </div>

                <div class="col-span-2">
                    <label class="block text-[10px] uppercase font-bold text-muted mb-3 tracking-widest">Description</label>
                    <textarea name="description" rows="4" class="pro-input py-4"></textarea>
                </div>

                <div class="col-span-1">
                    <label class="block text-[10px] uppercase font-bold text-muted mb-3 tracking-widest">Upload Image</label>
                    <input type="file" id="file_image" name="image" accept="image/*" class="text-xs text-muted file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-brand file:text-dark hover:file:opacity-90 file:cursor-pointer transition-all">
                </div>

                <div class="col-span-1">
                    <label class="block text-[10px] uppercase font-bold text-muted mb-3 tracking-widest">Image Preview</label>
                    <div class="w-full aspect-square max-h-[160px] rounded-2xl border border-border bg-white/5 overflow-hidden flex items-center justify-center p-2 group">
                        <img id="image_preview" src="" class="w-full h-full object-cover rounded-xl hidden">
                        <div id="preview_placeholder" class="text-muted text-[10px] uppercase tracking-widest font-bold opacity-30 flex flex-col items-center gap-2">
                            <i data-lucide="image" class="w-8 h-8 opacity-20"></i>
                            No Image Found
                        </div>
                    </div>
                </div>
            </div>

            <script>
            const externalInput = document.getElementById('external_image');
            const fileInput = document.getElementById('file_image');
            const previewImg = document.getElementById('image_preview');
            const placeholder = document.getElementById('preview_placeholder');

            function updatePreview(src) {
                if (src) {
                    previewImg.src = src;
                    previewImg.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                } else {
                    previewImg.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                }
            }

            externalInput.addEventListener('input', (e) => {
                const url = e.target.value;
                if (url) {
                    updatePreview(url);
                } else if (!fileInput.files.length) {
                    updatePreview(null);
                }
            });

            fileInput.addEventListener('change', function(e) {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        updatePreview(event.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    updatePreview(externalInput.value || null);
                }
            });
            </script>
            </div>

            <div class="pt-6 border-t border-border flex gap-4">
                <button type="submit" class="flex-grow bg-brand text-dark font-bold py-4 rounded-xl hover:opacity-90 transition-all uppercase tracking-[0.2em] text-xs shadow-xl shadow-brand/10">
                    Save Product
                </button>
                <a href="products.php" class="px-8 py-4 rounded-xl border border-border text-muted hover:text-white transition-all uppercase tracking-[0.2em] text-xs font-bold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
