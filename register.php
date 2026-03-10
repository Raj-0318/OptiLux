<?php
require_once __DIR__ . '/includes/header.php';
if (isLoggedIn()) {
    header("Location: /Optilux/account.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (!$name || !$email || !$password) {
        $error = "Complete all fields to continue.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
        $error = "There is no such email address.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "Password must include uppercase, lowercase, and numbers.";
    } else {
        // Check email
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->execute([$email]);
        if ($stmt_check->fetch()) {
            $error = "Email already registered.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            try {
                $stmt->execute([$name, $email, $password]);
                $user_id = $conn->lastInsertId();
                // Redirect to login with success flash message
                setFlash('success', "Your account has been created. Please sign in.");
                header("Location: /Optilux/login.php");
                exit;
            } catch (PDOException $e) {
                $error = "An error occurred. Please attempt again.";
            }
        }
    }
}
?>

<div class="flex-grow flex min-h-[80vh]" style="margin:2rem; border:0.5px solid gray">
    <!-- Left side: Abstract Luxury Image -->
    <div class="hidden lg:block lg:w-1/2 relative bg-primary order-last">
        <img src="https://images.unsplash.com/photo-1556306535-0f09a536f0bff?auto=format&fit=crop&q=80&w=1200" alt="Luxury Atelier Experience" class="absolute inset-0 w-full h-full object-cover opacity-60 grayscale mix-blend-luminosity">
        <div class="absolute inset-x-0 bottom-0 p-16 bg-gradient-to-t from-primary via-primary/80 to-transparent">
            <h2 class="text-white font-serif text-4xl tracking-widest uppercase mb-4">Join Us</h2>
            <p class="text-white/50 text-xs tracking-wider uppercase leading-relaxed max-w-md">Create an account for faster checkout and to track your orders.</p>
        </div>
    </div>
    
    <!-- Right side: Register Form Couture -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 lg:p-24 bg-white">
        <div class="w-full max-w-md">
            <div class="text-center mb-16">
                <i data-lucide="award" class="w-8 h-8 mx-auto text-primary mb-6 stroke-[1]"></i>
                <h1 class="text-3xl font-serif text-primary tracking-widest uppercase mb-4">Create Account</h1>
                <p class="text-slate-400 text-xs tracking-[0.2em] uppercase">Fill in your details below</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-primary text-white text-[10px] tracking-widest uppercase font-semibold px-6 py-4 mb-8 flex items-center justify-center gap-3">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-accent stroke-[1.5]"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-6">
                    <label class="block text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-3">Full Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-4 font-serif text-primary focus:outline-none transition duration-300" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                
                <div class="mb-6">
                    <label class="block text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-3">Email Address</label>
                    <input type="email" name="email" required class="w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-4 font-serif text-primary focus:outline-none transition duration-300" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <div class="mb-6">
                    <label class="block text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-3">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required class="w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-4 font-serif text-primary focus:outline-none transition duration-300">
                        <button type="button" onclick="togglePassword('password', 'pass-icon')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                            <i data-lucide="eye" id="pass-icon" class="w-4 h-4 stroke-[1.5]"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-10">
                    <label class="block text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-3">Confirm Password</label>
                    <div class="relative">
                        <input type="password" name="confirm_password" id="confirm_password" required class="w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-4 font-serif text-primary focus:outline-none transition duration-300">
                        <button type="button" onclick="togglePassword('confirm_password', 'confirm-pass-icon')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                            <i data-lucide="eye" id="confirm-pass-icon" class="w-4 h-4 stroke-[1.5]"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-primary text-white hover:bg-black transition-all duration-500 uppercase tracking-[0.2em] font-semibold text-xs py-5 flex items-center justify-center gap-3 border border-primary mb-8">
                    Sign Up
                </button>
            </form>
            
            <div class="text-center border-t border-black/5 pt-8">
                <p class="text-slate-400 text-[10px] uppercase tracking-wider mb-4">Already have an account?</p>
                <a href="/Optilux/login.php" class="inline-block text-primary text-[10px] uppercase tracking-[0.2em] font-bold border-b border-black/20 hover:border-primary pb-1 transition duration-300">
                    Login
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
