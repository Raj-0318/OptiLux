</main>

<footer class="bg-primary text-white pt-24 pb-12 mt-auto border-t border-white/5">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-12 gap-12 lg:gap-8 mb-16">
        <div class="md:col-span-4">
            <h3 class="text-3xl font-serif tracking-widest uppercase mb-6">Optilux<span class="text-accent">.</span></h3>
            <p class="text-white/40 text-xs tracking-wider leading-relaxed mb-8 pr-12">
                Providing premium eyewear for everyone. We offer the best sunglasses and eyeglasses that look great and feel comfortable.
            </p>
            <div class="flex gap-6">
                <a href="#" class="text-white/30 hover:text-accent transition duration-300"><i data-lucide="instagram" class="w-4 h-4 stroke-[1.5]"></i></a>
                <a href="#" class="text-white/30 hover:text-accent transition duration-300"><i data-lucide="twitter" class="w-4 h-4 stroke-[1.5]"></i></a>
            </div>
        </div>
        
        <div class="md:col-span-2 md:col-start-7">
            <h4 class="text-[10px] font-bold tracking-[0.2em] uppercase text-white/70 mb-8">Shop</h4>
            <ul class="text-xs text-white/40 space-y-4 tracking-wider">
                <li><a href="/Optilux/shop.php?category=sunglasses" class="hover:text-accent transition duration-300">Sunglasses</a></li>
                <li><a href="/Optilux/shop.php?category=eyeglasses" class="hover:text-accent transition duration-300">Eyeglasses</a></li>
                <li><a href="/Optilux/brands.php" class="hover:text-accent transition duration-300">Brands</a></li>
                <li><a href="/Optilux/shop.php" class="hover:text-accent transition duration-300">New Arrivals</a></li>
            </ul>
        </div>
        
        <div class="md:col-span-2">
            <h4 class="text-[10px] font-bold tracking-[0.2em] uppercase text-white/70 mb-8">Customer Service</h4>
            <ul class="text-xs text-white/40 space-y-4 tracking-wider">
                <li><a href="/Optilux/contact.php" class="hover:text-accent transition duration-300">Contact Us</a></li>
                <li><a href="#" class="hover:text-accent transition duration-300">Shipping & Returns</a></li>
                <li><a href="#" class="hover:text-accent transition duration-300">Care Guide</a></li>
                <li><a href="/Optilux/account.php" class="hover:text-accent transition duration-300">My Account</a></li>
            </ul>
        </div>
        
        <div class="md:col-span-2">
            <h4 class="text-[10px] font-bold tracking-[0.2em] uppercase text-white/70 mb-8">Legal</h4>
            <ul class="text-xs text-white/40 space-y-4 tracking-wider">
                <li><a href="#" class="hover:text-accent transition duration-300">Privacy Policy</a></li>
                <li><a href="#" class="hover:text-accent transition duration-300">Terms of Service</a></li>
            </ul>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-6 border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center text-[10px] text-white/30 tracking-[0.2em] uppercase">
        <p>&copy; <?= date('Y') ?> OPTILUX. ALL RIGHTS RESERVED.</p>
        <p class="mt-4 md:mt-0 italic font-serif normal-case tracking-wider text-white/20 text-xs">Built with care.</p>
    </div>
</footer>

<script>
  // Initialize Lucide icons
  lucide.createIcons();

  /**
   * Global Wishlist Toggle (AJAX)
   * Prevents page reloads and provides smooth UI transitions.
   */
  async function toggleWishlist(productId, btn) {
      try {
          const response = await fetch(`/Optilux/wishlist.php?action=toggle&id=${productId}&ajax=1`);
          const data = await response.json();
          
          if (!data.success && data.redirect) {
              window.location.href = data.redirect;
              return;
          }
          
          if (data.success) {
              // Handle Wishlist Page (removal)
              const itemRow = btn.closest('[data-wishlist-item]');
              if (itemRow && data.status === 'removed') {
                  itemRow.style.opacity = '0';
                  setTimeout(() => {
                      itemRow.remove();
                      // If no items left, reload to show empty state (or could inject empty HTML)
                      if (document.querySelectorAll('[data-wishlist-item]').length === 0) {
                          window.location.reload();
                      }
                  }, 500);
                  return;
              }

              // Handle Global Toggle (Shop/Product Page)
              const icon = btn.querySelector('i');
              if (data.status === 'added') {
                  btn.classList.add('bg-accent', 'text-white');
                  btn.classList.remove('bg-white', 'text-primary', 'border-black/10');
                  if (icon) {
                      icon.classList.add('fill-current');
                      icon.classList.remove('stroke-[1.5]');
                  }
                  btn.title = "Remove from Wishlist";
              } else {
                  btn.classList.remove('bg-accent', 'text-white');
                  // Re-add specific classes based on original state if needed
                  // For simplicity, we just toggle based on what's common
                  if (!btn.classList.contains('bg-primary')) {
                      btn.classList.add('bg-white', 'text-primary');
                  }
                  if (icon) {
                      icon.classList.remove('fill-current');
                      icon.classList.add('stroke-[1.5]');
                  }
                  btn.title = "Add to Wishlist";
              }
              
              // Broadcast event if other components need to know
              document.dispatchEvent(new CustomEvent('wishlistUpdated', { detail: { productId, status: data.status } }));
          }
      } catch (error) {
          console.error('Wishlist error:', error);
      }
  }
</script>
</body>
</html>


