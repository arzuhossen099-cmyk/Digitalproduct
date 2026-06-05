<?php
$title = "My Profile";
$active_page = "profile";
require_once __DIR__ . '/includes/header_user.php';

$user = get_logged_in_user();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "CSRF validation failed.";
    } else {
        $full_name = trim($_POST['full_name']);
        $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
        $stmt->execute([$full_name, $user['id']]);
        $message = "Profile updated successfully!";
        $user['full_name'] = $full_name; // Update local copy
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "CSRF validation failed.";
    } else {
        $old_pass = $_POST['old_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if (password_verify($old_pass, $user['password'])) {
            if ($new_pass === $confirm_pass) {
                if (strlen($new_pass) >= 8) {
                    $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashed, $user['id']]);
                    $message = "Password changed successfully!";
                } else {
                    $error = "New password must be at least 8 characters.";
                }
            } else {
                $error = "Passwords do not match.";
            }
        } else {
            $error = "Incorrect old password.";
        }
    }
}
?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="lp-card text-center">
            <div class="bg-accent rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px; background: var(--accent-primary);">
                <span class="h1 text-dark fw-bold mb-0"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></span>
            </div>
            <h4 class="fw-bold mb-1"><?php echo h($user['full_name'] ?: $user['username']); ?></h4>
            <p class="text-muted small mb-3">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
            <div class="lp-badge lp-badge-info"><?php echo h($subscription['plan_name'] ?? 'Free Plan'); ?></div>

            <hr class="my-4 border-secondary">

            <div class="text-start">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Referral Code</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control bg-dark border-secondary text-info" value="<?php echo h($user['referral_code']); ?>" readonly>
                        <button class="btn btn-lp-outline py-0 px-2" onclick="navigator.clipboard.writeText('<?php echo h($user['referral_code']); ?>'); alert('Copied!')"><i class="fas fa-copy"></i></button>
                    </div>
                </div>
                <div class="d-grid">
                    <a href="affiliate.php" class="btn btn-lp-outline btn-sm">View Referrals</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="lp-card mb-4">
            <h5 class="fw-bold mb-4"><i class="fas fa-user-edit me-2 text-accent"></i> Personal Information</h5>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Username</label>
                        <input type="text" class="lp-input w-100 opacity-50" value="<?php echo h($user['username']); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Email Address</label>
                        <input type="text" class="lp-input w-100 opacity-50" value="<?php echo h($user['email']); ?>" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small">Full Name</label>
                        <input type="text" name="full_name" class="lp-input w-100" value="<?php echo h($user['full_name']); ?>" required>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" name="update_profile" class="btn-lp-primary">Save Changes</button>
                </div>
            </form>
        </div>

        <div class="lp-card">
            <h5 class="fw-bold mb-4"><i class="fas fa-lock me-2 text-accent"></i> Security & Password</h5>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="mb-3">
                    <label class="form-label text-muted small">Current Password</label>
                    <input type="password" name="old_password" class="lp-input w-100" required>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">New Password</label>
                        <input type="password" name="new_password" class="lp-input w-100" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="lp-input w-100" required>
                    </div>
                </div>
                <button type="submit" name="change_password" class="btn-lp-outline">Update Password</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
