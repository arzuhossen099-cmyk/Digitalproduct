<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$error = '';
$success = '';

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['plan_id'])) {
    $plan_id = intval($_POST['plan_id']);

    $stmt = $pdo->prepare("SELECT * FROM plans WHERE id = ?");
    $stmt->execute([$plan_id]);
    $plan = $stmt->fetch();

    if (!$plan) {
        $error = "Invalid plan selected.";
    } elseif ($user['balance'] < $plan['price']) {
        $error = "Insufficient balance to activate this plan. Please deposit money first.";
    } else {
        try {
            $pdo->beginTransaction();

            // Deduct balance
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$plan['price'], $_SESSION['user_id']]);

            // Record transaction
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'plan_purchase', ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $plan['price'], "Activated plan: " . $plan['name']]);

            // Deactivate existing plans
            $stmt = $pdo->prepare("UPDATE user_plans SET status = 'expired' WHERE user_id = ? AND status = 'active'");
            $stmt->execute([$_SESSION['user_id']]);

            // Activate new plan
            $expires_at = date('Y-m-d H:i:s', strtotime("+{$plan['duration_days']} days"));
            $stmt = $pdo->prepare("INSERT INTO user_plans (user_id, plan_id, status, expires_at) VALUES (?, ?, 'active', ?)");
            $stmt->execute([$_SESSION['user_id'], $plan_id, $expires_at]);

            $pdo->commit();
            $success = "Plan activated successfully! You can now access all features.";
            // Update local user variable for UI
            $user['balance'] -= $plan['price'];
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to activate plan.";
        }
    }
}

$stmt = $pdo->query("SELECT * FROM plans ORDER BY price ASC");
$available_plans = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT up.*, p.name FROM user_plans up JOIN plans p ON up.plan_id = p.id WHERE up.user_id = ? AND up.status = 'active' ORDER BY up.activated_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$active_plan = $stmt->fetch();
?>

<?php if ($active_plan): ?>
<div class="card bg-success text-white mb-4 shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Active Plan: <?php echo htmlspecialchars($active_plan['name']); ?></h5>
        <p class="mb-0">Expires on: <?php echo date('d M Y, h:i A', strtotime($active_plan['expires_at'])); ?></p>
    </div>
</div>
<?php endif; ?>

<h4 class="mb-3">Available Plans</h4>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="row g-3">
    <?php foreach ($available_plans as $p): ?>
    <div class="col-12">
        <div class="card shadow-sm border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title mb-0 text-primary"><?php echo htmlspecialchars($p['name']); ?></h5>
                    <span class="h4 mb-0">৳<?php echo number_format($p['price'], 2); ?></span>
                </div>
                <p class="card-text text-muted"><?php echo htmlspecialchars($p['description']); ?></p>
                <p class="card-text"><i class="fas fa-clock me-1"></i> Validity: <?php echo $p['duration_days']; ?> Days</p>
                <form method="POST">
                    <input type="hidden" name="plan_id" value="<?php echo $p['id']; ?>">
                    <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Are you sure you want to activate this plan?')">Activate Now</button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
