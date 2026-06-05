<?php
$title = "Invoice";
require_once __DIR__ . '/includes/auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$user = get_logged_in_user();

$stmt = $pdo->prepare("SELECT p.*, pl.name as plan_name FROM payments p LEFT JOIN plans pl ON p.amount = pl.price WHERE p.id = ? AND p.user_id = ?");
$stmt->execute([$id, $user['id']]);
$invoice = $stmt->fetch();

if (!$invoice) {
    die("Invoice not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo h($invoice['transaction_id']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body class="bg-primary p-5">
    <div class="container">
        <div class="lp-card p-5 mx-auto" style="max-width: 800px;">
            <div class="d-flex justify-content-between mb-5">
                <div>
                    <h2 class="text-accent fw-bold">LeadPress</h2>
                    <p class="text-muted">Enterprise Lead Generation SaaS</p>
                </div>
                <div class="text-end">
                    <h4 class="mb-0">INVOICE</h4>
                    <p class="text-muted">#<?php echo h($invoice['transaction_id']); ?></p>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-6">
                    <h6 class="text-secondary text-uppercase small">Billed To</h6>
                    <div class="fw-bold"><?php echo h($user['full_name'] ?: $user['username']); ?></div>
                    <div class="text-muted small"><?php echo h($user['email']); ?></div>
                </div>
                <div class="col-6 text-end">
                    <h6 class="text-secondary text-uppercase small">Date Issued</h6>
                    <div class="fw-bold"><?php echo date('M d, Y', strtotime($invoice['created_at'])); ?></div>
                </div>
            </div>

            <table class="lp-table mb-5">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Subscription Plan: <?php echo h($invoice['plan_name'] ?: 'Custom Credits'); ?></td>
                        <td class="text-end fw-bold"><?php echo format_currency($invoice['amount']); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="row">
                <div class="col-6">
                    <h6 class="text-secondary text-uppercase small">Payment Method</h6>
                    <div class="text-accent fw-bold"><?php echo h($invoice['payment_method']); ?></div>
                </div>
                <div class="col-6 text-end">
                    <h4 class="text-accent fw-bold">Total: <?php echo format_currency($invoice['amount']); ?></h4>
                    <p class="text-success small fw-bold">Status: <?php echo ucfirst($invoice['status']); ?></p>
                </div>
            </div>

            <hr class="my-5 border-secondary">
            <div class="text-center text-muted small">
                Thank you for your business! If you have any questions, please contact support@leadpress.com
            </div>

            <div class="mt-4 text-center no-print">
                <button onclick="window.print()" class="btn btn-lp-primary"><i class="fas fa-print me-2"></i> Print Invoice</button>
                <a href="billing.php" class="btn btn-lp-outline ms-2">Back to Billing</a>
            </div>
        </div>
    </div>
</body>
</html>
