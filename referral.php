<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$referral_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/register.php?ref=" . $user['referral_code'];

$stmt = $pdo->prepare("SELECT u.username, r.created_at FROM referrals r JOIN users u ON r.referee_id = u.id WHERE r.referrer_id = ? ORDER BY r.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$referrals = $stmt->fetchAll();
?>

<div class="card shadow-sm mb-4">
    <div class="card-body text-center">
        <h5 class="card-title">Referral Program</h5>
        <p class="text-muted">Invite your friends and earn rewards when they activate a plan.</p>

        <div class="mb-3">
            <label class="form-label">Your Referral Link</label>
            <div class="input-group">
                <input type="text" class="form-control" value="<?php echo $referral_link; ?>" id="refLink" readonly>
                <button class="btn btn-outline-primary" type="button" onclick="copyRef()">Copy</button>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Referral Code</label>
            <h4 class="text-primary"><?php echo $user['referral_code']; ?></h4>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">My Referrals</h5>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($referrals)): ?>
                        <tr><td colspan="2" class="text-center">No referrals yet</td></tr>
                    <?php else:
                        foreach ($referrals as $ref):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ref['username']); ?></td>
                            <td><?php echo date('d/m/y', strtotime($ref['created_at'])); ?></td>
                        </tr>
                    <?php
                        endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function copyRef() {
  var copyText = document.getElementById("refLink");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(copyText.value);
  alert("Copied to clipboard: " + copyText.value);
}
</script>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
