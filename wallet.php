<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';
?>

<div class="card shadow-sm text-center mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-6 border-end">
                <h6 class="text-muted small">Main Balance</h6>
                <h3 class="text-primary">৳<?php echo number_format($user['balance'] ?? 0, 2); ?></h3>
            </div>
            <div class="col-6">
                <h6 class="text-muted small">Commission Balance</h6>
                <h3 class="text-success">৳<?php echo number_format($user['commission_balance'] ?? 0, 2); ?></h3>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col">
                <a href="deposit.php" class="btn btn-outline-success w-100">Deposit</a>
            </div>
            <div class="col">
                <a href="withdraw.php" class="btn btn-outline-danger w-100">Withdraw</a>
            </div>
        </div>
    </div>
</div>

<h5 class="mb-3">Recent Transactions</h5>
<div class="list-group">
    <?php
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$_SESSION['user_id']]);
    $txs = $stmt->fetchAll();
    if (empty($txs)):
    ?>
        <div class="list-group-item text-center text-muted">No transactions yet</div>
    <?php else:
        foreach ($txs as $tx):
    ?>
        <div class="list-group-item">
            <div class="d-flex justify-content-between">
                <span class="fw-bold"><?php echo ucfirst($tx['type']); ?></span>
                <span class="<?php echo in_array($tx['type'], ['deposit', 'reward', 'commission']) ? 'text-success' : 'text-danger'; ?>">
                    <?php echo in_array($tx['type'], ['deposit', 'reward', 'commission']) ? '+' : '-'; ?>৳<?php echo number_format($tx['amount'], 2); ?>
                </span>
            </div>
            <small class="text-muted"><?php echo date('d M, h:i A', strtotime($tx['created_at'])); ?></small>
        </div>
    <?php
        endforeach;
    endif; ?>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
