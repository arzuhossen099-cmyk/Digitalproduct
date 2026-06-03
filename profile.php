<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Profile</h5>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">User Level</label>
            <input type="text" class="form-control" value="Level <?php echo $user['user_level'] ?? 1; ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Joined</label>
            <input type="text" class="form-control" value="<?php echo date('d M Y', strtotime($user['created_at'])); ?>" readonly>
        </div>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
