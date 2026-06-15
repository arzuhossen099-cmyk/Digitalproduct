<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    $message = "Settings updated successfully!";
}

$site_name = get_setting($pdo, 'site_name', 'PLAYPULSE');
$contact_email = get_setting($pdo, 'contact_email', 'admin@playpulse.com');
$facebook_url = get_setting($pdo, 'facebook_url', '#');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">General Settings</h2>
</div>

<?php if ($message): ?><div class="alert alert-success"><?php echo h($message); ?></div><?php endif; ?>

<div class="card shadow admin-card">
    <div class="card-body p-4">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="mb-3">
                <label class="form-label">Site Name</label>
                <input type="text" name="settings[site_name]" class="form-control bg-dark text-white border-secondary" value="<?php echo h($site_name); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Email</label>
                <input type="email" name="settings[contact_email]" class="form-control bg-dark text-white border-secondary" value="<?php echo h($contact_email); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Facebook URL</label>
                <input type="text" name="settings[facebook_url]" class="form-control bg-dark text-white border-secondary" value="<?php echo h($facebook_url); ?>">
            </div>
            <button type="submit" class="btn btn-yellow">Save Settings</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
