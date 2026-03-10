<?php
require_once __DIR__ . '/includes/header.php';
$featured_products = getProducts($conn, 4); // Fetch 4 products for luxury minimalist layout
?>

<!-- Cinematic Hero -->
<section class="relative w-full h-[90vh] bg-primary overflow-hidden flex items-center border-b border-white/5">
    <div class="absolute inset-0 z-0">
        <!-- Ultra-premium dark aesthetic background image -->
        <img src="https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&q=80&w=2000" alt="Luxury Eyewear" class="w-full h-full object-cover opacity-40">
        <div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/80 to-transparent"></div>
    </div>
    
    <div class="relative z-10 w-full max-w-7xl mx-auto px-6 flex flex-col items-center text-center mt-20">
        <span class="text-accent text-xs font-semibold tracking-[0.3em] uppercase mb-6 block">Welcome to Optilux</span>
        <h1 class="text-5xl md:text-7xl lg:text-8xl font-serif text-white mb-8 leading-tight tracking-wider">
            PREMIUM <br><span class="text-white/60 italic font-light">EYEWEAR</span>
        </h1>
        <p class="text-white/50 font-light max-w-xl mx-auto mb-12 text-sm md:text-base leading-relaxed tracking-wide">
            Discover our collection of premium sunglasses and eyeglasses. Designed for everyday wear and perfect style.
        </p>
        <a href="/Optilux/shop.php" class="border border-accent text-accent hover:bg-accent hover:text-primary transition-all duration-500 ease-in-out px-10 py-4 text-xs tracking-[0.2em] font-semibold uppercase">
            Shop Now
        </a>
    </div>
</section>

<!-- Abstract Categories -->
<section class="py-32 bg-primary">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row gap-12 lg:gap-24 items-center">
            <div class="w-full md:w-1/2">
                <div class="relative group cursor-pointer overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1591076482161-42ce6da69f67?auto=format&fit=crop&q=80&w=800" alt="Optical" class="w-full h-[600px] object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700 ease-out grayscale group-hover:grayscale-0">
                    <div class="absolute bottom-0 left-0 p-10 w-full bg-gradient-to-t from-primary to-transparent pointer-events-none">
                        <h2 class="text-3xl font-serif text-white tracking-widest mb-2">EYEGLASSES</h2>
                        <span class="text-accent text-xs tracking-[0.2em] uppercase font-semibold block">Shop Now</span>
                    </div>
                    <a href="/Optilux/shop.php?category=eyeglasses" class="absolute inset-0 z-10"></a>
                </div>
            </div>
            
            <div class="w-full md:w-1/2 mt-12 md:mt-32">
                <div class="relative group cursor-pointer overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1577803645773-f96470509666?auto=format&fit=crop&q=80&w=800" alt="Sun" class="w-full h-[600px] object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700 ease-out grayscale group-hover:grayscale-0">
                    <div class="absolute bottom-0 left-0 p-10 w-full bg-gradient-to-t from-primary to-transparent pointer-events-none">
                        <h2 class="text-3xl font-serif text-white tracking-widest mb-2">SUNGLASSES</h2>
                        <span class="text-accent text-xs tracking-[0.2em] uppercase font-semibold block">Shop Now</span>
                    </div>
                    <a href="/Optilux/shop.php?category=sunglasses" class="absolute inset-0 z-10"></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- The Curation (Featured) -->
<section class="py-32 bg-[#0A0A0A] border-y border-white/5">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-20">
            <div>
                <span class="text-accent text-xs font-semibold tracking-[0.3em] uppercase mb-4 block">Featured Products</span>
                <h2 class="text-4xl md:text-5xl font-serif text-white tracking-widest uppercase">Top Picks</h2>
            </div>
            <a href="/Optilux/shop.php" class="hidden md:inline-block text-white/50 hover:text-white transition duration-300 text-xs tracking-[0.2em] uppercase font-semibold border-b border-white/20 hover:border-white pb-1">View All</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-16">
            <?php foreach($featured_products as $product): ?>
            <div class="group flex flex-col">
                <div class="relative w-full aspect-square bg-[#F7F7F7] mb-6 overflow-hidden group">
                    <?php $img = !empty($product['image']) ? $product['image'] : ''; ?>
                    <a href="/Optilux/product.php?id=<?= $product['id'] ?>" class="block w-full h-full">
                         <img src="<?= $img ?>" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700 ease-in-out mix-blend-multiply">
                    </a>
                    
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        <?php if ($product['is_new']): ?>
                            <span class="bg-white text-primary text-[9px] px-3 py-1 font-bold tracking-[0.2em] uppercase">New</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Quick actions overlay on hover -->
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-4 pointer-events-none">
                        <a href="/Optilux/wishlist.php?action=add&id=<?= $product['id'] ?>" class="pointer-events-auto w-12 h-12 bg-primary/80 backdrop-blur text-white hover:text-accent rounded-full flex items-center justify-center transition duration-300 border border-white/10">
                            <i data-lucide="heart" class="w-4 h-4 stroke-[1.5]"></i>
                        </a>
                        <a href="/Optilux/cart.php?action=add&id=<?= $product['id'] ?>" class="pointer-events-auto w-12 h-12 bg-accent/90 backdrop-blur text-primary hover:bg-white rounded-full flex items-center justify-center transition duration-300">
                            <i data-lucide="shopping-bag" class="w-4 h-4 stroke-[1.5]"></i>
                        </a>
                    </div>
                </div>
                
                <div class="text-center">
                    <p class="text-white/40 text-[10px] uppercase font-bold tracking-[0.2em] mb-2"><?= htmlspecialchars($product['brand_name'] ?? 'Optilux') ?></p>
                    <a href="/Optilux/product.php?id=<?= $product['id'] ?>">
                        <h3 class="font-serif text-white text-lg mb-3 tracking-wider group-hover:text-accent transition duration-300"><?= htmlspecialchars($product['name']) ?></h3>
                    </a>
                    <div class="flex justify-center items-center font-light text-sm tracking-widest text-white/80">
                        <?php if ($product['sale_price']): ?>
                            <span class="text-accent mr-3"><?= formatPrice($product['sale_price']) ?></span>
                            <span class="text-white/30 line-through text-xs"><?= formatPrice($product['price']) ?></span>
                        <?php else: ?>
                            <span><?= formatPrice($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-16 md:hidden">
            <a href="/Optilux/shop.php" class="inline-block text-white/50 hover:text-white transition duration-300 text-xs tracking-[0.2em] uppercase font-semibold border-b border-white/20 hover:border-white pb-1">View All</a>
        </div>
    </div>
</section>

<!-- Brand Philosophy / Minimal Banner -->
<section class="py-32 bg-primary text-center px-6">
    <div class="max-w-3xl mx-auto">
        <i data-lucide="gem" class="w-8 h-8 mx-auto text-accent mb-8 stroke-[1]"></i>
        <h2 class="text-3xl md:text-4xl font-serif text-white leading-relaxed tracking-wider mb-8">
            "We believe eyewear is more than just an accessory, it's a way to express yourself."
        </h2>
        <span class="text-white/40 text-xs tracking-[0.3em] uppercase font-semibold">— OPTILUX TEAM</span>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
