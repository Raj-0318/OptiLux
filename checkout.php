<?php
require_once __DIR__ . '/includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Get cart items
$stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.sale_price, p.stock, p.image
                        FROM cart c
                        JOIN products p ON c.product_id = p.id
                        WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    header("Location: /Optilux/cart.php");
    exit;
}

$subtotal = 0;
foreach($cart_items as $item) {
    if ($item['stock'] < $item['quantity']) {
        // Handle out of stock items in real implementation
    }
    $curr_price = $item['sale_price'] ?: $item['price'];
    $subtotal += $curr_price * $item['quantity'];
}
$shipping = $subtotal > 999 ? 0 : 99;
$total = $subtotal + $shipping;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address_line = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $zip = trim($_POST['zip'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    
    // Additional payment data
    $upi_id = trim($_POST['upi_id'] ?? '');
    $card_number = trim($_POST['card_number'] ?? '');
    
    if (!$first_name || !$last_name || !$email || !$phone || !$address_line || !$city || !$state || !$zip || !$payment_method) {
        $error = "All billing fields are required.";
    } elseif ($payment_method === 'UPI' && (empty($upi_id) || !str_contains($upi_id, '@'))) {
        $error = "Please enter a valid UPI ID (e.g., name@bank).";
    } elseif ($payment_method === 'Card') {
        $clean_card = str_replace(' ', '', $card_number);
        if (strlen($clean_card) !== 16) {
            $error = "Card number must be 16 digits.";
        } else {
            // Server-side Luhn Check
            $sum = 0;
            for ($i = 0; $i < 16; $i++) {
                $digit = (int)$clean_card[15 - $i];
                if ($i % 2 === 1) {
                    $digit *= 2;
                    if ($digit > 9) $digit -= 9;
                }
                $sum += $digit;
            }
            if ($sum % 10 !== 0) {
                $error = "Invalid card number (Luhn check failed).";
            }
        }
    } else {
        $full_address = "$first_name $last_name\n$address_line\n$city, $state $zip\nPhone: $phone";
        $method_detail = $payment_method;
        if($payment_method === 'UPI') $method_detail .= " ($upi_id)";
        if($payment_method === 'Card') $method_detail .= " (Ends in " . substr(str_replace(' ', '', $card_number), -4) . ")";
        
        try {
            $conn->beginTransaction();
            
            // Create Order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, address, payment_method) VALUES (?, ?, 'pending', ?, ?)");
            $stmt->execute([$user_id, $total, $full_address, $method_detail]);
            $order_id = $conn->lastInsertId();
            
            // Insert Order Items and Update Stock
            foreach($cart_items as $item) {
                $curr_price = $item['sale_price'] ?: $item['price'];
                $stmt_oi = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt_oi->execute([$order_id, $item['product_id'], $item['quantity'], $curr_price]);
                
                $stmt_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt_stock->execute([$item['quantity'], $item['product_id']]);
            }
            
            // Clear Cart
            $stmt_clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt_clear->execute([$user_id]);
            
            $conn->commit();
            
            header("Location: /Optilux/order-confirm.php?id=" . $order_id);
            exit;
            
        } catch (Exception $e) {
            $conn->rollBack();
            $error = "An error occurred while processing your order. Please try again.";
        }
    }
}

// Fetch user data to pre-fill
$stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();
$name_parts = explode(' ', $user['name'] ?? '', 2);
?>

<div class="bg-primary text-white py-16 border-b border-white/5">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-serif tracking-widest uppercase mb-4">Checkout</h1>
        <p class="text-white/40 text-xs tracking-[0.2em] font-light uppercase">Complete Your Order</p>
    </div>
</div>

<div class="border-b border-black/5 py-4">
    <div class="max-w-7xl mx-auto px-6 text-[10px] uppercase tracking-widest font-semibold text-slate-400">
        <a href="/Optilux/" class="hover:text-primary transition duration-300">Optilux</a> / 
        <a href="/Optilux/cart.php" class="hover:text-primary transition duration-300">Cart</a> / 
        <span class="text-primary">Checkout</span>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-20 flex flex-col lg:flex-row gap-16">
    <!-- Billing details Minimal -->
    <div class="w-full lg:w-2/3">
        <?php if ($error): ?>
            <div class="bg-primary border border-accent text-white px-6 py-4 mb-8 flex items-center gap-4 text-xs tracking-wider uppercase">
                <i data-lucide="alert-circle" class="w-5 h-5 text-accent stroke-[1.5]"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="checkoutForm">
            <div class="mb-16" id="billingSection">
                <h2 class="text-xl font-serif text-primary tracking-widest uppercase mb-8 border-b border-black/10 pb-4 flex items-center justify-between">
                    Billing Information
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500 opacity-0 transition-opacity duration-300" id="billingCheck"></i>
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-slate-500 mb-3">First Name</label>
                        <input type="text" name="first_name" id="firstName" required value="<?= htmlspecialchars($name_parts[0] ?? '') ?>" class="billing-input w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-3 font-serif focus:outline-none transition duration-300">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-slate-500 mb-3">Last Name</label>
                        <input type="text" name="last_name" id="lastName" required value="<?= htmlspecialchars($name_parts[1] ?? '') ?>" class="billing-input w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-3 font-serif focus:outline-none transition duration-300">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-slate-500 mb-3">Email Address</label>
                        <input type="email" name="email" id="email" required value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="billing-input w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-3 font-serif focus:outline-none transition duration-300">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-slate-500 mb-3">Phone Number</label>
                        <input type="tel" name="phone" id="phone" required class="billing-input w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-3 font-serif focus:outline-none transition duration-300">
                    </div>
                </div>
                
                <div class="mb-8">
                    <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-slate-500 mb-3">Street Address</label>
                    <input type="text" name="address" id="address" required placeholder="House number and street name" class="billing-input w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-3 font-serif focus:outline-none transition duration-300">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div>
                        <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-slate-500 mb-3">City</label>
                        <input type="text" name="city" id="city" required class="billing-input w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-3 font-serif focus:outline-none transition duration-300">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-slate-500 mb-3">State</label>
                        <input type="text" name="state" id="state" required class="billing-input w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-3 font-serif focus:outline-none transition duration-300">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-slate-500 mb-3">Postal Code</label>
                        <input type="text" name="zip" id="zip" required class="billing-input w-full bg-slate-50 border-b border-black/10 focus:border-primary focus:bg-white rounded-none px-4 py-3 font-serif focus:outline-none transition duration-300">
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="button" id="continueToPayment" class="bg-primary text-white px-10 py-4 uppercase tracking-[0.2em] text-[10px] font-bold hover:bg-black transition-all duration-300 opacity-50 pointer-events-none" onclick="unlockPaymentStep()">
                        Continue to Payment Method
                    </button>
                </div>
            </div>
            
            <div id="paymentSection" class="hidden opacity-0 translate-y-8 transition-all duration-700">
                <h2 class="text-xl font-serif text-primary tracking-widest uppercase mb-8 border-b border-black/10 pb-4 flex items-center justify-between">
                    Payment Method
                    <span id="lockStatus" class="text-[9px] font-bold tracking-widest text-emerald-500 flex items-center gap-2">
                        <i data-lucide="unlock" class="w-3 h-3"></i> OPTILUX VERIFIED
                    </span>
                </h2>
                
                <div class="space-y-4">
                    <!-- COD -->
                    <label class="payment-option flex flex-col p-6 bg-slate-50 border border-transparent hover:border-black/10 cursor-pointer group hover:bg-white transition duration-300">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-4">
                                <input type="radio" name="payment_method" value="COD" checked class="w-4 h-4 text-primary bg-transparent border-black/20 focus:ring-primary focus:ring-offset-0">
                                <span class="font-serif text-slate-500 group-hover:text-primary transition duration-300 text-lg tracking-wider">Cash on Delivery</span>
                            </div>
                            <i data-lucide="banknote" class="w-6 h-6 text-slate-400 group-hover:text-primary transition duration-300 stroke-[1]"></i>
                        </div>
                    </label>
                    
                    <!-- UPI -->
                    <label class="payment-option flex flex-col p-6 bg-slate-50 border border-transparent hover:border-black/10 cursor-pointer group hover:bg-white transition duration-300">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-4">
                                <input type="radio" name="payment_method" value="UPI" class="w-4 h-4 text-primary bg-transparent border-black/20 focus:ring-primary focus:ring-offset-0">
                                <span class="font-serif text-slate-500 group-hover:text-primary transition duration-300 text-lg tracking-wider">UPI / Transfer</span>
                            </div>
                            <i data-lucide="smartphone" class="w-6 h-6 text-slate-400 group-hover:text-primary transition duration-300 stroke-[1]"></i>
                        </div>
                        <div id="upiForm" class="hidden mt-6 pt-6 border-t border-black/5 animate-in fade-in slide-in-from-top-1">
                            <div class="mb-4">
                                <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-slate-500 mb-3">Enter UPI ID</label>
                                <div class="relative">
                                    <input type="text" name="upi_id" id="upiId" placeholder="username@upi" class="w-full bg-white border border-black/10 rounded-none px-4 py-3 font-serif focus:outline-none focus:border-primary transition">
                                    <div id="upiValidation" class="absolute right-4 top-1/2 -translate-y-1/2">
                                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500 hidden" id="upiValidIcon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-4 inline-block border border-black/5 rounded-xl">
                                <img id="upiQR" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=upi://pay?pa=optilux@bank&am=<?= $total ?>&cu=INR" alt="UPI QR" class="w-32 h-32 grayscale opacity-50 transition duration-500">
                                <p class="text-[8px] text-center mt-3 text-slate-400 font-bold tracking-widest uppercase">SCAN TO PAY ₹<?= number_format($total, 2) ?></p>
                            </div>
                        </div>
                    </label>
                    
                    <!-- Card -->
                    <label class="payment-option flex flex-col p-6 bg-slate-50 border border-transparent hover:border-black/10 cursor-pointer group hover:bg-white transition duration-300">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-4">
                                <input type="radio" name="payment_method" value="Card" class="w-4 h-4 text-primary bg-transparent border-black/20 focus:ring-primary focus:ring-offset-0">
                                <span class="font-serif text-slate-500 group-hover:text-primary transition duration-300 text-lg tracking-wider">Credit / Debit Card</span>
                            </div>
                            <i data-lucide="credit-card" class="w-6 h-6 text-slate-400 group-hover:text-primary transition duration-300 stroke-[1]"></i>
                        </div>
                        <div id="cardForm" class="hidden mt-6 pt-6 border-t border-black/5 animate-in fade-in slide-in-from-top-1">
                            <div class="mb-4">
                                <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-slate-500 mb-3">Card Number</label>
                                <div class="relative">
                                    <input type="text" name="card_number" id="cardNumber" maxlength="19" placeholder="0000 0000 0000 0000" class="w-full bg-white border border-black/10 rounded-none px-4 py-3 font-serif focus:outline-none focus:border-primary transition">
                                    <div id="cardValidation" class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                                        <i data-lucide="x-circle" class="w-4 h-4 text-rose-500 hidden" id="cardInvalidIcon"></i>
                                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500 hidden" id="cardValidIcon"></i>
                                    </div>
                                </div>
                                <p id="cardError" class="text-[9px] text-rose-500 font-bold mt-2 hidden uppercase tracking-widest">Invalid Card Number</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-slate-500 mb-3">Expiry Date</label>
                                    <input type="text" name="card_expiry" id="cardExpiry" maxlength="5" placeholder="MM/YY" class="w-full bg-white border border-black/10 rounded-none px-4 py-3 font-serif focus:outline-none focus:border-primary transition">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-slate-500 mb-3">CVV</label>
                                    <input type="text" name="card_cvv" id="cardCvv" maxlength="3" placeholder="000" class="w-full bg-white border border-black/10 rounded-none px-4 py-3 font-serif focus:outline-none focus:border-primary transition">
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Order Summary Minimal -->
    <div class="w-full lg:w-1/3">
        <div class="bg-primary text-white p-10 sticky top-32">
            <h2 class="text-lg font-serif tracking-widest uppercase border-b border-white/10 pb-6 mb-8 text-center">Summary</h2>
            
            <div class="space-y-6 mb-8 border-b border-white/10 pb-8">
                <?php foreach($cart_items as $item): 
                    $curr_price = $item['sale_price'] ?: $item['price'];    
                ?>
                <div class="flex justify-between items-start text-xs font-light tracking-wide">
                    <div class="flex gap-4 flex-grow pr-4">
                        <?php 
                            $img = !empty($item['image']) ? $item['image'] : '';
                            if ($img && strpos($img, 'http') !== 0 && strpos($img, '/') !== 0) {
                                $img = '/Optilux/' . $img;
                            }
                        ?>
                        <div class="w-16 h-16 bg-[#F7F7F7] flex-shrink-0 overflow-hidden">
                            <img src="<?= $img ?>" class="w-full h-full object-cover mix-blend-multiply" onerror="this.src='https://images.unsplash.com/photo-1572635196237-14b3f281503f?auto=format&fit=crop&q=80&w=200'">
                        </div>
                        <div>
                            <p class="font-bold text-white mb-1 tracking-widest line-clamp-2"><?= htmlspecialchars($item['name']) ?></p>
                            <p class="text-white/40 text-[9px] uppercase tracking-[0.2em] font-bold">Qty: <?= $item['quantity'] ?></p>
                        </div>
                    </div>
                    <span class="font-serif text-accent flex-shrink-0"><?= formatPrice($curr_price * $item['quantity']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="space-y-4 text-[10px] uppercase font-bold tracking-[0.2em] text-white/50 border-b border-white/10 pb-8 mb-8">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span class="text-white"><?= formatPrice($subtotal) ?></span>
                </div>
                <div class="flex justify-between">
                    <span>Shipping</span>
                    <span class="text-white"><?= $shipping == 0 ? 'Free' : formatPrice($shipping) ?></span>
                </div>
            </div>
            
            <div class="flex flex-col items-center mb-10 text-center">
                <span class="text-accent text-[9px] font-bold tracking-[0.3em] uppercase mb-4 block">Total (VAT Included)</span>
                <span class="text-4xl font-serif text-white tracking-widest"><?= formatPrice($total) ?></span>
            </div>
            
            <button type="button" id="placeOrderBtn" onclick="submitOrder();" class="block w-full bg-white text-primary hover:bg-accent hover:text-white transition-all duration-500 uppercase tracking-[0.2em] font-semibold text-xs py-5 text-center border border-transparent disabled:opacity-50 disabled:cursor-not-allowed">
                Place Order
            </button>
            
            <p class="text-[9px] text-white/30 mt-6 text-center tracking-wider uppercase leading-relaxed">By confirming, you agree to Optilux's Privacy Policy & Terms of Service.</p>
        </div>
    </div>
</div>

<script>
// Section Unlocking Logic
const billingInputs = document.querySelectorAll('.billing-input');
const continueBtn = document.getElementById('continueToPayment');
const billingCheck = document.getElementById('billingCheck');
const paymentSection = document.getElementById('paymentSection');

function checkBillingCompletion() {
    let allFilled = true;
    billingInputs.forEach(input => {
        if (!input.value.trim()) allFilled = false;
    });

    if (allFilled) {
        continueBtn.classList.remove('opacity-50', 'pointer-events-none');
        billingCheck.classList.remove('opacity-0');
        billingCheck.classList.add('opacity-100');
    } else {
        continueBtn.classList.add('opacity-50', 'pointer-events-none');
        billingCheck.classList.remove('opacity-100');
        billingCheck.classList.add('opacity-0');
    }
}

function unlockPaymentStep() {
    // Show and Animate payment section
    paymentSection.classList.remove('hidden');
    setTimeout(() => {
        paymentSection.classList.remove('opacity-0', 'translate-y-8');
        paymentSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 50);
    
    // Disable billing fields to prevent accidental changes
    billingInputs.forEach(input => input.setAttribute('readonly', true));
    continueBtn.innerHTML = '<i data-lucide="check" class="w-3 h-3 inline mr-2 text-emerald-500"></i> BILLING VERIFIED';
    continueBtn.classList.replace('bg-primary', 'bg-slate-200');
    continueBtn.classList.replace('text-white', 'text-slate-500');
    continueBtn.classList.add('pointer-events-none');
    lucide.createIcons();
}

billingInputs.forEach(input => {
    input.addEventListener('input', checkBillingCompletion);
});

// Run initially
checkBillingCompletion();

// Payment Method Selection UI
const methodRadios = document.querySelectorAll('input[name="payment_method"]');
const upiForm = document.getElementById('upiForm');
const cardForm = document.getElementById('cardForm');
const upiIdInput = document.getElementById('upiId');
const upiQR = document.getElementById('upiQR');

methodRadios.forEach(radio => {
    radio.addEventListener('change', () => {
        // Reset styles for all
        document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('border-primary', 'bg-white'));
        document.querySelectorAll('.payment-option').forEach(opt => opt.classList.add('bg-slate-50', 'border-transparent'));
        
        // Highlight selected
        const label = radio.closest('.payment-option');
        label.classList.remove('bg-slate-50', 'border-transparent');
        label.classList.add('border-primary', 'bg-white');

        // Show/Hide forms
        upiForm.classList.add('hidden');
        cardForm.classList.add('hidden');
        if (radio.value === 'UPI') upiForm.classList.remove('hidden');
        if (radio.value === 'Card') cardForm.classList.remove('hidden');
    });
});

// UPI QR Interaction & Validation
const upiValidIcon = document.getElementById('upiValidIcon');
upiIdInput.addEventListener('input', (e) => {
    if (e.target.value.includes('@')) {
        upiQR.classList.remove('grayscale', 'opacity-50');
        upiValidIcon.classList.remove('hidden');
    } else {
        upiQR.classList.add('grayscale', 'opacity-50');
        upiValidIcon.classList.add('hidden');
    }
});

// Card Validation (Luhn Algorithm)
const cardNumberInput = document.getElementById('cardNumber');
const cardExpiryInput = document.getElementById('cardExpiry');
const cardCvvInput = document.getElementById('cardCvv');
const cardValidIcon = document.getElementById('cardValidIcon');
const cardInvalidIcon = document.getElementById('cardInvalidIcon');
const cardError = document.getElementById('cardError');

function validateCardLuhn(number) {
    let sum = 0;
    let isSecond = false;
    number = number.replace(/\s+/g, '');
    for (let i = number.length - 1; i >= 0; i--) {
        let d = parseInt(number.charAt(i));
        if (isSecond) {
            d = d * 2;
            if (d > 9) d = d - 9;
        }
        sum += d;
        isSecond = !isSecond;
    }
    return (sum % 10 == 0);
}

cardNumberInput.addEventListener('input', (e) => {
    // Formatting
    let val = e.target.value.replace(/\D/g, '');
    let formatted = val.match(/.{1,4}/g)?.join(' ') || '';
    e.target.value = formatted;

    // Validation
    if (val.length === 16) {
        if (validateCardLuhn(val)) {
            cardValidIcon.classList.remove('hidden');
            cardInvalidIcon.classList.add('hidden');
            cardError.classList.add('hidden');
        } else {
            cardInvalidIcon.classList.remove('hidden');
            cardValidIcon.classList.add('hidden');
            cardError.classList.remove('hidden');
        }
    } else {
        cardValidIcon.classList.add('hidden');
        cardInvalidIcon.classList.add('hidden');
        cardError.classList.add('hidden');
    }
});

// Expiry Formatting
cardExpiryInput.addEventListener('input', (e) => {
    let val = e.target.value.replace(/\D/g, '');
    if (val.length > 2) {
        e.target.value = val.slice(0, 2) + '/' + val.slice(2, 4);
    } else {
        e.target.value = val;
    }
});

cardExpiryInput.addEventListener('keydown', (e) => {
    if (e.key === 'Backspace' && cardExpiryInput.value.length === 3) {
        cardExpiryInput.value = cardExpiryInput.value.slice(0, 2);
    }
});

function submitOrder() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const placeBtn = document.getElementById('placeOrderBtn');
    
    if (selectedMethod === 'UPI') {
        if (!upiIdInput.value.includes('@')) {
            alert('Please enter a valid UPI ID (e.g., user@bank)');
            return;
        }
    } else if (selectedMethod === 'Card') {
        const val = cardNumberInput.value.replace(/\s+/g, '');
        if (val.length !== 16 || !validateCardLuhn(val)) {
            alert('Please enter a valid credit card number');
            return;
        }
        if (cardExpiryInput.value.length < 5) {
            alert('Please enter card expiry date (MM/YY)');
            return;
        }
        if (cardCvvInput.value.length < 3) {
            alert('Please enter 3-digit CVV');
            return;
        }
    }
    
    // Loading State
    placeBtn.disabled = true;
    placeBtn.innerHTML = '<span class="flex items-center justify-center gap-3"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> PLACING ORDER...</span>';
    lucide.createIcons();
    
    setTimeout(() => {
        document.getElementById('checkoutForm').submit();
    }, 800);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
