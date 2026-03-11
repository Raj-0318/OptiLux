<?php 
require_once __DIR__ . '/includes/header.php'; 

$success_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success_msg = "Your inquiry has been received by the Maison Concierge. We shall respond shortly.";
}
?>

<!-- Cinematic Hero: The Vision -->
<section class="relative h-[80vh] bg-primary flex items-center justify-center overflow-hidden border-b border-white/5">
    <!-- Sophisticated Background -->
    <div class="absolute inset-0 z-0">
        <img src="https://i.pinimg.com/1200x/23/d6/d1/23d6d17a8aab161b6979b794645c3ad1.jpg" 
             alt="Maison Office" class="w-full h-full object-cover opacity-20 grayscale scale-110 animate-[pulse_8s_ease-in-out_infinite]">
        <div class="absolute inset-0 bg-gradient-to-t from-primary via-transparent to-primary/80"></div>
    </div>
    
    <div class="relative z-10 text-center px-6 max-w-5xl animate-in fade-in slide-in-from-bottom-8 duration-1000">
        <span class="text-accent text-[10px] font-bold tracking-[0.5em] uppercase mb-8 block">Estate / Concierge</span>
        <h1 class="text-6xl md:text-8xl font-serif text-white tracking-widest leading-none mb-10 italic">
            Connect <br><span class="not-italic text-white/40 tracking-[0.2em] font-light uppercase text-3xl md:text-5xl mt-6 block">Optilux</span>
        </h1>
        <p class="text-white/40 text-xs md:text-sm font-light max-w-2xl mx-auto leading-relaxed tracking-widest uppercase mb-12">
            Establish a direct narrative with the Maison. We curate responses with artisanal precision for every inquiry.
        </p>
        <div class="w-[1px] h-20 bg-gradient-to-b from-accent to-transparent mx-auto"></div>
    </div>
</section>

