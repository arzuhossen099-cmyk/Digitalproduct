<?php
$title = "Coupon Management";
$active_page = "coupons";
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->query("SELECT * FROM coupons ORDER BY created_at DESC");
$coupons = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-accent">Promo Codes</h2>
    <button class="btn btn-lp-primary btn-sm"><i class="fas fa-plus me-2"></i> Create Coupon</button>
</div>

<div class="lp-table-container">
    <table class="lp-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Discount</th>
                <th>Used</th>
                <th>Limit</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($coupons as $c): ?>
            <tr>
                <td><code class="text-info"><?php echo h($c['code']); ?></code></td>
                <td><?php echo $c['discount_type'] === 'percentage' ? $c['discount_value'] . '%' : format_currency($c['discount_value']); ?></td>
                <td><?php echo $c['used_count']; ?></td>
                <td><?php echo $c['usage_limit'] ?: 'Unlimited'; ?></td>
                <td><span class="lp-badge lp-badge-<?php echo $c['is_active'] ? 'success' : 'danger'; ?>"><?php echo $c['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
                <td>
                    <button class="btn btn-sm btn-lp-outline px-2 py-0"><i class="fas fa-edit small"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
