<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$success = '';

if (isset($_GET['claim_daily'])) {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = ? AND type = 'reward' AND description = 'Daily Check-in' AND DATE(created_at) = ?");
    $stmt->execute([$_SESSION['user_id'], $today]);

    if ($stmt->fetchColumn() == 0) {
        $reward = 1.00; // 1 BDT for daily check-in
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$reward, $_SESSION['user_id']]);
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'reward', ?, 'Daily Check-in')");
        $stmt->execute([$_SESSION['user_id'], $reward]);
        $pdo->commit();
        $success = "Daily reward of ৳$reward claimed!";
        $user['balance'] += $reward;
    } else {
        $error = "Daily reward already claimed today.";
    }
}
?>

<div class="row g-3">
    <div class="col-12">
        <div class="card shadow-sm border-warning">
            <div class="card-body text-center">
                <i class="fas fa-calendar-check fa-3x text-warning mb-3"></i>
                <h5 class="card-title">Daily Check-in</h5>
                <p class="text-muted">Claim your daily reward of ৳1.00</p>
                <?php if (isset($success)): ?>
                    <div class="text-success mb-2"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="text-danger mb-2"><?php echo $error; ?></div>
                <?php endif; ?>
                <a href="rewards.php?claim_daily=1" class="btn btn-warning w-100">Claim Now</a>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm border-primary">
            <div class="card-body text-center">
                <i class="fas fa-play-circle fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Watch Ads</h5>
                <p class="text-muted">Earn rewards by watching short video ads.</p>
                <a href="ads.php" class="btn btn-primary w-100">Watch & Earn</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
