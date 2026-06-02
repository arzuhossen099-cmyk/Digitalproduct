<?php
require_once 'header.php';

if (isset($_POST['update_gateway'])) {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([$value, $key]);
    }
    echo "<div class='alert alert-success'>Gateway settings updated!</div>";
    // Refresh settings
    $stmt = $pdo->query("SELECT * FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

?>

<div class="card shadow-sm col-md-8">
    <div class="card-body">
        <h5 class="card-title mb-4">Payment Gateway (SSLCommerz) Configuration</h5>
        <div class="alert alert-info small">
            <i class="fas fa-info-circle me-2"></i> This integration allows users to deposit money instantly via bKash, Nagad, Cards, etc.
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Auto Deposit Status</label>
                <select name="settings[auto_deposit_status]" class="form-select">
                    <option value="enabled" <?php echo ($settings['auto_deposit_status'] ?? '') == 'enabled' ? 'selected' : ''; ?>>Enabled</option>
                    <option value="disabled" <?php echo ($settings['auto_deposit_status'] ?? '') == 'disabled' ? 'selected' : ''; ?>>Disabled</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Gateway Mode</label>
                <select name="settings[gateway_mode]" class="form-select">
                    <option value="sandbox" <?php echo ($settings['gateway_mode'] ?? '') == 'sandbox' ? 'selected' : ''; ?>>Sandbox (Test)</option>
                    <option value="live" <?php echo ($settings['gateway_mode'] ?? '') == 'live' ? 'selected' : ''; ?>>Live (Real Payments)</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Store ID</label>
                <input type="text" name="settings[gateway_store_id]" class="form-control" value="<?php echo htmlspecialchars($settings['gateway_store_id'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Store Password</label>
                <input type="text" name="settings[gateway_store_password]" class="form-control" value="<?php echo htmlspecialchars($settings['gateway_store_password'] ?? ''); ?>">
            </div>

            <div class="bg-light p-3 rounded mb-3">
                <h6>IPN / Callback URLs</h6>
                <small class="text-muted d-block mb-1">Set these in your Payment Gateway Dashboard:</small>
                <code class="d-block bg-white p-2 border rounded">
                    <?php
                        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname(dirname($_SERVER['PHP_SELF']));
                        echo $base_url . "/api/callback.php";
                    ?>
                </code>
            </div>

            <button type="submit" name="update_gateway" class="btn btn-primary">Save Configuration</button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>
