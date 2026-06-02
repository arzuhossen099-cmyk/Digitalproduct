<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

// Level 1 Referrals
$stmt = $pdo->prepare("SELECT id, username, created_at FROM users WHERE referred_by = ?");
$stmt->execute([$_SESSION['user_id']]);
$l1_referrals = $stmt->fetchAll();

// Level 2 Referrals
$l2_referrals = [];
if (!empty($l1_referrals)) {
    $l1_ids = array_column($l1_referrals, 'id');
    $placeholders = implode(',', array_fill(0, count($l1_ids), '?'));
    $stmt = $pdo->prepare("SELECT id, username, referred_by, created_at FROM users WHERE referred_by IN ($placeholders)");
    $stmt->execute($l1_ids);
    $l2_referrals = $stmt->fetchAll();
}

// Total Commissions
$stmt = $pdo->prepare("SELECT SUM(amount) FROM referral_commissions WHERE referrer_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_commissions = $stmt->fetchColumn() ?: 0;

$referral_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/register.php?ref=" . $user['referral_code'];
?>

<div class="card shadow-sm mb-4 bg-primary text-white">
    <div class="card-body text-center">
        <h5 class="card-title">Referral Program (2-Tier)</h5>
        <h2 class="mb-0">৳<?php echo number_format($total_commissions, 2); ?></h2>
        <small>Total Commissions Earned</small>

        <div class="mt-3">
            <label class="form-label small">Your Referral Link</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" value="<?php echo $referral_link; ?>" id="refLink" readonly>
                <button class="btn btn-light" type="button" onclick="copyRef()">Copy</button>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6">
        <div class="card text-center p-3 shadow-sm border-0">
            <h4 class="mb-0"><?php echo count($l1_referrals); ?></h4>
            <small class="text-muted">Direct (L1)</small>
        </div>
    </div>
    <div class="col-6">
        <div class="card text-center p-3 shadow-sm border-0">
            <h4 class="mb-0"><?php echo count($l2_referrals); ?></h4>
            <small class="text-muted">Indirect (L2)</small>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <div class="list-group-item bg-light fw-bold">Recent Level 1 Invites</div>
            <?php if (empty($l1_referrals)): ?>
                <div class="list-group-item text-center text-muted py-4">No invites yet</div>
            <?php else: ?>
                <?php foreach (array_slice($l1_referrals, 0, 5) as $r): ?>
                    <div class="list-group-item d-flex justify-content-between">
                        <span><?php echo htmlspecialchars($r['username']); ?></span>
                        <small class="text-muted"><?php echo date('d/m/y', strtotime($r['created_at'])); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function copyRef() {
  var copyText = document.getElementById("refLink");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(copyText.value);
  alert("Link Copied!");
}
</script>

<?php require_once 'includes/bottom_menu.php'; require_once 'includes/footer.php'; ?>