<section class="bg-white py-32">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-24 items-start">
            
            <!-- Contact Form: Floating Elegance -->
            <div class="lg:col-span-7 bg-white p-12 md:p-16 rounded-[4rem] shadow-[0_40px_100px_-20px_rgba(0,0,0,0.1)] border border-black/[0.03] animate-in fade-in slide-in-from-left-8 duration-1000 delay-300">
                <div class="mb-12">
                    <span class="text-slate-300 text-[10px] font-black tracking-[0.4em] uppercase block mb-4">The Inquiry</span>
                    <h2 class="text-3xl font-serif text-primary tracking-tight leading-none italic">Compose a Message</h2>
                </div>
                
                <?php if ($success_msg): ?>
                    <div class="bg-emerald-50 text-emerald-600 p-6 rounded-[2rem] border border-emerald-100 mb-10 flex items-center gap-4 animate-in zoom-in duration-500">
                        <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                        </div>
                        <p class="text-[11px] font-bold tracking-widest uppercase italic"><?= $success_msg ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div class="space-y-4 group">
                            <label class="block text-[9px] font-black tracking-[0.4em] text-slate-400 uppercase transition-colors group-focus-within:text-accent">First Name</label>
                            <input type="text" required class="w-full bg-transparent border-b border-black/5 py-4 text-xs font-light tracking-widest text-primary focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-accent transition-colors duration-500 placeholder:text-slate-200" placeholder="E.g. Alexander">
                        </div>
                        <div class="space-y-4 group">
                            <label class="block text-[9px] font-black tracking-[0.4em] text-slate-400 uppercase transition-colors group-focus-within:text-accent">Last Name</label>
                            <input type="text" required class="w-full bg-transparent border-b border-black/5 py-4 text-xs font-light tracking-widest text-primary focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-accent transition-colors duration-500 placeholder:text-slate-200" placeholder="E.g. Sterling">
                        </div>
                    </div>
                    
                    <div class="space-y-4 group">
                        <label class="block text-[9px] font-black tracking-[0.4em] text-slate-400 uppercase transition-colors group-focus-within:text-accent">Formal Email</label>
                        <input type="email" required class="w-full bg-transparent border-b border-black/5 py-4 text-xs font-light tracking-widest text-primary focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-accent transition-colors duration-500 placeholder:text-slate-200" placeholder="E.g. sterling@curation.com">
                    </div>
                    
                    <div class="space-y-4 group">
                        <label class="block text-[9px] font-black tracking-[0.4em] text-slate-400 uppercase transition-colors group-focus-within:text-accent">Nature of Inquiry</label>
                        <div class="relative">
                            <select class="w-full bg-transparent border-b border-black/5 py-4 text-xs font-light tracking-widest text-primary focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-accent transition-colors duration-500 appearance-none cursor-pointer">
                                <option>General Inquiry</option>
                                <option>The Elite Registry</option>
                                <option>Bespoke Acquisition</option>
                                <option>Artisanal Support</option>
                            </select>
                            <div class="absolute right-0 bottom-4 pointer-events-none">
                                <i data-lucide="chevron-down" class="w-3 h-3 text-slate-300"></i>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 group">
                        <label class="block text-[9px] font-black tracking-[0.4em] text-slate-400 uppercase transition-colors group-focus-within:text-accent">Narrative</label>
                        <textarea rows="4" required class="w-full bg-transparent border-b border-black/5 py-4 text-xs font-light tracking-widest leading-relaxed text-primary focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-accent transition-colors duration-500 placeholder:text-slate-200 resize-none" placeholder="We value your perspective..."></textarea>
                    </div>
                    
                    <button type="submit" class="group/btn relative px-12 py-6 overflow-hidden rounded-2xl bg-primary text-white text-[10px] font-black tracking-[0.5em] uppercase transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
                        <span class="relative z-10">Transmit Message</span>
                        <div class="absolute inset-0 bg-accent translate-y-full group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                    </button>
                </form>
            </div>

            <!-- The Elite Concierge: Info & Map -->
            <div class="lg:col-span-5 flex flex-col justify-between self-stretch animate-in fade-in slide-in-from-right-8 duration-1000 delay-500">
                <div class="space-y-20">
                    <div class="space-y-12">
                        <div class="flex items-start gap-8 group">
                            <div class="w-14 h-14 bg-slate-50 text-primary rounded-[1.5rem] flex items-center justify-center flex-shrink-0 border border-black/[0.03] group-hover:bg-primary group-hover:text-white transition-all duration-500 transform group-hover:-rotate-12">
                                <i data-lucide="map-pin" class="w-6 h-6 stroke-[1.2]"></i>
                            </div>
                            <div>
                                <h3 class="text-[10px] font-black tracking-[0.4em] text-slate-400 uppercase mb-4">The Flagship Maison</h3>
                                <p class="text-xl font-serif text-primary tracking-tight leading-relaxed italic">123 OptiLux Avenue, <br>Fashion District, Mumbai</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-8 group">
                            <div class="w-14 h-14 bg-slate-50 text-accent rounded-[1.5rem] flex items-center justify-center flex-shrink-0 border border-accent/5 group-hover:bg-accent group-hover:text-white transition-all duration-500 transform group-hover:rotate-12">
                                <i data-lucide="phone" class="w-6 h-6 stroke-[1.2]"></i>
                            </div>
                            <div>
                                <h3 class="text-[10px] font-black tracking-[0.4em] text-slate-400 uppercase mb-4">Direct Dispatch</h3>
                                <p class="text-xl font-serif text-primary tracking-tight leading-relaxed italic">+91 98765 43210</p>
                                <p class="text-[9px] font-bold text-slate-300 tracking-[0.2em] uppercase mt-2">Available Mon-Sat 10:00 — 19:00</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-8 group focus-within:ring-0">
                            <div class="w-14 h-14 bg-slate-50 text-primary rounded-[1.5rem] flex items-center justify-center flex-shrink-0 border border-black/[0.03] group-hover:bg-primary group-hover:text-white transition-all duration-500 transform group-hover:-rotate-12">
                                <i data-lucide="mail" class="w-6 h-6 stroke-[1.2]"></i>
                            </div>
                            <div>
                                <h3 class="text-[10px] font-black tracking-[0.4em] text-slate-400 uppercase mb-4">Digital Correspondence</h3>
                                <p class="text-xl font-serif text-primary tracking-tight leading-relaxed italic">hello@optilux.com</p>
                                <p class="text-[9px] font-bold text-slate-300 tracking-[0.2em] uppercase mt-2">Response within one solar day</p>
                            </div>
                        </div>
                    </div>

                    <!-- Architectural Map Placeholder -->
                    <div class="relative group h-96 rounded-[4rem] overflow-hidden shadow-2xl border border-black/5 bg-slate-50">
                        <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&q=80&w=1200" 
                             class="absolute inset-0 w-full h-full object-cover grayscale opacity-30 transition-all duration-1000 group-hover:scale-110 group-hover:grayscale-0">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <button class="px-8 py-4 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl text-[9px] font-black tracking-[0.5em] text-white uppercase hover:bg-white hover:text-primary transition-all duration-500 shadow-2xl">
                                Reveal Studio Path
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>



