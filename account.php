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
                    
                    <a href="/Optilux/account.php" class="group flex items-center justify-between py-2 border-b border-primary transition-all duration-500">
                        <span class="text-[11px] font-serif tracking-[0.2em] uppercase text-primary font-bold">Profile Details</span>
                        <div class="w-1.5 h-1.5 bg-accent rounded-full shadow-[0_0_10px_rgba(251,191,36,0.6)]"></div>
                    </a>
                    
                    <a href="/Optilux/orders.php" class="group flex items-center justify-between py-2 border-b border-transparent hover:border-black/10 transition-all duration-500">
                        <span class="text-[11px] font-serif tracking-[0.2em] uppercase text-slate-400 group-hover:text-primary transition-colors">Order History</span>
                        <i data-lucide="package" class="w-3 h-3 text-slate-300 group-hover:text-accent transition-colors"></i>
                    </a>
                    
                    <a href="/Optilux/wishlist.php" class="group flex items-center justify-between py-2 border-b border-transparent hover:border-black/10 transition-all duration-500">
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-serif tracking-[0.2em] uppercase text-slate-400 group-hover:text-primary transition-colors">Wishlist</span>
                            <span class="text-[8px] font-bold bg-slate-50 text-slate-400 px-1.5 py-0.5 border border-black/5"><?= $wishlist_count ?></span>
                        </div>
                        <i data-lucide="heart" class="w-3 h-3 text-slate-300 group-hover:text-rose-400 transition-colors"></i>
                    </a>
                    
                    <a href="/Optilux/logout.php" class="group flex items-center justify-between py-2 mt-8 opacity-60 hover:opacity-100 transition-opacity">
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
                                       class="w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-4 font-serif text-lg focus:outline-none transition-all duration-500">
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
                        <button type="submit" class="bg-primary text-white px-12 py-5 uppercase tracking-[0.3em] text-[10px] font-bold hover:bg-black transition-all duration-500 shadow-xl active:scale-95">
                            Update Profile
                        </button>
                    </form>
                </div>
            </section>

            <!-- Orders Preview Section -->
            <section class="animate-in fade-in duration-1000 delay-150">
            <!-- Maison Elite Hub (Unique & Latest Replacement for Chronicle) -->
            <section class="animate-in fade-in duration-1000 delay-150">
                <header class="mb-12 border-b border-black/10 pb-4 flex items-center justify-between">
                    <h2 class="text-xl font-serif text-primary tracking-widest uppercase">Elite Hub</h2>
                    <span class="text-[9px] font-bold tracking-widest text-accent uppercase">Maison Exclusive</span>
                </header>

                <div class="space-y-12">
                    <!-- Membership Status Card -->
                    <div class="relative group overflow-hidden rounded-[3rem] bg-slate-950 p-12 text-white shadow-2xl transition-all duration-700 hover:shadow-primary/20">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/80 via-transparent to-accent/10 opacity-50"></div>
                        <!-- Animated Mesh Background -->
                        <div class="absolute inset-0 opacity-20 pointer-events-none">
                            <div class="absolute top-0 right-0 w-96 h-96 bg-accent rounded-full blur-[120px] -mr-48 -mt-48 animate-pulse"></div>
                            <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500 rounded-full blur-[100px] -ml-32 -mb-32 opacity-30"></div>
                        </div>

                        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-12">
                            <div class="space-y-6">
                                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-md">
                                    <div class="w-1.5 h-1.5 rounded-full bg-accent shadow-[0_0_10px_rgba(251,191,36,0.8)]"></div>
                                    <span class="text-[9px] font-bold tracking-[0.3em] uppercase">Obsidian Member</span>
                                </div>
                                <h3 class="text-5xl md:text-7xl font-serif tracking-tighter leading-none italic">The Elite Status</h3>
                                <p class="text-white/40 text-[10px] font-medium tracking-[0.4em] uppercase max-w-xs leading-relaxed">
                                    Your presence in the Maison marks a distinguished journey in visionary curation.
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="w-48 h-48 rounded-full border border-white/10 flex items-center justify-center relative p-2 group-hover:border-accent/40 transition-colors duration-700">
                                    <div class="absolute inset-0 rounded-full border-t-2 border-accent animate-[spin_8s_linear_infinite] opacity-40"></div>
                                    <div class="text-center">
                                        <p class="text-[8px] font-black tracking-widest text-accent uppercase mb-1">Tier Level</p>
                                        <p class="text-3xl font-serif tracking-widest">VIII</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hub Features Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Tailored Curation -->
                        <div class="bg-white border border-black/[0.03] rounded-[2.5rem] p-10 hover:shadow-2xl transition-all duration-700 group overflow-hidden">
                            <h4 class="text-[10px] font-black tracking-[0.4em] text-slate-300 uppercase mb-8">Member Curation</h4>
                            <div class="flex items-center gap-8 mb-10">
                                <div class="w-32 h-32 bg-slate-50 rounded-[2rem] overflow-hidden flex items-center justify-center p-6 border border-black/[0.02] transform group-hover:scale-110 transition-transform duration-700">
                                    <img src="/Optilux/uploads/products/curation_elite.png" 
                                         class="w-full h-full object-contain mix-blend-multiply group-hover:scale-110 transition-all duration-700" alt="Curation">
                                </div>
                                <div>
                                    <p class="text-xl font-serif text-primary tracking-tight leading-tight mb-2">The Avant-Garde Selection</p>
                                    <p class="text-[9px] font-bold text-accent tracking-widest uppercase italic">Tailored for you</p>
                                </div>
                            </div>
                            <button class="w-full bg-slate-50 text-primary py-4 text-[9px] font-black tracking-[0.3em] uppercase rounded-2xl hover:bg-primary hover:text-white transition-all duration-500">Access Gallery</button>
                        </div>

                        <!-- Hub Privileges -->
                        <div class="bg-slate-50 border border-black/[0.02] rounded-[2.5rem] p-10 flex flex-col justify-between">
                            <h4 class="text-[10px] font-black tracking-[0.4em] text-slate-400 mb-10 uppercase">Member Privileges</h4>
                            <div class="grid grid-cols-2 gap-6 mb-10">
                                <div class="space-y-3">
                                    <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-sm">
                                        <i data-lucide="headset" class="w-3.5 h-3.5 text-primary stroke-[1.5]"></i>
                                    </div>
                                    <p class="text-[8px] font-bold tracking-widest text-primary uppercase">24/7 Concierge</p>
                                </div>
                                <div class="space-y-3">
                                    <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-sm">
                                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-accent stroke-[1.5]"></i>
                                    </div>
                                    <p class="text-[8px] font-bold tracking-widest text-primary uppercase">Priority Drop</p>
                                </div>
                                <div class="space-y-3">
                                    <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-sm">
                                        <i data-lucide="globe" class="w-3.5 h-3.5 text-primary stroke-[1.5]"></i>
                                    </div>
                                    <p class="text-[8px] font-bold tracking-widest text-primary uppercase">Global Lounge</p>
                                </div>
                                <div class="space-y-3">
                                    <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-sm">
                                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-primary stroke-[1.5]"></i>
                                    </div>
                                    <p class="text-[8px] font-bold tracking-widest text-primary uppercase">Elite Warranty</p>
                                </div>
                            </div>
                            <p class="text-[9px] text-slate-400 font-medium leading-relaxed italic tracking-wide">
                                Your Obsidian status unlocks worldwide premium servicing and first-contact acquisition.
                            </p>
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


