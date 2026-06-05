<?php
$title = "System Settings";
$active_page = "settings";
require_once __DIR__ . '/includes/header.php';

if (isset($_POST['update_settings'])) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "CSRF validation failed.";
    } else {
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
        $success = "Settings updated successfully!";
    }
}

$stmt = $pdo->query("SELECT * FROM settings ORDER BY category");
$settings_list = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Configuration</h2>
    <?php if (isset($success)): ?>
        <div class="alert alert-success py-1 px-3 mb-0 small"><?php echo $success; ?></div>
    <?php endif; ?>
</div>

<div class="lp-card">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <div class="row g-4">
            <?php foreach ($settings_list as $s): ?>
            <div class="col-md-6">
                <label class="form-label text-muted small text-uppercase"><?php echo str_replace('_', ' ', $s['setting_key']); ?></label>
                <input type="text" name="settings[<?php echo h($s['setting_key']); ?>]" class="lp-input w-100" value="<?php echo h($s['setting_value']); ?>">
            </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-5 pt-3 border-top border-secondary">
            <button type="submit" name="update_settings" class="btn-lp-primary">Save System Settings</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
