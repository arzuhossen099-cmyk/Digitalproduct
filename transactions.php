<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$transactions = $stmt->fetchAll();
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Transaction History</h5>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="4" class="text-center">No transactions found</td></tr>
                    <?php else:
                        foreach ($transactions as $tx):
                    ?>
                        <tr>
                            <td><small><?php echo date('d/m/y', strtotime($tx['created_at'])); ?></small></td>
                            <td><?php echo ucfirst($tx['type']); ?></td>
                            <td class="<?php echo in_array($tx['type'], ['deposit', 'reward']) ? 'text-success' : 'text-danger'; ?>">
                                <?php echo in_array($tx['type'], ['deposit', 'reward']) ? '+' : '-'; ?>৳<?php echo number_format($tx['amount'], 2); ?>
                            </td>
                            <td><span class="badge bg-success">Completed</span></td>
                        </tr>
                    <?php
                        endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Pending Deposits</h5>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM deposits WHERE user_id = ? AND status = 'pending' ORDER BY created_at DESC");
                    $stmt->execute([$_SESSION['user_id']]);
                    $pending_deposits = $stmt->fetchAll();
                    if (empty($pending_deposits)):
                    ?>
                        <tr><td colspan="3" class="text-center">No pending deposits</td></tr>
                    <?php else:
                        foreach ($pending_deposits as $d):
                    ?>
                        <tr>
                            <td><small><?php echo date('d/m/y', strtotime($d['created_at'])); ?></small></td>
                            <td>৳<?php echo number_format($d['amount'], 2); ?></td>
                            <td><span class="badge bg-warning"><?php echo ucfirst($d['status']); ?></span></td>
                        </tr>
                    <?php
                        endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
