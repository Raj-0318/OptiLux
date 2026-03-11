<?php
ob_start();
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();
requireAdmin();

// Fetch Categories & Brands for the Action Drawer
$drawer_categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$drawer_brands = $conn->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiLux Management Console</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary: #0a0a0a;
            --accent: #d4af37;
            --accent-soft: rgba(212, 175, 55, 0.1);
            --bg-body: #050505;
            --bg-card: #111111;
            --border: rgba(255, 255, 255, 0.08);
            --text-main: #f8f8f8;
            --text-muted: #888888;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .font-serif-lux { font-family: 'Cinzel', serif; }

        .sidebar {
            width: 280px;
            background: var(--primary);
            border-right: 1px solid var(--border);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 50;
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .nav-link {
            display: flex;
            items-center: center;
            gap: 12px;
            padding: 12px 20px;
            border-radius: 12px;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-link.active {
            color: var(--accent);
            background: var(--accent-soft);
        }

        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
        }

        .pro-active-indicator {
            width: 4px;
            height: 20px;
            background: var(--accent);
            border-radius: 0 4px 4px 0;
            position: absolute;
            left: 0;
        }

        /* Modal / Form Styling */
        .pro-input {
            background: #1a1a1a;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            color: #fff;
            font-size: 14px;
            width: 100%;
            transition: border-color 0.2s;
        }
        .pro-input:focus {
            outline: none;
            border-color: var(--accent);
        }
    </style>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              brand: '#d4af37',
              dark: '#0a0a0a',
              card: '#111111',
              border: 'rgba(255,255,255,0.08)',
            }
          }
        }
      }

      function toggleModal(id) {
          const modal = document.getElementById(id);
          if (modal.classList.contains('hidden')) {
              modal.classList.remove('hidden');
              modal.classList.add('flex');
          } else {
              modal.classList.add('hidden');
              modal.classList.remove('flex');
          }
      }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar flex flex-col">
    <div class="p-8 border-b border-border">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-brand rounded-lg flex items-center justify-center text-dark font-bold text-xl font-serif-lux italic">O</div>
            <h1 class="text-xl font-bold tracking-tighter font-serif-lux text-brand">OptiLux</h1>
        </div>
        <p class="text-[10px] uppercase tracking-[0.3em] text-muted mt-2 font-semibold">Admin Panel</p>
    </div>

    <nav class="flex-grow p-6 space-y-2 mt-4">
        <?php 
        $current_page = basename($_SERVER['PHP_SELF']); 
        function isActive($page, $current) { return ($current === $page) ? 'active' : ''; }
        ?>
        <a href="index.php" class="nav-link relative <?= isActive('index.php', $current_page) ?>">
            <?php if(isActive('index.php', $current_page)): ?><div class="pro-active-indicator"></div><?php endif; ?>
            <i data-lucide="layout-grid" class="w-5 h-5"></i> Dashboard
        </a>
        <a href="products.php" class="nav-link relative <?= isActive('products.php', $current_page) ?>">
            <?php if(isActive('products.php', $current_page)): ?><div class="pro-active-indicator"></div><?php endif; ?>
            <i data-lucide="package" class="w-5 h-5"></i> Products
        </a>
        <a href="categories.php" class="nav-link relative <?= isActive('categories.php', $current_page) ?>">
            <?php if(isActive('categories.php', $current_page)): ?><div class="pro-active-indicator"></div><?php endif; ?>
            <i data-lucide="box" class="w-5 h-5"></i> Categories
        </a>
        <a href="orders.php" class="nav-link relative <?= isActive('orders.php', $current_page) ?>">
            <?php if(isActive('orders.php', $current_page)): ?><div class="pro-active-indicator"></div><?php endif; ?>
            <i data-lucide="shopping-cart" class="w-5 h-5"></i> Orders
        </a>
        <a href="brands.php" class="nav-link relative <?= isActive('brands.php', $current_page) ?>">
            <?php if(isActive('brands.php', $current_page)): ?><div class="pro-active-indicator"></div><?php endif; ?>
            <i data-lucide="award" class="w-5 h-5"></i> Brands
        </a>
        <a href="users.php" class="nav-link relative <?= isActive('users.php', $current_page) ?>">
            <?php if(isActive('users.php', $current_page)): ?><div class="pro-active-indicator"></div><?php endif; ?>
            <i data-lucide="users" class="w-5 h-5"></i> Users
        </a>
    </nav>

    <div class="p-6 border-t border-border space-y-4">
        <div class="flex items-center gap-3 px-2">
            <div class="w-10 h-10 rounded-full bg-brand/10 border border-brand/20 flex items-center justify-center text-brand font-bold">A</div>
            <div>
                <p class="text-xs font-bold">Admin</p>
                <p class="text-[10px] text-muted uppercase tracking-wider">Store Manager</p>
            </div>
        </div>
        <a href="../logout.php" class="nav-link text-rose-500 hover:bg-rose-500/10 hover:text-rose-400">
            <i data-lucide="log-out" class="w-5 h-5"></i> Sign Out
        </a>
    </div>
</aside>

<!-- Main Container -->
<div class="main-content">
    <main class="p-10 flex-grow">
<?php foreach(['success', 'error', 'info', 'warning'] as $type): ?>
    <?php if ($msg = getFlash($type)): ?>
        <div id="flash-<?= $type ?>-toast" class="fixed top-24 right-8 z-[100] animate-in fade-in slide-in-from-top-4 duration-500 min-w-[320px]">
            <div class="glass-card bg-primary border <?= $type === 'success' ? 'border-emerald-500/30' : ($type === 'error' ? 'border-rose-500/30' : 'border-brand/30') ?> text-white px-8 py-5 flex items-center gap-4 shadow-2xl backdrop-blur-xl">
                <div class="w-10 h-10 rounded-xl <?= $type === 'success' ? 'bg-emerald-500/20 text-emerald-500' : ($type === 'error' ? 'bg-rose-500/20 text-rose-500' : 'bg-brand/20 text-brand') ?> flex items-center justify-center">
                    <i data-lucide="<?= $type === 'success' ? 'check-circle' : ($type === 'error' ? 'alert-circle' : 'info') ?>" class="w-5 h-5"></i>
                </div>
                <div class="flex-grow">
                    <p class="text-[9px] uppercase tracking-[0.3em] font-bold <?= $type === 'success' ? 'text-emerald-500' : ($type === 'error' ? 'text-rose-500' : 'text-brand') ?> mb-0.5"><?= strtoupper($type) ?></p>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-white/90 leading-tight"><?= htmlspecialchars($msg) ?></p>
                </div>
                <button onclick="document.getElementById('flash-<?= $type ?>-toast').remove()" class="text-white/20 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="h-1 <?= $type === 'success' ? 'bg-emerald-500/30' : ($type === 'error' ? 'bg-rose-500/30' : 'bg-brand/30') ?> w-full animate-progress-shrink origin-left"></div>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('flash-<?= $type ?>-toast');
                if (toast) {
                    toast.classList.add('transition-all', 'duration-500', 'opacity-0', 'translate-y-[-1rem]');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 5000);
        </script>
    <?php endif; ?>
<?php endforeach; ?>

<style>
@keyframes progress-shrink {
    from { transform: scaleX(1); }
    to { transform: scaleX(0); }
}
.animate-progress-shrink {
    animation: progress-shrink 5s linear forwards;
}
</style>
    

