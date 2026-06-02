<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM ads_tasks WHERE id = ? AND status = 'active'");
$stmt->execute([$id]);
$ad = $stmt->fetch();

if (!$ad) { header("Location: earn.php"); exit(); }

$success = false;
if (isset($_POST['ad_completed'])) {
    // Add logic to prevent fast refresh / multiple claims
    $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $stmt->execute([$ad['reward_amount'], $_SESSION['user_id']]);

    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'reward', ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $ad['reward_amount'], "Ad Reward: " . $ad['title']]);

    $success = true;
}
?>

<div class="card shadow-sm text-center p-5">
    <?php if ($success): ?>
        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
        <h4>Congratulations!</h4>
        <p>You have earned ৳<?php echo $ad['reward_amount']; ?></p>
        <a href="earn.php" class="btn btn-primary w-100 mt-3">Next Task</a>
    <?php else: ?>
        <h4><?php echo htmlspecialchars($ad['title']); ?></h4>
        <p class="text-muted">Wait for the timer to finish to get your reward.</p>

        <div class="h2 text-primary my-4" id="timer"><?php echo $ad['duration_seconds']; ?>s</div>

        <div class="alert alert-info">
            <i class="fas fa-external-link-alt me-1"></i> Opening Ad/Link in a new tab...
        </div>

        <form method="POST" id="complete-form" style="display:none;">
            <input type="hidden" name="ad_completed" value="1">
            <button type="submit" class="btn btn-success w-100 py-3">Claim Reward</button>
        </form>

        <script>
            let timeLeft = <?php echo $ad['duration_seconds']; ?>;
            window.open("<?php echo $ad['url']; ?>", "_blank");

            const timerInterval = setInterval(() => {
                timeLeft--;
                document.getElementById('timer').innerText = timeLeft + "s";
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('timer').style.display = 'none';
                    document.getElementById('complete-form').style.display = 'block';
                }
            }, 1000);
        </script>
    <?php endif; ?>
</div>

<?php require_once 'includes/bottom_menu.php'; require_once 'includes/footer.php'; ?>
