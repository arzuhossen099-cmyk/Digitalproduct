<?php
require_once 'header.php';

if (isset($_POST['give_reward'])) {
    $user_id = intval($_POST['user_id']);
    $amount = floatval($_POST['amount']);
    $description = $_POST['description'];

    try {
        $pdo->beginTransaction();

        // Add balance to user
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $user_id]);

        // Record in transactions
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'reward', ?, ?)");
        $stmt->execute([$user_id, $amount, $description]);

        // Record in reward_logs
        $stmt = $pdo->prepare("INSERT INTO reward_logs (user_id, amount, description) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $amount, $description]);

        // Notify user
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->execute([$user_id, "You have received a special reward of ৳" . number_format($amount, 2) . ": " . $description]);

        $pdo->commit();
        echo "<div class='alert alert-success'>Reward given successfully to User ID: $user_id</div>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<div class='alert alert-danger'>Failed to give reward: " . $e->getMessage() . "</div>";
    }
}

$users = $pdo->query("SELECT id, username, phone FROM users WHERE is_admin = 0 ORDER BY username ASC")->fetchAll();
?>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Manual Reward Assignment</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Select User</label>
                        <select name="user_id" class="form-select select2" required>
                            <option value="">Select a user...</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['username']); ?> (<?php echo $u['phone']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (৳)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description / Reason</label>
                        <input type="text" name="description" class="form-control" required placeholder="e.g. VIP Bonus, Special Gift">
                    </div>
                    <button type="submit" name="give_reward" class="btn btn-primary w-100">Send Reward</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
