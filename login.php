<?php
require_once __DIR__ . '/includes/header.php';
if (isLoggedIn()) {
    header("Location: /Optilux/account.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($email && $password) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
            $error = "There is no such email address.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && $password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    setFlash('success', "Welcome back, Admin.");
                    header("Location: /Optilux/admin/index.php");
                } else {
                    // If there was a redirect URL, use it, else account
                    setFlash('success', "Welcome back, " . $_SESSION['user_name'] . ".");
                    $redirect = $_SESSION['redirect_after_login'] ?? '/Optilux/account.php';
                    unset($_SESSION['redirect_after_login']);
                    header("Location: " . $redirect);
                }
                exit;
            } else {
                $error = "Incorrect credentials provided.";
            }
        }
    } else {
        $error = "Complete all fields to continue.";
    }
}
?>

<div class="flex-grow flex min-h-[80vh]" style="margin:2rem; border:0.5px solid gray">
    <!-- Left side: Abstract Luxury Image -->
    <div class="hidden lg:block lg:w-1/2 relative bg-primary">
        <img src="https://images.unsplash.com/photo-1589782182703-2aaa69037b5b?auto=format&fit=crop&q=80&w=1200" alt="Luxury Atelier" class="absolute inset-0 w-full h-full object-cover opacity-60 grayscale mix-blend-luminosity">
        <div class="absolute inset-x-0 bottom-0 p-16 bg-gradient-to-t from-primary via-primary/80 to-transparent">
            <h2 class="text-white font-serif text-4xl tracking-widest uppercase mb-4">Welcome Back</h2>
            <p class="text-white/50 text-xs tracking-wider uppercase leading-relaxed max-w-md">Sign in to access your account, track orders, and manage your profile.</p>
        </div>
    </div>
    
    <!-- Right side: Login Form Couture -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 lg:p-24 bg-white">
        <div class="w-full max-w-md">
            <div class="text-center mb-16">
                <i data-lucide="key-round" class="w-8 h-8 mx-auto text-primary mb-6 stroke-[1]"></i>
                <h1 class="text-3xl font-serif text-primary tracking-widest uppercase mb-4">Login</h1>
                <p class="text-slate-400 text-xs tracking-[0.2em] uppercase">Enter your details below</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-primary text-white text-[10px] tracking-widest uppercase font-semibold px-6 py-4 mb-8 flex items-center justify-center gap-3">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-accent stroke-[1.5]"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-8">
                    <label class="block text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-3">Email Address</label>
                    <input type="email" name="email" required class="w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-4 font-serif text-primary focus:outline-none transition duration-300 placeholder:text-slate-300" placeholder="client@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <div class="mb-10">
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400">Password</label>
                        <a href="#" class="text-[9px] uppercase tracking-wider text-slate-400 hover:text-primary transition duration-300 border-b border-transparent hover:border-black/20 pb-0.5">Forgot?</a>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password" required class="w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-4 font-serif text-primary focus:outline-none transition duration-300">
                        <button type="button" onclick="togglePassword('password', 'pass-icon')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                            <i data-lucide="eye" id="pass-icon" class="w-4 h-4 stroke-[1.5]"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-primary text-white hover:bg-black transition-all duration-500 uppercase tracking-[0.2em] font-semibold text-xs py-5 flex items-center justify-center gap-3 border border-primary mb-8">
                    Sign In
                </button>
            </form>
            
            <div class="text-center border-t border-black/5 pt-8">
                <p class="text-slate-400 text-[10px] uppercase tracking-wider mb-4">Don't have an account?</p>
                <a href="/Optilux/register.php" class="inline-block text-primary text-[10px] uppercase tracking-[0.2em] font-bold border-b border-black/20 hover:border-primary pb-1 transition duration-300">
                    Create Account
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
    } else {
        input.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
    }
    lucide.createIcons();
}
</script>
