<?php
require_once 'header.php';

// Handle Settings Update
if (isset($_POST['update_settings'])) {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $pdo->prepare("UPDATE aviator_settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([$value, $key]);
    }
    echo "<div class='alert alert-success'>Settings updated!</div>";
}

// Fetch Current Settings
$stmt = $pdo->query("SELECT * FROM aviator_settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Fetch Bet History
$stmt = $pdo->query("SELECT h.*, u.username FROM aviator_history h JOIN users u ON h.user_id = u.id ORDER BY h.created_at DESC LIMIT 50");
$history = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-md-5">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4"><i class="fas fa-cog me-2"></i> Game Controls</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Game Status</label>
                        <select name="settings[game_status]" class="form-select">
                            <option value="active" <?php echo ($settings['game_status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($settings['game_status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Control Mode</label>
                        <select name="settings[control_mode]" class="form-select">
                            <option value="auto" <?php echo ($settings['control_mode'] ?? '') == 'auto' ? 'selected' : ''; ?>>Auto (Random)</option>
                            <option value="manual" <?php echo ($settings['control_mode'] ?? '') == 'manual' ? 'selected' : ''; ?>>Manual (Controlled)</option>
                        </select>
                        <small class="text-muted">In Manual mode, the next game will crash at the exact point specified below.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-danger fw-bold">Next Crash Point (Manual Only)</label>
                        <input type="number" step="0.01" name="settings[next_crash_point]" class="form-control" value="<?php echo $settings['next_crash_point'] ?? '1.00'; ?>">
                        <small class="text-muted">Set this value before the next game starts.</small>
                    </div>
                    <hr>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label">Min Bet</label>
                            <input type="number" name="settings[min_bet]" class="form-control" value="<?php echo $settings['min_bet'] ?? ''; ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Max Bet</label>
                            <input type="number" name="settings[max_bet]" class="form-control" value="<?php echo $settings['max_bet'] ?? ''; ?>">
                        </div>
                    </div>
                    <button type="submit" name="update_settings" class="btn btn-primary w-100">Update Controls</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4"><i class="fas fa-history me-2"></i> Real-time Bet History</h5>
                <div class="table-responsive" style="max-height: 600px;">
                    <table class="table table-striped table-hover small">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Bet</th>
                                <th>Crash</th>
                                <th>Payout</th>
                                <th>Win</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($history)): ?>
                                <tr><td colspan="7" class="text-center py-4">No bet history found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($history as $h): ?>
                                <tr>
                                    <td><small><?php echo date('H:i:s', strtotime($h['created_at'])); ?></small></td>
                                    <td><strong><?php echo htmlspecialchars($h['username']); ?></strong></td>
                                    <td>৳<?php echo number_format($h['bet_amount'], 2); ?></td>
                                    <td><?php echo number_format($h['crash_point'], 2); ?>x</td>
                                    <td><?php echo $h['multiplier'] > 0 ? number_format($h['multiplier'], 2) . 'x' : '-'; ?></td>
                                    <td class="<?php echo $h['win_amount'] > 0 ? 'text-success' : 'text-danger'; ?>">
                                        ৳<?php echo number_format($h['win_amount'], 2); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $h['result'] == 'win' ? 'success' : 'danger'; ?>">
                                            <?php echo strtoupper($h['result']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
