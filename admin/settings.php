<?php
require_once 'header.php';

if (isset($_POST['update_settings'])) {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([$value, $key]);
    }
    echo "<div class='alert alert-success'>Settings updated!</div>";
    // Refresh settings
    $stmt = $pdo->query("SELECT * FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

?>

<div class="card shadow-sm col-md-6">
    <div class="card-body">
        <h5 class="card-title mb-4">Website Settings</h5>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Site Name</label>
                <input type="text" name="settings[site_name]" class="form-control" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">bKash Number</label>
                <input type="text" name="settings[bkash_number]" class="form-control" value="<?php echo htmlspecialchars($settings['bkash_number'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Nagad Number</label>
                <input type="text" name="settings[nagad_number]" class="form-control" value="<?php echo htmlspecialchars($settings['nagad_number'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Rocket Number</label>
                <input type="text" name="settings[rocket_number]" class="form-control" value="<?php echo htmlspecialchars($settings['rocket_number'] ?? ''); ?>">
            </div>
            <button type="submit" name="update_settings" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>
