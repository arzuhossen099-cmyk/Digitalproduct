<?php
$title = "Revenue Reports";
$active_page = "payments";
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->query("SELECT p.*, u.username FROM payments p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
$payments = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-accent">Payment History</h2>
</div>

<div class="lp-table-container">
    <table class="lp-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>User</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Transaction ID</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $p): ?>
            <tr>
                <td class="small text-muted"><?php echo date('M d, Y H:i', strtotime($p['created_at'])); ?></td>
                <td class="fw-bold"><?php echo h($p['username']); ?></td>
                <td class="text-accent"><?php echo format_currency($p['amount']); ?></td>
                <td><?php echo h($p['payment_method']); ?></td>
                <td><code class="text-info"><?php echo h($p['transaction_id']); ?></code></td>
                <td>
                    <span class="lp-badge lp-badge-<?php echo $p['status'] === 'completed' ? 'success' : 'warning'; ?>">
                        <?php echo ucfirst($p['status']); ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
