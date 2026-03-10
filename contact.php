<?php 
require_once __DIR__ . '/includes/header.php'; 

$success_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real app, send email or save to DB
    $success_msg = "Thank you for reaching out! We'll get back to you shortly.";
}
?>

<div class="bg-primary text-white py-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Contact Us</h1>
        <p class="text-xl text-slate-300 max-w-2xl mx-auto">Have a question about an order, our products, or just want to say hello? We'd love to hear from you.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
        
        <!-- Contact Form -->
        <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100">
            <h2 class="text-2xl font-bold text-primary mb-6">Send us a message</h2>
            
            <?php if ($success_msg): ?>
                <div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl border border-emerald-200 mb-6 flex items-center gap-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 flex-shrink-0"></i>
                    <p><?= $success_msg ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">First Name</label>
                        <input type="text" required class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Last Name</label>
                        <input type="text" required class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                    <input type="email" required class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition">
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Subject</label>
                    <select class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition bg-white">
                        <option>General Inquiry</option>
                        <option>Order Status</option>
                        <option>Returns & Exchanges</option>
                        <option>Product Information</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Message</label>
                    <textarea rows="5" required class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition"></textarea>
                </div>
                
                <button type="submit" class="w-full btn-primary text-lg">Send Message</button>
            </form>
        </div>

        <!-- Contact Info & Map -->
        <div class="flex flex-col justify-between">
            <div>
                <h2 class="text-3xl font-bold text-primary mb-8">Get in Touch</h2>
                
                <div class="space-y-8">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-slate-100 text-primary rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="map-pin" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-primary text-lg mb-1">Visit Our Store</h3>
                            <p class="text-slate-600">123 OptiLux Avenue<br>Fashion District<br>Mumbai, MH 400001</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-slate-100 text-primary rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="phone" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-primary text-lg mb-1">Call Us</h3>
                            <p class="text-slate-600">+91 98765 43210</p>
                            <p class="text-slate-500 text-sm mt-1">Mon-Sat from 10am to 7pm</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-slate-100 text-primary rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="mail" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-primary text-lg mb-1">Email Us</h3>
                            <p class="text-slate-600">hello@optilux.com</p>
                            <p class="text-slate-500 text-sm mt-1">We usually reply within 24 hours</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Map Placeholder -->
            <div class="mt-12 bg-slate-200 rounded-2xl h-64 flex items-center justify-center text-slate-500 relative overflow-hidden">
                <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&q=80&w=800" class="absolute inset-0 w-full h-full object-cover opacity-50 mix-blend-multiply">
                <div class="relative z-10 bg-white px-6 py-3 rounded-full shadow font-bold text-primary flex items-center gap-2">
                    <i data-lucide="map" class="w-5 h-5"></i> View on Map
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


