<?php
require_once __DIR__ . '/includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    
    if ($name) {
        $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->execute([$name, $user_id]);
        $_SESSION['user_name'] = $name;
        $success = "Profile updated successfully.";
    } else {
        $error = "Name cannot be empty.";
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch 3 recent orders
$stmt_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
$stmt_orders->execute([$user_id]);
$recent_orders = $stmt_orders->fetchAll();

// Get wishlist count
$stmt_wishlist = $conn->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
$stmt_wishlist->execute([$user_id]);
$wishlist_count = $stmt_wishlist->fetch()['count'];

// Split name for aesthetic layout
$name_parts = explode(' ', $user['name'] ?? '', 2);
?>

<!-- Account Hero -->
<div class="bg-primary text-white py-20 border-b border-white/5 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div>
                <span class="text-accent text-[10px] font-bold tracking-[0.4em] uppercase mb-4 block">Personal Workspace</span>
                <h1 class="text-4xl md:text-6xl font-serif tracking-widest uppercase leading-tight">Optilux Member</h1>
            </div>
            <div class="text-right">
                <p class="text-white/40 text-[10px] font-bold tracking-[0.2em] uppercase mb-2">Authenticated as</p>
                <p class="text-xl font-serif text-accent tracking-wider"><?= htmlspecialchars($user['name']) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-20">
    <div class="flex flex-col lg:flex-row gap-20">
        <!-- Sidebar Navigation -->
        <div class="w-full lg:w-64 flex-shrink-0">
            <div class="bg-white border border-black/5 p-8 sticky top-32 shadow-2xl">
                <nav class="space-y-6">
                    <p class="text-[9px] font-black tracking-[0.4em] text-slate-300 uppercase mb-8">Navigation</p>
                    
                    <a href="/Optilux/account.php" class="group flex items-center justify-between py-2 border-b border-primary focus:outline-none focus:ring-0 transition-colors duration-500">
                        <span class="text-[11px] font-serif tracking-[0.2em] uppercase text-primary font-bold">Profile Details</span>
                        <div class="w-1.5 h-1.5 bg-accent rounded-full shadow-[0_0_10px_rgba(251,191,36,0.6)]"></div>
                    </a>
                    
                    <a href="/Optilux/orders.php" class="group flex items-center justify-between py-2 border-b border-transparent hover:border-black/10 focus:outline-none focus:ring-0 transition-colors duration-500">
                        <span class="text-[11px] font-serif tracking-[0.2em] uppercase text-slate-400 group-hover:text-primary transition-colors">Order History</span>
                        <i data-lucide="package" class="w-3 h-3 text-slate-300 group-hover:text-accent transition-colors"></i>
                    </a>
                    
                    <a href="/Optilux/wishlist.php" class="group flex items-center justify-between py-2 border-b border-transparent hover:border-black/10 focus:outline-none focus:ring-0 transition-colors duration-500">
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-serif tracking-[0.2em] uppercase text-slate-400 group-hover:text-primary transition-colors">Wishlist</span>
                            <span class="text-[8px] font-bold bg-slate-50 text-slate-400 px-1.5 py-0.5 border border-black/5"><?= $wishlist_count ?></span>
                        </div>
                        <i data-lucide="heart" class="w-3 h-3 text-slate-300 group-hover:text-rose-400 transition-colors"></i>
                    </a>
                    
                    <a href="/Optilux/logout.php" class="group flex items-center justify-between py-2 mt-8 opacity-60 hover:opacity-100 focus:outline-none focus:ring-0 transition-opacity">
                        <span class="text-[10px] font-bold tracking-[0.2em] uppercase text-rose-500">Secure Logout</span>
                        <i data-lucide="power" class="w-3 h-3 text-rose-500"></i>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-grow space-y-20">
            
            <?php if ($success): ?>
                <div class="bg-primary border border-accent text-white px-8 py-5 flex items-center gap-4 text-[10px] tracking-[0.2em] uppercase animate-in fade-in slide-in-from-top-4">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-accent stroke-[1]"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="bg-rose-500 text-white px-8 py-5 flex items-center gap-4 text-[10px] tracking-[0.2em] uppercase animate-in fade-in slide-in-from-top-4">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-white stroke-[1]"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Profile Section -->
            <section class="animate-in fade-in duration-700">
                <header class="mb-12 border-b border-black/10 pb-4 flex items-center justify-between">
                    <h2 class="text-xl font-serif text-primary tracking-widest uppercase">Identity</h2>
                    <span class="text-[9px] font-bold tracking-widest text-slate-400 uppercase">Profile Settings</span>
                </header>
                
                <div class="bg-white border border-black/5 p-10 shadow-sm relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 -mr-16 -mt-16 rounded-full opacity-50 group-hover:scale-110 transition-transform duration-1000"></div>
                    
                    <form method="POST" action="" class="relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                            <div class="space-y-4">
                                <label class="block text-[10px] font-bold tracking-[0.3em] uppercase text-slate-500">Display Name</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required 
                                       class="w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-4 font-serif text-lg focus:outline-none focus:ring-0 focus:ring-offset-0 transition-colors duration-500">
                            </div>
                            <div class="space-y-4 opacity-70">
                                <label class="block text-[10px] font-bold tracking-[0.3em] uppercase text-slate-500">Email Reference</label>
                                <div class="relative">
                                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled 
                                           class="w-full bg-white border-b border-black/5 rounded-none px-4 py-4 font-serif text-lg text-slate-400 cursor-not-allowed">
                                    <i data-lucide="lock" class="absolute right-4 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-300"></i>
                                </div>
                                <p class="text-[8px] text-slate-300 font-bold uppercase tracking-widest pt-1">Restricted attribute</p>
                            </div>
                        </div>
                        <button type="submit" class="bg-primary text-white px-12 py-5 uppercase tracking-[0.3em] text-[10px] font-bold hover:bg-black focus:outline-none focus:ring-0 focus:ring-offset-0 transition-colors duration-500 shadow-xl active:scale-95">
                            Update Profile
                        </button>
                    </form>
                </div>
            </section>

            <!-- Orders Preview Section -->
            <section class="animate-in fade-in duration-1000 delay-150">
            <!-- Maison Elite Hub (Redesigned with Ultra-Premium Aesthetic) -->
            <section class="animate-in fade-in duration-1000 delay-150">
                <header class="mb-12 border-b border-black/10 pb-4 flex items-center justify-between">
                    <h2 class="text-xl font-serif text-primary tracking-widest uppercase italic">The Elite Hub</h2>
                    <span class="text-[9px] font-bold tracking-[0.3em] text-accent uppercase bg-accent/5 px-3 py-1 rounded-full">Maison Exclusive</span>
                </header>

                <div class="space-y-12">
                    <!-- Obsidian Cinematic Status Card -->
                    <div class="relative group overflow-hidden rounded-[4rem] bg-[#020617] p-16 text-white shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)] transition-all duration-1000 hover:shadow-primary/30">
                        <!-- Advanced Mesh Gradient Background -->
                        <div class="absolute inset-0 z-0">
                            <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-primary/40 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
                            <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-accent/10 rounded-full blur-[100px] translate-x-1/2 translate-y-1/2 opacity-40"></div>
                            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.03] mix-blend-overlay"></div>
                        </div>

                        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-16">
                            <div class="space-y-10 max-w-2xl">
                                <div class="inline-flex items-center gap-4 px-5 py-2.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-xl shadow-inner">
                                    <div class="w-2 h-2 rounded-full bg-accent animate-ping"></div>
                                    <span class="text-[10px] font-black tracking-[0.5em] uppercase text-white/90">Obsidian Status</span>
                                </div>
                                
                                <div class="space-y-4">
                                    <h3 class="text-6xl md:text-8xl font-serif tracking-tighter leading-none italic bg-gradient-to-r from-white via-white to-white/40 bg-clip-text text-transparent">
                                        Visionary <br><span class="not-italic font-light tracking-widest uppercase text-3xl md:text-5xl border-l border-accent/30 pl-8 ml-2 mt-4 inline-block">Curation</span>
                                    </h3>
                                    <p class="text-white/40 text-[11px] font-medium tracking-[0.5em] uppercase leading-relaxed max-w-md pt-4">
                                        Your presence marks a distinguished journey in luxury optics.
                                    </p>
                                </div>
                            </div>

                            <div class="flex-shrink-0 flex items-center justify-center">
                                <div class="relative group/badge">
                                    <!-- Rotating Aura -->
                                    <div class="absolute inset-0 rounded-full border border-white/5 scale-150 rotate-45 animate-[spin_20s_linear_infinite]"></div>
                                    <div class="absolute inset-0 rounded-full border border-accent/20 scale-125 -rotate-12 animate-[spin_15s_linear_infinite_reverse]"></div>
                                    
                                    <div class="w-64 h-64 rounded-full border-[0.5px] border-white/10 flex items-center justify-center relative p-4 backdrop-blur-sm bg-white/[0.02] group-hover:border-accent/50 transition-all duration-1000 shadow-2xl">
                                        <div class="absolute inset-0 rounded-full border-t border-accent animate-[spin_10s_linear_infinite] opacity-60"></div>
                                        <div class="text-center relative">
                                            <p class="text-[10px] font-black tracking-[0.4em] text-accent uppercase mb-3 drop-shadow-[0_0_8px_rgba(245,162,11,0.5)]">Tier Level</p>
                                            <p class="text-7xl font-serif tracking-tighter leading-none">VIII</p>
                                            <div class="h-1 w-8 bg-accent mx-auto mt-6 rounded-full opacity-50"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hub Features Grid (Asymmetrical & Modern) -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 h-full">
                        <!-- Member Curation (Spans 7 columns) -->
                        <div class="md:col-span-7 bg-white border border-black/[0.03] rounded-[3.5rem] p-12 hover:shadow-[0_40px_80px_-20px_rgba(0,0,0,0.1)] transition-all duration-1000 group relative overflow-hidden flex flex-col justify-between min-h-[450px]">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-full -mr-32 -mt-32 opacity-40 group-hover:scale-150 transition-transform duration-1000"></div>
                            
                            <div class="relative z-10">
                                <span class="text-[10px] font-black tracking-[0.5em] text-slate-300 uppercase block mb-10">Curation Vault</span>
                                <div class="flex flex-col sm:flex-row items-center gap-12">
                                    <div class="w-48 h-48 bg-slate-50 rounded-[2.5rem] overflow-hidden flex items-center justify-center p-8 border border-black/[0.02] transform group-hover:-rotate-6 group-hover:scale-110 transition-all duration-700 shadow-xl">
                                        <img src="/Optilux/uploads/products/curation_elite.png" 
                                             class="w-full h-full object-cover mix-blend-multiply transition-all duration-1000" alt="Curation">
                                    </div>
                                    <div class="text-center sm:text-left">
                                        <h4 class="text-3xl font-serif text-primary tracking-tight leading-tight mb-4 group-hover:text-accent transition-colors duration-500">The Avant-Garde <br>Selection</h4>
                                        <p class="text-[10px] font-bold text-slate-400 tracking-[0.3em] uppercase italic">Personalized for your aesthetic</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="relative z-10 pt-12">
                                <button class="group/btn relative px-10 py-5 overflow-hidden rounded-2xl bg-primary text-white text-[10px] font-black tracking-[0.4em] uppercase focus:outline-none focus:ring-0 focus:ring-offset-0 transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
                                    <span class="relative z-10">Access Gallery</span>
                                    <div class="absolute inset-0 bg-accent translate-y-full group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                                </button>
                            </div>
                        </div>

                        <!-- Hub Privileges (Spans 5 columns) -->
                        <div class="md:col-span-5 bg-slate-50 border border-black/[0.02] rounded-[3.5rem] p-12 flex flex-col justify-between group hover:bg-white hover:shadow-[0_40px_80px_-20px_rgba(0,0,0,0.1)] transition-all duration-1000">
                            <header>
                                <span class="text-[10px] font-black tracking-[0.5em] text-slate-400 mb-12 block uppercase">Privileges</span>
                            </header>
                            
                            <div class="grid grid-cols-2 gap-y-10 gap-x-6">
                                <div class="space-y-4 group/item">
                                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm group-hover/item:bg-primary group-hover/item:text-white transition-all duration-500 transform group-hover/item:rotate-12">
                                        <i data-lucide="headset" class="w-5 h-5 stroke-[1.5]"></i>
                                    </div>
                                    <p class="text-[9px] font-black tracking-widest text-primary uppercase">24/7 Concierge</p>
                                </div>
                                <div class="space-y-4 group/item">
                                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm group-hover/item:bg-accent group-hover/item:text-white transition-all duration-500 transform group-hover/item:rotate-12">
                                        <i data-lucide="sparkles" class="w-5 h-5 stroke-[1.5]"></i>
                                    </div>
                                    <p class="text-[9px] font-black tracking-widest text-primary uppercase">Priority Drop</p>
                                </div>
                                <div class="space-y-4 group/item">
                                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm group-hover/item:bg-primary group-hover/item:text-white transition-all duration-500 transform group-hover/item:rotate-12">
                                        <i data-lucide="globe" class="w-5 h-5 stroke-[1.5]"></i>
                                    </div>
                                    <p class="text-[9px] font-black tracking-widest text-primary uppercase">Global Lounge</p>
                                </div>
                                <div class="space-y-4 group/item">
                                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm group-hover/item:bg-accent group-hover/item:text-white transition-all duration-500 transform group-hover/item:rotate-12">
                                        <i data-lucide="shield-check" class="w-5 h-5 stroke-[1.5]"></i>
                                    </div>
                                    <p class="text-[9px] font-black tracking-widest text-primary uppercase">Elite Warranty</p>
                                </div>
                            </div>

                            <footer class="mt-12">
                                <p class="text-[10px] text-slate-400 font-medium leading-[2] italic tracking-wider border-t border-black/5 pt-8">
                                    Secured Obsidian status provides global access to first-contact acquisitions.
                                </p>
                            </footer>
                        </div>
                    </div>
                </div>
            </section>
            </section>

        </div>
    </div>
</div>

<?php 
// Ensure logout exists
if (!file_exists(__DIR__ . '/logout.php')) {
    file_put_contents(__DIR__ . '/logout.php', '<?php session_start(); session_destroy(); header("Location: /Optilux/login.php"); exit; ?>');
}
?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


