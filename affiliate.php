<?php
$title = "Affiliate Program";
require_once __DIR__ . '/includes/header_user.php';

$user = get_logged_in_user();

// Check if user is already an affiliate
$stmt = $pdo->prepare("SELECT * FROM affiliates WHERE user_id = ?");
$stmt->execute([$user['id']]);
$affiliate = $stmt->fetch();

if (!$affiliate) {
    // Auto-enroll if not enrolled
    $aff_id = 'LP' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
    $stmt = $pdo->prepare("INSERT INTO affiliates (user_id, affiliate_id) VALUES (?, ?)");
    $stmt->execute([$user['id'], $aff_id]);

    $stmt = $pdo->prepare("SELECT * FROM affiliates WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $affiliate = $stmt->fetch();
}

$referral_link = "http://" . $_SERVER['HTTP_HOST'] . "/register.php?ref=" . $affiliate['affiliate_id'];

$stmt = $pdo->prepare("SELECT * FROM commissions WHERE affiliate_user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$commissions = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Affiliate Dashboard</h2>
    <div class="stat-card p-2 px-3 bg-success bg-opacity-10 border-success">
        <span class="small fw-bold text-success">Total Earnings: <?php echo format_currency($affiliate['total_earnings']); ?></span>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="stat-card">
            <h5 class="fw-bold mb-3">Your Referral Link</h5>
            <div class="input-group">
                <input type="text" class="form-control bg-dark text-info border-secondary" value="<?php echo $referral_link; ?>" readonly id="refLink">
                <button class="btn btn-outline-info" onclick="copyRef()">Copy</button>
            </div>
            <p class="text-muted small mt-2">Share this link and earn 10% commission on every subscription purchase.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card h-100 d-flex flex-column justify-content-center text-center">
            <div class="text-muted small">Total Clicks</div>
            <h3 class="fw-bold"><?php echo number_format($affiliate['total_clicks']); ?></h3>
            <div class="text-muted small mt-2">Signups: <?php echo number_format($affiliate['total_signups']); ?></div>
        </div>
    </div>
</div>

<div class="stat-card">
    <h5 class="fw-bold mb-4">Commission History</h5>
    <div class="table-responsive">
        <table class="table table-dark table-hover border-secondary">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Referral</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commissions as $comm): ?>
                <tr>
                    <td><?php echo date('M d, Y', strtotime($comm['created_at'])); ?></td>
                    <td>User ID: <?php echo h($comm['referred_user_id']); ?></td>
                    <td><?php echo format_currency($comm['amount']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $comm['status'] === 'paid' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($comm['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($commissions)): ?>
                    <tr><td colspan="4" class="text-center text-muted">No commissions earned yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function copyRef() {
    var copyText = document.getElementById("refLink");
    copyText.select();
    document.execCommand("copy");
    alert("Referral link copied!");
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
