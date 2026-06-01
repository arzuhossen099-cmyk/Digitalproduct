<?php
require_once 'header.php';

if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $stmt = $pdo->prepare("UPDATE withdrawals SET status = 'approved' WHERE id = ? AND status = 'pending'");
    $stmt->execute([$id]);
    echo "<div class='alert alert-success'>Withdrawal marked as approved!</div>";
}

if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $stmt = $pdo->prepare("SELECT * FROM withdrawals WHERE id = ? AND status = 'pending'");
    $stmt->execute([$id]);
    $w = $stmt->fetch();

    if ($w) {
        $pdo->beginTransaction();
        // Update status
        $stmt = $pdo->prepare("UPDATE withdrawals SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);

        // Refund balance
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$w['amount'], $w['user_id']]);

        // Record refund transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'reward', ?, 'Withdrawal rejected - balance refunded')");
        $stmt->execute([$w['user_id'], $w['amount']]);

        $pdo->commit();
        echo "<div class='alert alert-danger'>Withdrawal rejected and balance refunded!</div>";
    }
}

$stmt = $pdo->query("SELECT w.*, u.username FROM withdrawals w JOIN users u ON w.user_id = u.id ORDER BY w.created_at DESC");
$withdrawals = $stmt->fetchAll();
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Manage Withdrawals</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Account</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($withdrawals as $w): ?>
                    <tr>
                        <td><small><?php echo date('d/m/y H:i', strtotime($w['created_at'])); ?></small></td>
                        <td><?php echo htmlspecialchars($w['username']); ?></td>
                        <td>৳<?php echo number_format($w['amount'], 2); ?></td>
                        <td><?php echo ucfirst($w['method']); ?></td>
                        <td><?php echo htmlspecialchars($w['account_number']); ?></td>
                        <td>
                            <span class="badge bg-<?php
                                echo $w['status'] == 'pending' ? 'warning' : ($w['status'] == 'approved' ? 'success' : 'danger');
                            ?>"><?php echo ucfirst($w['status']); ?></span>
                        </td>
                        <td>
                            <?php if ($w['status'] == 'pending'): ?>
                                <a href="withdrawals.php?approve=<?php echo $w['id']; ?>" class="btn btn-sm btn-success">Approve</a>
                                <a href="withdrawals.php?reject=<?php echo $w['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject this withdrawal?')">Reject</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
