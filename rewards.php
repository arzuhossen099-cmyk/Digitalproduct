<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$error = '';
$success = '';

// Get User Total Deposit
$stmt = $pdo->prepare("SELECT SUM(amount) FROM deposits WHERE user_id = ? AND status = 'approved'");
$stmt->execute([$_SESSION['user_id']]);
$total_deposit = $stmt->fetchColumn() ?: 0;

// Get User Active Plan Price
$stmt = $pdo->prepare("SELECT p.price FROM user_plans up JOIN plans p ON up.plan_id = p.id WHERE up.user_id = ? AND up.status = 'active' AND (up.expires_at IS NULL OR up.expires_at > NOW()) LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$active_plan_price = $stmt->fetchColumn() ?: 0;

// Handle Reward Claim
if (isset($_POST['claim_reward'])) {
    $reward_id = intval($_POST['reward_id']);

    $stmt = $pdo->prepare("SELECT * FROM rewards WHERE id = ? AND status = 'active'");
    $stmt->execute([$reward_id]);
    $reward = $stmt->fetch();

    if (!$reward) {
        $error = "Invalid or inactive reward.";
    } else {
        // Check Eligibility
        $eligible = true;
        if ($total_deposit < $reward['min_deposit']) $eligible = false;
        if ($active_plan_price < $reward['min_plan_price']) $eligible = false;

        if (!$eligible) {
            $error = "You do not meet the eligibility requirements for this reward.";
        } else {
            // Check frequency (simple check for daily)
            if ($reward['frequency'] == 'daily') {
                $today = date('Y-m-d');
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM reward_logs WHERE user_id = ? AND reward_id = ? AND DATE(created_at) = ?");
                $stmt->execute([$_SESSION['user_id'], $reward_id, $today]);
                if ($stmt->fetchColumn() > 0) {
                    $error = "You have already claimed this reward today.";
                }
            } elseif ($reward['frequency'] == 'one-time') {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM reward_logs WHERE user_id = ? AND reward_id = ?");
                $stmt->execute([$_SESSION['user_id'], $reward_id]);
                if ($stmt->fetchColumn() > 0) {
                    $error = "You have already claimed this one-time reward.";
                }
            }

            if (!$error) {
                try {
                    $pdo->beginTransaction();

                    $final_amount = $reward['amount'] + $reward['bonus_amount'];
                    if ($reward['percentage'] > 0) {
                        // Percentage could be based on deposit or plan price, let's assume plan price for this logic
                        $final_amount += ($active_plan_price * ($reward['percentage'] / 100));
                    }

                    // Add balance
                    $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                    $stmt->execute([$final_amount, $_SESSION['user_id']]);

                    // Record in transactions
                    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'reward', ?, ?)");
                    $stmt->execute([$_SESSION['user_id'], $final_amount, "Claimed reward: " . $reward['name']]);

                    // Record in logs
                    $stmt = $pdo->prepare("INSERT INTO reward_logs (user_id, reward_id, amount, description) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$_SESSION['user_id'], $reward_id, $final_amount, $reward['name']]);

                    // Notify user
                    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                    $stmt->execute([$_SESSION['user_id'], "Congratulations! You claimed ৳" . number_format($final_amount, 2) . " from " . $reward['name']]);

                    $pdo->commit();
                    $success = "Reward claimed successfully!";
                    $user['balance'] += $final_amount;
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Claim failed. Please try again.";
                }
            }
        }
    }
}

// Fetch Active Rewards
$stmt = $pdo->query("SELECT * FROM rewards WHERE status = 'active' ORDER BY created_at DESC");
$available_rewards = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-warning text-dark p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Your Eligibility Stats:</h6>
                    <small>Total Deposit: ৳<?php echo number_format($total_deposit, 2); ?> | Active Plan: ৳<?php echo number_format($active_plan_price, 2); ?></small>
                </div>
                <i class="fas fa-chart-line fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<h4 class="mb-3">Available Rewards</h4>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="row g-3">
    <?php if(empty($available_rewards)): ?>
        <div class="col-12 text-center text-muted py-5">No active rewards at the moment.</div>
    <?php else: ?>
        <?php foreach ($available_rewards as $r):
            $is_eligible = ($total_deposit >= $r['min_deposit'] && $active_plan_price >= $r['min_plan_price']);
        ?>
        <div class="col-12">
            <div class="card shadow-sm <?php echo $is_eligible ? 'border-success' : 'border-light opacity-75'; ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($r['name']); ?></h5>
                            <span class="badge bg-secondary mb-2"><?php echo ucfirst($r['frequency']); ?></span>
                        </div>
                        <div class="text-end">
                            <h5 class="text-success mb-0">৳<?php echo number_format($r['amount'] + $r['bonus_amount'], 2); ?></h5>
                            <?php if($r['percentage'] > 0) echo "<small class='text-muted'>+ " . $r['percentage'] . "%</small>"; ?>
                        </div>
                    </div>
                    <p class="card-text text-muted small"><?php echo htmlspecialchars($r['description']); ?></p>

                    <div class="bg-light p-2 rounded mb-3 small">
                        <strong>Requirements:</strong><br>
                        - Min Deposit: ৳<?php echo $r['min_deposit']; ?>
                        (<?php echo $total_deposit >= $r['min_deposit'] ? '<span class="text-success">Met</span>' : '<span class="text-danger">Need ৳'.($r['min_deposit']-$total_deposit).' more</span>'; ?>)<br>
                        - Min Plan: ৳<?php echo $r['min_plan_price']; ?>
                        (<?php echo $active_plan_price >= $r['min_plan_price'] ? '<span class="text-success">Met</span>' : '<span class="text-danger">Not Met</span>'; ?>)
                    </div>

                    <form method="POST">
                        <input type="hidden" name="reward_id" value="<?php echo $r['id']; ?>">
                        <button type="submit" name="claim_reward" class="btn <?php echo $is_eligible ? 'btn-success' : 'btn-secondary disabled'; ?> w-100">
                            <?php echo $is_eligible ? 'Claim Now' : 'Not Eligible'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="mt-4">
    <h5>Claimed History</h5>
    <div class="list-group list-group-flush shadow-sm bg-white rounded">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM reward_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
        $stmt->execute([$_SESSION['user_id']]);
        $history = $stmt->fetchAll();
        if(empty($history)):
        ?>
            <div class="list-group-item text-center text-muted">No reward history yet.</div>
        <?php else: ?>
            <?php foreach ($history as $h): ?>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <span class="fw-bold"><?php echo htmlspecialchars($h['description']); ?></span>
                        <span class="text-success">+ ৳<?php echo number_format($h['amount'], 2); ?></span>
                    </div>
                    <small class="text-muted"><?php echo date('d M Y, h:i A', strtotime($h['created_at'])); ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
