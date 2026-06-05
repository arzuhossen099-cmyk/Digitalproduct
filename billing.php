<?php
$title = "Billing & Invoices";
require_once __DIR__ . '/includes/header_user.php';

$user = get_logged_in_user();

$stmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$payments = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Billing History</h2>
    <div class="stat-card p-2 px-3 d-flex align-items-center bg-primary bg-opacity-10 border-primary">
        <i class="fas fa-credit-card me-2 text-primary"></i>
        <span class="small fw-bold">Active Plan: <?php echo h($subscription['plan_name'] ?? 'None'); ?></span>
    </div>
</div>

<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-dark table-hover border-secondary">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction ID</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?php echo date('M d, Y', strtotime($payment['created_at'])); ?></td>
                    <td><code class="text-info"><?php echo h($payment['transaction_id']); ?></code></td>
                    <td><?php echo h($payment['payment_method']); ?></td>
                    <td><?php echo format_currency($payment['amount']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $payment['status'] === 'completed' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($payment['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($payment['status'] === 'completed'): ?>
                            <a href="invoice.php?id=<?php echo $payment['id']; ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-file-invoice me-1"></i> Invoice</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($payments)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No billing history found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
