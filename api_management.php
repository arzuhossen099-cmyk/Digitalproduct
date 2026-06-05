<?php
$title = "API Management";
require_once __DIR__ . '/includes/header_user.php';

$user = get_logged_in_user();

if (isset($_POST['generate_key'])) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }
    $new_key = bin2hex(random_bytes(32));
    $name = trim($_POST['key_name'] ?? 'Default Key');
    $stmt = $pdo->prepare("INSERT INTO api_keys (user_id, api_key, name) VALUES (?, ?, ?)");
    $stmt->execute([$user['id'], $new_key, $name]);
    $success = "API Key generated successfully!";
}

$stmt = $pdo->prepare("SELECT * FROM api_keys WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$keys = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">REST API Keys</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#keyModal"><i class="fas fa-plus me-2"></i> Generate New Key</button>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-dark border-secondary">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>API Key</th>
                    <th>Status</th>
                    <th>Last Used</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($keys as $key): ?>
                <tr>
                    <td><?php echo h($key['name']); ?></td>
                    <td><code><?php echo substr($key['api_key'], 0, 8); ?>...<?php echo substr($key['api_key'], -8); ?></code></td>
                    <td><span class="badge bg-success"><?php echo ucfirst($key['status']); ?></span></td>
                    <td><?php echo $key['last_used_at'] ? date('M d, Y H:i', strtotime($key['last_used_at'])) : 'Never'; ?></td>
                    <td><?php echo date('M d, Y', strtotime($key['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($keys)): ?>
                    <tr><td colspan="5" class="text-center text-muted">You haven't generated any API keys yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="keyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary">
            <form method="POST">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Generate API Key</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Key Name (e.g. CRM Integration)</label>
                        <input type="text" name="key_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" name="generate_key" class="btn btn-primary">Generate Key</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
