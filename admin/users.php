<?php
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_role'])) {
        if ($_POST['id'] != $_SESSION['user_id']) { 
            $id = (int)$_POST['id'];
            $role = $_POST['role'];
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            if ($stmt->execute([$role, $id])) {
                setFlash('success', "User role updated successfully.");
            }
        }
    }
    
    if (isset($_POST['delete_id'])) {
        if ($_POST['delete_id'] != $_SESSION['user_id']) {
            $id = (int)$_POST['delete_id'];
            
            try {
                $conn->beginTransaction();
                
                // 1. Delete cart and wishlist items
                $conn->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$id]);
                $conn->prepare("DELETE FROM wishlist WHERE user_id = ?")->execute([$id]);
                
                // 2. Delete reviews
                $conn->prepare("DELETE FROM reviews WHERE user_id = ?")->execute([$id]);
                
                // 3. Handle orders (delete associated items first)
                $order_stmt = $conn->prepare("SELECT id FROM orders WHERE user_id = ?");
                $order_stmt->execute([$id]);
                $order_ids = $order_stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (!empty($order_ids)) {
                    $placeholders = implode(',', array_fill(0, count($order_ids), '?'));
                    $conn->prepare("DELETE FROM order_items WHERE order_id IN ($placeholders)")->execute($order_ids);
                    $conn->prepare("DELETE FROM orders WHERE user_id = ?")->execute([$id]);
                }
                
                // 4. Finally delete the user
                $conn->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
                
                $conn->commit();
                setFlash('success', "User and all associated data have been removed.");
            } catch (Exception $e) {
                $conn->rollBack();
                setFlash('error', "Could not remove user: " . $e->getMessage());
            }
        }
    }
    
    header("Location: users.php");
    exit;
}

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div class="glass-card overflow-hidden">
    <div class="p-10 border-b border-border bg-white/5 flex justify-between items-center bg-gradient-to-r from-white/5 to-transparent">
        <div>
            <h1 class="text-xl font-bold font-serif-lux tracking-widest uppercase">Manage Users</h1>
            <p class="text-[9px] text-muted uppercase tracking-[0.2em] mt-1 font-semibold"><?= count($users) ?> Total Accounts</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-muted text-[10px] uppercase tracking-[0.2em] border-b border-border">
                    <th class="px-8 py-6 font-bold">ID</th>
                    <th class="px-8 py-6 font-bold">Name</th>
                    <th class="px-8 py-6 font-bold">Email</th>
                    <th class="px-8 py-6 font-bold">Joined</th>
                    <th class="px-8 py-6 font-bold text-right">Role / Action</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium tracking-wide text-main">
                <?php foreach($users as $user): ?>
                <tr class="border-b border-border last:border-0 hover:bg-white/5 transition-all">
                    <td class="px-8 py-6 text-muted font-bold tracking-widest">#USER-<?= str_pad($user['id'], 3, '0', STR_PAD_LEFT) ?></td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-brand/10 border border-brand/20 flex items-center justify-center text-brand font-bold text-lg">
                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <span class="font-bold text-sm tracking-tight block"><?= htmlspecialchars($user['name']) ?></span>
                                <span class="text-[9px] text-muted uppercase tracking-widest font-bold"><?= $user['role'] === 'admin' ? 'Administrator' : 'General User' ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6 font-medium text-muted"><?= htmlspecialchars($user['email']) ?></td>
                    <td class="px-8 py-6 text-muted"><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <form method="POST" class="inline">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <select name="role" class="pro-input !w-auto !py-2 !text-[10px] font-bold uppercase tracking-widest cursor-pointer" onchange="this.form.submit()" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <input type="hidden" name="update_role" value="1">
                            </form>

                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <!-- Permission-Based Delete Button -->
                            <form method="POST" id="delete-form-<?= $user['id'] ?>" class="relative inline-block">
                                <input type="hidden" name="delete_id" value="<?= $user['id'] ?>">
                                <button type="button" 
                                        id="delete-btn-<?= $user['id'] ?>"
                                        onmousedown="startHold(<?= $user['id'] ?>)" 
                                        onmouseup="stopHold(<?= $user['id'] ?>)" 
                                        onmouseleave="stopHold(<?= $user['id'] ?>)"
                                        ontouchstart="startHold(<?= $user['id'] ?>)"
                                        ontouchend="stopHold(<?= $user['id'] ?>)"
                                        class="w-10 h-10 rounded-xl bg-rose-500/5 border border-rose-500/10 text-rose-500/80 hover:bg-rose-500/10 transition-all flex items-center justify-center relative overflow-hidden group">
                                    <i data-lucide="trash-2" id="icon-<?= $user['id'] ?>" class="w-4 h-4 relative z-10 transition-all duration-300"></i>
                                    <span id="text-<?= $user['id'] ?>" class="absolute inset-0 z-20 flex items-center justify-center text-[8px] font-bold uppercase tracking-tighter opacity-0 scale-50 transition-all duration-300 pointer-events-none">Confirm?</span>
                                    <!-- Progress Layer -->
                                    <div id="progress-<?= $user['id'] ?>" class="absolute inset-0 bg-rose-500 origin-bottom scale-y-0 opacity-20"></div>
                                </button>
                                <!-- Tooltip / Instruction -->
                                <div id="hint-<?= $user['id'] ?>" class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-rose-500 text-[8px] text-white rounded opacity-0 pointer-events-none transition-opacity uppercase font-bold tracking-tighter whitespace-nowrap">Hold to seeking permission</div>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
let holdIntervals = {};
let isConfirmState = {};

function startHold(id) {
    if (isConfirmState[id]) {
        document.getElementById(`delete-form-${id}`).submit();
        return;
    }

    const progress = document.getElementById(`progress-${id}`);
    const hint = document.getElementById(`hint-${id}`);
    const icon = document.getElementById(`icon-${id}`);
    const text = document.getElementById(`text-${id}`);
    const btn = document.getElementById(`delete-btn-${id}`);
    
    hint.classList.add('opacity-100');
    let startTime = Date.now();
    let duration = 1500; // 1.5 seconds

    holdIntervals[id] = setInterval(() => {
        let elapsed = Date.now() - startTime;
        let p = Math.min(elapsed / duration, 1);
        progress.style.transform = `scaleY(${p})`;
        
        if (p >= 1) {
            clearInterval(holdIntervals[id]);
            // Transition to Permission/Confirm state
            isConfirmState[id] = true;
            icon.classList.add('opacity-0', 'scale-50');
            text.classList.remove('opacity-0', 'scale-50');
            text.classList.add('opacity-100', 'scale-100');
            btn.classList.add('!bg-rose-500', '!text-white', '!border-rose-500');
            progress.style.transform = 'scaleY(1)';
            progress.classList.add('!opacity-100');
            hint.textContent = "Tap to confirm deletion";
        }
    }, 10);
}

function stopHold(id) {
    if (isConfirmState[id]) return; // Stay in confirm state once reached

    const progress = document.getElementById(`progress-${id}`);
    const hint = document.getElementById(`hint-${id}`);
    
    clearInterval(holdIntervals[id]);
    hint.classList.remove('opacity-100');
    progress.style.transform = 'scaleY(0)';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
