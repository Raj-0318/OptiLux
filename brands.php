<?php
require_once __DIR__ . '/includes/header.php';
// Fetch brands along with the image of their first product as a fallback logo
$brands = $conn->query("SELECT b.*, 
                       (SELECT image FROM products WHERE brand_id = b.id ORDER BY id ASC LIMIT 1) as fallback_image 
                       FROM brands b ORDER BY b.name ASC")->fetchAll();
?>

<!-- Cinematic Hero: The Partner Selection -->
<section class="relative h-[80vh] bg-primary flex items-center justify-center overflow-hidden border-b border-white/5">
    <!-- Sophisticated Background -->
    <div class="absolute inset-0 z-0">
        <img src="https://i.pinimg.com/1200x/65/2b/28/652b283370cd2e5ccee41f6d7af6d293.jpg" 
             alt="Elite Curation" class="w-full h-full object-cover opacity-20 grayscale scale-110 animate-[pulse_8s_ease-in-out_infinite]">
        <div class="absolute inset-0 bg-gradient-to-t from-primary via-transparent to-primary/80"></div>
    </div>
    
    <div class="relative z-10 text-center px-6 max-w-5xl animate-in fade-in slide-in-from-bottom-8 duration-1000">
        <span class="text-accent text-[10px] font-bold tracking-[0.5em] uppercase mb-8 block font-sans">Distinguished Houses</span>
        <h1 class="text-6xl md:text-8xl font-serif text-white tracking-widest leading-none mb-10 italic">
            Elite <br><span class="not-italic text-white/40 tracking-[0.2em] font-light uppercase text-3xl md:text-5xl mt-6 block">Partners</span>
        </h1>
        <p class="text-white/40 text-xs md:text-sm font-light max-w-2xl mx-auto leading-relaxed tracking-widest uppercase mb-12 font-sans">
            A meticulously gathered selection of the world's most distinguished eyewear houses. Where heritage meets contemporary vision.
        </p>
        <div class="w-[1px] h-20 bg-gradient-to-b from-accent to-transparent mx-auto"></div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-6 py-32">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-24">
        <?php foreach($brands as $brand): ?>
            <a href="/Optilux/shop.php?brand_id=<?= $brand['id'] ?>" class="group flex flex-col items-center relative transition-all duration-700">
                <!-- Brand Visual Container -->
                <div class="relative w-full aspect-[4/5] overflow-hidden bg-slate-50 transition-colors duration-700 transform-gpu">
                    <div class="w-full h-full transition-transform duration-1000 ease-out group-hover:scale-105 will-change-transform">
                        <?php 
                            $brand_img = !empty($brand['logo']) ? $brand['logo'] : $brand['fallback_image'];
                            if ($brand_img): 
                        ?>
                            <img src="<?= htmlspecialchars($brand_img) ?>" 
                                 alt="<?= htmlspecialchars($brand['name']) ?>" 
                                 class="w-full h-full object-cover transition-all duration-1000 <?= empty($brand['logo']) ? '' : 'grayscale group-hover:grayscale-0' ?> transform-gpu">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-white/50">
                                <span class="text-7xl font-serif text-slate-200 group-hover:text-primary transition-colors duration-700"><?= substr($brand['name'], 0, 1) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Brand Typography Set -->
                <div class="mt-10 text-center w-full">
                    <span class="text-[9px] font-black tracking-[0.5em] uppercase text-accent/60 mb-5 block group-hover:text-accent transition-colors duration-700 italic">Distinguished House</span>
                    <h3 class="font-serif text-2xl tracking-[0.25em] uppercase text-primary mb-6 transition-all duration-700 group-hover:tracking-[0.3em]"><?= htmlspecialchars($brand['name']) ?></h3>
                    <div class="flex items-center justify-center gap-4 opacity-0 group-hover:opacity-100 transition-all duration-700 translate-y-2 group-hover:translate-y-0">
                        <div class="h-[1px] w-8 bg-accent/30"></div>
                        <span class="text-[8px] font-bold tracking-[0.4em] uppercase text-accent">Explore</span>
                        <div class="h-[1px] w-8 bg-accent/30"></div>
                    </div>
                </div>
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


