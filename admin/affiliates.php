<?php
$title = "Affiliate Management";
$active_page = "affiliates";
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->query("SELECT a.*, u.username FROM affiliates a JOIN users u ON a.user_id = u.id ORDER BY a.total_earnings DESC");
$affiliates = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-accent">Affiliate Overview</h2>
</div>

<div class="lp-table-container">
    <table class="lp-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Affiliate ID</th>
                <th>Clicks</th>
                <th>Signups</th>
                <th>Earnings</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($affiliates as $a): ?>
            <tr>
                <td class="fw-bold"><?php echo h($a['username']); ?></td>
                <td><code class="text-info"><?php echo h($a['affiliate_id']); ?></code></td>
                <td><?php echo number_format($a['total_clicks']); ?></td>
                <td><?php echo number_format($a['total_signups']); ?></td>
                <td class="text-success fw-bold"><?php echo format_currency($a['total_earnings']); ?></td>
                <td class="small text-muted"><?php echo date('M d, Y', strtotime($a['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
