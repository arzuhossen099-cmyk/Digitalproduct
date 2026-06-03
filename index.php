<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php'; // This will be created in step 11, but let's assume it checks for plan activation

// Check if user has active plan
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_plans WHERE user_id = ? AND status = 'active' AND (expires_at IS NULL OR expires_at > NOW())");
$stmt->execute([$_SESSION['user_id']]);
$has_active_plan = $stmt->fetchColumn() > 0;
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white p-3 shadow">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h5>
                    <small>Balance: ৳<?php echo number_format($user['balance'], 2); ?></small>
                </div>
                <div>
                    <?php if ($has_active_plan): ?>
                        <span class="badge bg-success">Plan Active</span>
                    <?php else: ?>
                        <span class="badge bg-danger">No Active Plan</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!$has_active_plan): ?>
<div class="alert alert-warning mb-4">
    <strong>Notice:</strong> You need to activate a plan to access all features. <a href="plans.php" class="alert-link">View Plans</a>
</div>
<?php endif; ?>

<?php
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['error'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['error']);
}
?>

<div class="row g-3">
    <!-- Level 1 Accessible -->
    <div class="col-6">
        <a href="recharge.php" class="text-decoration-none">
            <div class="card text-center p-3 shadow-sm h-100">
                <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                <h6 class="mb-0">Mobile Recharge</h6>
            </div>
        </a>
    </div>
    <div class="col-6">
        <a href="packages.php" class="text-decoration-none">
            <div class="card text-center p-3 shadow-sm h-100">
                <i class="fas fa-globe fa-2x text-success mb-2"></i>
                <h6 class="mb-0">Internet Packs</h6>
            </div>
        </a>
    </div>
    <div class="col-6">
        <a href="rewards.php" class="text-decoration-none">
            <div class="card text-center p-3 shadow-sm h-100">
                <i class="fas fa-gift fa-2x text-warning mb-2"></i>
                <h6 class="mb-0">Daily Bonus</h6>
            </div>
        </a>
    </div>

    <!-- Level 2 Restricted -->
    <div class="col-6">
        <?php if (($user['user_level'] ?? 1) >= 2): ?>
            <a href="earn.php" class="text-decoration-none">
                <div class="card text-center p-3 shadow-sm h-100 border-primary">
                    <i class="fas fa-dollar-sign fa-2x text-primary mb-2"></i>
                    <h6 class="mb-0">Earn Money</h6>
                </div>
            </a>
        <?php else: ?>
            <div class="card text-center p-3 shadow-sm h-100 opacity-75" onclick="alert('Upgrade to Level 2 to access this!')" style="cursor:not-allowed;">
                <i class="fas fa-lock fa-2x text-muted mb-2"></i>
                <h6 class="mb-0 text-muted">Earn Money</h6>
            </div>
        <?php endif; ?>
    </div>
    <!-- Dynamic Games -->
    <?php
    $stmt = $pdo->query("SELECT * FROM games WHERE status = 'active' ORDER BY created_at DESC");
    $dynamic_games = $stmt->fetchAll();
    foreach ($dynamic_games as $game):
    ?>
    <div class="col-6">
        <?php if (($user['user_level'] ?? 1) >= 2): ?>
            <a href="<?php echo htmlspecialchars($game['link'] ?? ''); ?>" class="text-decoration-none">
                <div class="card text-center p-3 shadow-sm h-100">
                    <i class="<?php echo htmlspecialchars($game['icon_class'] ?? 'fas fa-gamepad'); ?> fa-2x text-danger mb-2"></i>
                    <h6 class="mb-0"><?php echo htmlspecialchars($game['name'] ?? 'Game'); ?></h6>
                </div>
            </a>
        <?php else: ?>
            <div class="card text-center p-3 shadow-sm h-100 opacity-75" onclick="alert('Upgrade to Level 2 to access this!')" style="cursor:not-allowed;">
                <i class="fas fa-lock fa-2x text-muted mb-2"></i>
                <h6 class="mb-0 text-muted"><?php echo htmlspecialchars($game['name'] ?? 'Game'); ?></h6>
            </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <div class="col-6">
        <?php if (($user['user_level'] ?? 1) >= 2): ?>
            <a href="deposit.php" class="text-decoration-none">
                <div class="card text-center p-3 shadow-sm h-100">
                    <i class="fas fa-plus-circle fa-2x text-info mb-2"></i>
                    <h6 class="mb-0">Deposit</h6>
                </div>
            </a>
        <?php else: ?>
            <div class="card text-center p-3 shadow-sm h-100 opacity-75" onclick="alert('Upgrade to Level 2 to access this!')" style="cursor:not-allowed;">
                <i class="fas fa-lock fa-2x text-muted mb-2"></i>
                <h6 class="mb-0 text-muted">Deposit</h6>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-6">
        <?php if (($user['user_level'] ?? 1) >= 2): ?>
            <a href="aviator.php" class="text-decoration-none">
                <div class="card text-center p-3 shadow-sm h-100 border-danger">
                    <i class="fas fa-plane-departure fa-2x text-danger mb-2"></i>
                    <h6 class="mb-0 text-danger">Aviator Game</h6>
                </div>
            </a>
        <?php else: ?>
            <div class="card text-center p-3 shadow-sm h-100 opacity-75" onclick="alert('Upgrade to Level 2 to access this!')" style="cursor:not-allowed;">
                <i class="fas fa-lock fa-2x text-muted mb-2"></i>
                <h6 class="mb-0 text-muted">Aviator Game</h6>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-6">
        <?php if (($user['user_level'] ?? 1) >= 2): ?>
            <a href="withdraw.php" class="text-decoration-none">
                <div class="card text-center p-3 shadow-sm h-100">
                    <i class="fas fa-minus-circle fa-2x text-secondary mb-2"></i>
                    <h6 class="mb-0">Withdraw</h6>
                </div>
            </a>
        <?php else: ?>
            <div class="card text-center p-3 shadow-sm h-100 opacity-75" onclick="alert('Upgrade to Level 2 to access this!')" style="cursor:not-allowed;">
                <i class="fas fa-lock fa-2x text-muted mb-2"></i>
                <h6 class="mb-0 text-muted">Withdraw</h6>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-4">
    <h5>Latest Notifications</h5>
    <div class="list-group">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
        $stmt->execute([$_SESSION['user_id']]);
        $notifications = $stmt->fetchAll();
        if (empty($notifications)):
        ?>
            <div class="list-group-item text-muted text-center">No new notifications</div>
        <?php else:
            foreach ($notifications as $notif):
        ?>
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <small class="text-muted"><?php echo date('d M, h:i A', strtotime($notif['created_at'])); ?></small>
                </div>
                <p class="mb-1"><?php echo htmlspecialchars($notif['message']); ?></p>
            </div>
        <?php
            endforeach;
        endif;
        ?>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
