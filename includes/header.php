<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$cart_count = 0;
if (isLoggedIn()) {
    $cart_count = getCartCount($conn, $_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiLux - Premium Eyewear</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['"Montserrat"', 'sans-serif'],
              serif: ['"Cinzel"', 'serif'],
            },
            colors: {
              primary: '#050505',
              accent:  '#C5A059',
              surface: '#FFFFFF',
            }
          }
        }
      }
    </script>
    <style type="text/tailwindcss">
        @layer components {
          .btn-primary {
            @apply bg-primary hover:bg-slate-800 text-white font-medium px-6 py-3 rounded-lg transition-all;
          }
        }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-surface text-slate-800 font-sans min-h-screen flex flex-col">

<nav class="bg-primary text-white sticky top-0 z-50 border-b border-white/10 backdrop-blur-md bg-primary/95">
  <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-20">
    <a href="/Optilux/index.php" class="text-3xl font-serif text-white tracking-widest uppercase">Optilux<span class="text-accent">.</span></a>
    <div class="hidden md:flex gap-10 text-xs font-medium tracking-[0.2em] uppercase">
      <a href="/Optilux/shop.php" class="hover:text-accent transition duration-300">Shop</a>
      <a href="/Optilux/brands.php" class="hover:text-accent transition duration-300">Brands</a>
      <a href="/Optilux/about.php" class="hover:text-accent transition duration-300">About</a>
      <a href="/Optilux/contact.php" class="hover:text-accent transition duration-300">Contact Us</a>
    </div>
    <div class="flex items-center gap-6">
      <?php if (isLoggedIn()): ?>
        <!-- Logged in state -->
        <button class="hover:text-accent transition duration-300" aria-label="Search"><i data-lucide="search" class="w-5 h-5 stroke-[1.5]"></i></button>
        <a href="/Optilux/cart.php" class="hover:text-accent transition duration-300 relative flex items-center" aria-label="Cart">
          <i data-lucide="shopping-bag" class="w-5 h-5 stroke-[1.5]"></i>
          <?php if ($cart_count > 0): ?>
            <span class="absolute -top-2 -right-2 bg-accent text-primary text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center"><?= $cart_count ?></span>
          <?php endif; ?>
        </a>
        <a href="/Optilux/wishlist.php" class="hover:text-accent transition duration-300" aria-label="Wishlist"><i data-lucide="heart" class="w-5 h-5 stroke-[1.5]"></i></a>
        <a href="<?= isAdmin() ? '/Optilux/admin/index.php' : '/Optilux/account.php' ?>" class="hover:text-accent transition duration-300" aria-label="Profile"><i data-lucide="user" class="w-5 h-5 stroke-[1.5]"></i></a>
      <?php else: ?>
        <!-- Logged out state -->
        <div class="flex items-center gap-4 text-xs tracking-widest font-semibold uppercase">
          <a href="/Optilux/login.php" class="hover:text-accent transition duration-300">Login</a>
          <span class="text-white/20">|</span>
          <a href="/Optilux/register.php" class="hover:text-accent transition duration-300">Sign Up</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main class="flex-grow">
    <!-- Success Flash Message -->
    <?php $flash_success = getFlash('success'); if ($flash_success): ?>
        <div id="flash-success-toast" class="fixed top-24 right-8 z-[100] animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="bg-primary border border-accent/30 text-white px-8 py-5 flex items-center gap-4 shadow-2xl backdrop-blur-xl">
                <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center text-accent">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-[0.2em] font-bold text-accent mb-0.5">Success</p>
                    <p class="text-[11px] font-medium text-white/90 uppercase tracking-wider"><?= htmlspecialchars($flash_success) ?></p>
                </div>
                <button onclick="document.getElementById('flash-success-toast').remove()" class="ml-4 text-white/30 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="h-0.5 bg-accent/30 w-full animate-progress-shrink origin-left"></div>
        </div>
        <style>
            @keyframes progress-shrink {
                from { transform: scaleX(1); }
                to { transform: scaleX(0); }
            }
            .animate-progress-shrink {
                animation: progress-shrink 5s linear forwards;
            }
        </style>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('flash-success-toast');
                if (toast) {
                    toast.classList.add('transition-all', 'duration-500', 'opacity-0', 'translate-y-[-1rem]');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 5000);
        </script>
    <?php endif; ?>


        