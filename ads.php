<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$success = '';

if (isset($_POST['ad_watched'])) {
    $reward = 0.50; // 0.50 BDT per ad
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $stmt->execute([$reward, $_SESSION['user_id']]);
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'reward', ?, 'Ad Reward')");
    $stmt->execute([$_SESSION['user_id'], $reward]);
    $pdo->commit();
    $success = "You earned ৳$reward for watching the ad!";
    $user['balance'] += $reward;
}
?>

<div class="card shadow-sm mb-4">
    <div class="card-body text-center">
        <h5 class="card-title">Watch & Earn</h5>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div id="ad-container" class="bg-dark text-white p-5 rounded mb-3" style="height: 200px; display: flex; align-items: center; justify-content: center;">
            <div id="ad-timer">Watch this ad for 10 seconds...</div>
            <div id="ad-content" style="display:none;">
                <h4>Premium Offer!</h4>
                <p>Buy 1 Get 1 Free on all data packs.</p>
            </div>
        </div>

        <form id="reward-form" method="POST" style="display:none;">
            <input type="hidden" name="ad_watched" value="1">
            <button type="submit" class="btn btn-success w-100">Claim Reward</button>
        </form>

        <p id="waiting-msg">Please wait...</p>
    </div>
</div>

<script>
let seconds = 10;
const timerInterval = setInterval(() => {
    seconds--;
    document.getElementById('ad-timer').innerText = `Watch this ad for ${seconds} seconds...`;
    if (seconds <= 0) {
        clearInterval(timerInterval);
        document.getElementById('ad-timer').style.display = 'none';
        document.getElementById('ad-content').style.display = 'block';
        document.getElementById('reward-form').style.display = 'block';
        document.getElementById('waiting-msg').style.display = 'none';
    }
}, 1000);
</script>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
