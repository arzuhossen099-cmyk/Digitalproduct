<?php
require_once 'header.php';

if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $stmt = $pdo->prepare("SELECT * FROM deposits WHERE id = ? AND status = 'pending'");
    $stmt->execute([$id]);
    $deposit = $stmt->fetch();

    if ($deposit) {
        $pdo->beginTransaction();
        // Update deposit status
        $stmt = $pdo->prepare("UPDATE deposits SET status = 'approved' WHERE id = ?");
        $stmt->execute([$id]);

        // Add balance to user
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$deposit['amount'], $deposit['user_id']]);

        // Record transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'deposit', ?, ?)");
        $stmt->execute([$deposit['user_id'], $deposit['amount'], "Deposit approved: " . $deposit['transaction_id']]);

        // Notify user
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->execute([$deposit['user_id'], "Your deposit of ৳" . $deposit['amount'] . " has been approved!"]);

        $pdo->commit();
        echo "<div class='alert alert-success'>Deposit approved!</div>";
    }
}

if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $stmt = $pdo->prepare("UPDATE deposits SET status = 'rejected' WHERE id = ? AND status = 'pending'");
    $stmt->execute([$id]);
    echo "<div class='alert alert-danger'>Deposit rejected!</div>";
}

$stmt = $pdo->query("SELECT d.*, u.username FROM deposits d JOIN users u ON d.user_id = u.id ORDER BY d.created_at DESC");
$deposits = $stmt->fetchAll();
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Manage Deposits</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Sender</th>
                        <th>Method</th>
                        <th>TrxID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deposits as $d): ?>
                    <tr>
                        <td><small><?php echo date('d/m/y H:i', strtotime($d['created_at'])); ?></small></td>
                        <td><?php echo htmlspecialchars($d['username'] ?? ''); ?></td>
                        <td>৳<?php echo number_format($d['amount'] ?? 0, 2); ?></td>
                        <td><?php echo htmlspecialchars($d['sender_number'] ?? ''); ?></td>
                        <td><?php echo ucfirst($d['method'] ?? ''); ?></td>
                        <td><code><?php echo htmlspecialchars($d['transaction_id'] ?? ''); ?></code></td>
                        <td>
                            <span class="badge bg-<?php
                                echo $d['status'] == 'pending' ? 'warning' : ($d['status'] == 'approved' ? 'success' : 'danger');
                            ?>"><?php echo ucfirst($d['status']); ?></span>
                        </td>
                        <td>
                            <?php if ($d['status'] == 'pending'): ?>
                                <a href="deposits.php?approve=<?php echo $d['id']; ?>" class="btn btn-sm btn-success">Approve</a>
                                <a href="deposits.php?reject=<?php echo $d['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject this deposit?')">Reject</a>
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
