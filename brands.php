<?php
require_once __DIR__ . '/includes/header.php';
$brands = $conn->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll();
?>

<!-- Minimalist Hero -->
<div class="bg-primary text-white py-24 border-b border-white/5 overflow-hidden relative">
    <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
        <span class="text-accent text-[10px] font-bold tracking-[0.4em] uppercase mb-6 block fade-in-up">The Curation</span>
        <h1 class="text-5xl md:text-7xl font-serif tracking-widest uppercase mb-8 leading-tight">Elite Partners</h1>
        <p class="text-white/40 text-sm md:text-md max-w-2xl mx-auto font-light leading-relaxed tracking-wide">
            A meticulously gathered selection of the world's most distinguished eyewear houses. 
            Where heritage meets contemporary vision.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-24">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-0.5 bg-black/5 border border-black/5 shadow-2xl">
        <?php foreach($brands as $brand): ?>
            <a href="/Optilux/shop.php?brand_id=<?= $brand['id'] ?>" class="group bg-white flex flex-col items-center justify-center p-12 aspect-square relative overflow-hidden transition-all duration-700 hover:z-10">
                <!-- Background Accent -->
                <div class="absolute inset-0 bg-slate-50 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                
                <!-- Brand Visual -->
                <div class="relative z-10 w-32 h-32 mb-8 transform group-hover:scale-110 transition-transform duration-700 ease-out flex items-center justify-center">
                    <?php if ($brand['logo']): ?>
                        <img src="/uploads/brands/<?= htmlspecialchars($brand['logo']) ?>" 
                             alt="<?= htmlspecialchars($brand['name']) ?>" 
                             class="max-w-full max-h-full object-contain grayscale group-hover:grayscale-0 transition-all duration-700">
                    <?php else: ?>
                        <span class="text-5xl font-serif text-slate-200 group-hover:text-primary transition-colors duration-700"><?= substr($brand['name'], 0, 1) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Brand Name -->
                <div class="relative z-10 text-center">
                    <h3 class="font-serif text-[13px] tracking-[0.2em] uppercase text-primary mb-2"><?= htmlspecialchars($brand['name']) ?></h3>
                    <div class="h-[1px] w-0 group-hover:w-full bg-accent mx-auto transition-all duration-700"></div>
                </div>

                <!-- Action Hint -->
                <span class="absolute bottom-8 text-[8px] font-bold tracking-[0.3em] uppercase text-accent opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-700">Explore Collection</span>
            </a>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty($brands)): ?>
        <div class="text-center py-32 bg-white">
            <i data-lucide="compass" class="w-12 h-12 text-slate-200 mx-auto mb-6 animate-pulse"></i>
            <p class="font-serif text-slate-400 italic tracking-wider">The brand directory is currently undergoing curation.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Bottom Section: Call to action -->
<div class="bg-slate-50 py-32 border-t border-black/5">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-3xl font-serif text-primary tracking-widest uppercase mb-12">Unparalleled Standards</h2>
        <div class="grid md:grid-cols-3 gap-12">
            <div class="space-y-4">
                <span class="text-accent font-serif italic text-2xl">01</span>
                <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-primary">Authenticity</p>
                <p class="text-xs text-slate-400 leading-relaxed font-light">Every piece is verified for absolute origin and quality.</p>
            </div>
            <div class="space-y-4">
                <span class="text-accent font-serif italic text-2xl">02</span>
                <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-primary">Limited Release</p>
                <p class="text-xs text-slate-400 leading-relaxed font-light">Exclusive access to global design capsules and rare iterations.</p>
            </div>
            <div class="space-y-4">
                <span class="text-accent font-serif italic text-2xl">03</span>
                <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-primary">Aftercare</p>
                <p class="text-xs text-slate-400 leading-relaxed font-light">Comprehensive maintenance services for our elite collections.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


